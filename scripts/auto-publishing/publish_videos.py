#!/usr/bin/python
#
# Copyright Calin-Andrei Burloiu, calin.burloiu@gmail.com
#
# Automatically publishes videos in P2P-Tube DB based on the video files and
# a videos info file. Parameters: videos_info_file videos_directory category_id
#
import sys
import MySQLdb
import os
import fnmatch
import subprocess
import string
import json

# cms_content table
class VideosTable:
    tableName = "videos"
    user_id = 1
    thumbs_count = 1
    default_thumb = 0
    
    directory = os.curdir

    def __init__(self, dbCur, directory, name, title, description, tags, category_id):
        self.dbCur = dbCur
        self.directory = directory

        self.name = name
        self.title = title
        self.description = description
        self.duration, self.formats = self.findVideosMeta()
        self.formats_json = json.dumps(self.formats, separators=(',', ':'))
        self.category_id = category_id
        
        tagList = tags.split(',')
        self.tags = {}
        for tag in tagList:
            if tag != '':
                self.tags[tag.strip()] = 0
        self.tags_json = json.dumps(self.tags, separators=(',', ':'))
        
    def getVideoResolution(self, fileName):
        pipe = subprocess.Popen('mediainfo --Inform="Video;%Width%" ' + os.path.join(self.directory, fileName), shell=True, stdout=subprocess.PIPE).stdout
        width = pipe.readline().strip()

        pipe = subprocess.Popen('mediainfo --Inform="Video;%Height%" ' + os.path.join(self.directory, fileName), shell=True, stdout=subprocess.PIPE).stdout
        height = pipe.readline().strip()

        return width + 'x' + height

    def getVideoDar(self, fileName):
        pipe = subprocess.Popen('mediainfo --Inform="Video;%DisplayAspectRatio/String%" ' + os.path.join(self.directory, fileName), shell=True, stdout=subprocess.PIPE).stdout
        dar = pipe.readline().strip()

        return dar

    def getVideoDuration(self, fileName):
        pipe = subprocess.Popen('mediainfo --Inform="General;%Duration/String3%" ' + os.path.join(self.directory, fileName), shell=True, stdout=subprocess.PIPE).stdout
        output = pipe.readline().strip()
        dotPos = output.find('.')
        if output[0:2] == '00':
            duration = output[3:dotPos]
        else:
            duration = output[:dotPos]

        return duration
        

    # Returns a pair with duration and formats list.
    def findVideosMeta(self):
        files = [f for f in os.listdir(self.directory) if os.path.isfile(os.path.join(self.directory, f))]
        files = fnmatch.filter(files, self.name + "_*")

        # Duration not set
        duration = None

        # Formats list
        formats = []
        for f in files:
            if f.find('.tstream') == -1:
                # Duration (set once)
                if duration == None:
                    duration = self.getVideoDuration(f)

                format_ = {}
                format_['res'] = self.getVideoResolution(f)
                format_['dar'] = self.getVideoDar(f)
                format_['ext'] = f[(f.rfind('.')+1):]

                fileDef = f[(f.rfind('_')+1):f.rfind('.')]
                videoDef = format_['res'].split('x')[1] + 'p'
                if fileDef != videoDef:
                    raise VideoDefException(f)

                formats.append(format_)

        return (duration, formats)

    def insert(self):
        #if self.duration == None or self.formats_json == None or self.tags_json == None:
        query = "INSERT INTO `" + self.tableName + "` (name, title, description, duration, formats, category_id, user_id, tags, date, thumbs_count, default_thumb) VALUES ('" + self.name + "', '" + self.title + "', '" + self.description + "', '" + self.duration + "', '" + self.formats_json + "', " + str(self.category_id) + ", " + str(self.user_id) + ", '" + self.tags_json + "', NOW(), " + str(self.thumbs_count) + ", " + str(self.default_thumb) + ")"
        self.dbCur.execute(query)    
    
    @staticmethod
    def getAllNames(dbCur, category_id):
        allNames = set()
        query = "SELECT name FROM `" + VideosTable.tableName + "` WHERE category_id = " + str(category_id)
        dbCur.execute(query)

        while(True):
            row = dbCur.fetchone()
            if row == None:
                break
            allNames.add(row[0])

        return allNames


class VideoDefException(Exception):
    def __init__(self, value):
        self.value = 'Invalid video definition in file name "' + value + '"! '

    def __str__(self):
        return repr(self.value)


def main():
    # Check arguments.
    if len(sys.argv) < 3:
        sys.stdout.write('usage: ' + sys.argv[0] + ' videos_info_file videos_dir category_id\n')
        exit(1)

    # Command line arguments
    fileName = sys.argv[1]
    directory = sys.argv[2]
    category_id = int(sys.argv[3])
    if len(sys.argv) == 4:
        thumbsDir = sys.argv[3]
    else:
        thumbsDir = None

    # Connect to DB
    dbConn = MySQLdb.connect(host = 'koala.cs.pub.ro', user = 'koala_p2pnext',
            passwd = '', db = '')
    dbCur = dbConn.cursor()

    allNames = VideosTable.getAllNames(dbCur, category_id)

    # Open info file
    file = open(fileName, 'r')

    # Read videos info file
    i = 1
    name = file.readline()
    while name != '':
        name = name.strip()
        title = file.readline().strip()
        description = file.readline().strip()
        tags = file.readline().strip()
        
        if not name in allNames:
            sys.stdout.write(str(i) + '. ' + name + '\r')
            try:
                video = VideosTable(dbCur, directory, name, title, description, tags, category_id)
                video.insert()
                i = i+1

            except VideoDefException as e:
                sys.stdout.write('\n' + e.value + '\n')

        name = file.readline()

    # Clean-up
    dbCur.close()
    dbConn.close()
    sys.stdout.write('\n')

    return 0


if __name__ == "__main__":
    sys.exit(main())
