import sys

if __name__ == '__main__':
    input_file = open(sys.argv[1], 'r')

    s = 0.0
    c = 0
    while True:
        line = input_file.readline()
        if line == '':
            break

        s += float(line)
        c += 1

    print "sum =", s
    print "avg =", (s/c)
