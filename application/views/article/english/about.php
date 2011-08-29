<h1>P2P-Next</h1>
<p><a title="P2P-Next" href="http://www.p2p-next.org/">P2P-Next</a> is an integrated FP7 EU project involving more than 20 partners, including <a title="University Politehnica of Bucharest" href="http://www.upb.ro" ref="nofollow">University Politehnica of Bucharest</a> (UPB).</p>
<p>P2P-Next aims to build the next generation Peer-to-Peer (P2P) content delivery platform.</p>
<p>This site provides information on UPB's contribution to the P2P-Next project activities. We are currently part of WP4 (Work Package 4 - IPvNext Networking Fabric) and WP8 (Work Package 8 - Living Lab Trials).</p>

<h1>NextShare Video Plugins</h1>
<dl>
	<dt><a href="http://www.tribler.org/trac/wiki/BrowserPlugin" target="_blank">NextSharePC</a></dt>
	<dd>a media-player browser plugin which uses <a href="http://www.videolan.org/vlc/" target="_blank" ref="nofollow">VLC</a> libraries for video rendering and incorporates P2P technology for VideoOnDemand (VoD) and LiveStreaming content delivery. The plugin is currently working with Internet Explorer and Firefox on Windows.</dd>
</dl>
<dl>
	<dt><a href="http://www.tribler.org/trac/wiki/SwarmPlayer" target="_blank">SwarmPlayer</a></dt>
	<dd>a browser plugin which uses the new HTML5 rendering and incorporates P2P technology for VideoOnDemand (VoD) and LiveStreaming content delivery</dd>
</dl>

<table>
	<tr>
		<th></th>
		<th>Video Rendering</th>
		<th>Operating Systems</th>
		<th>Web Browsers</th>
	</tr>
	<tr>
		<th>
			<a href="http://www.tribler.org/trac/wiki/BrowserPlugin" target="_blank">NextSharePC</a>
		</th>
		<td>
			<a href="http://www.videolan.org/vlc/" target="_blank" ref="nofollow"><img src="<?php echo site_url('img/vlc-icon.png') ?>" alt="win" /> VLC</a>
		</td>
		<td>
			<p><img src="<?php echo site_url('img/windows-icon.png') ?>" alt="win" /> Windows</p>
			<p><img src="<?php echo site_url('img/macosx-icon.png') ?>" alt="win" /> Mac OS X</p>
		</td>
		<td>
			<p><img src="<?php echo site_url('img/firefox-icon.png') ?>" alt="win" /> <a href="http://www.mozilla.com/" target="_blank" ref="nofollow">Mozilla Firefox</a> 3.5 or greater</p>
			<p><img src="<?php echo site_url('img/ie-icon.png') ?>" alt="win" /> <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home" target="_blank" ref="nofollow">Internet Explorer</a> 7.0 or greater</p>
		</td>
	</tr>
	<tr>
		<th>
			<a href="http://www.tribler.org/trac/wiki/SwarmPlayer" target="_blank">SwarmPlayer</a>
		</th>
		<td>
			<a href="http://www.w3.org/TR/html5/" target="_blank" ref="nofollow"><img src="<?php echo site_url('img/html5-icon.png') ?>" alt="win" /> HTML5</a>
		</td>
		<td>
			<p><img src="<?php echo site_url('img/windows-icon.png') ?>" alt="win" /> Windows</p>
			<p><img src="<?php echo site_url('img/linux-icon.png') ?>" alt="win" /> Linux</p>
			<p><img src="<?php echo site_url('img/macosx-icon.png') ?>" alt="win" /> Mac OS X</p>
		</td>
		<td>
			<p><img src="<?php echo site_url('img/firefox-icon.png') ?>" alt="win" /> <a href="http://www.mozilla.com/" target="_blank" ref="nofollow">Mozilla Firefox</a> 3.5 or greater</p>
			<p><img src="<?php echo site_url('img/ie-icon.png') ?>" alt="win" /> <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home" target="_blank" ref="nofollow">Internet Explorer</a> 7.0 or greater</p>
		</td>
	</tr>
</table>

<h1>Video Assets</h1>
<p>To take a look at the NextShare plugins in action, all you have to do is <a href="<?php echo site_url('install-plugins'); ?>">install them</a> and then watch any video asset from this site.</p>
<p>Through the use of P2P technology, you will be able to stream movies from various categories:</p>
<ul>
	<li>from <a href="<?php site_url('catalog/category/1') ?>">feature films</a></li>
	<li>from <a href="<?php echo site_url('catalog/category/2') ?>"><em>TechTalks</em> technical presentations</a></li>
	<li>or from <a href="<?php site_url('catalog/category/3') ?>">various events</a> from our faculty</li>
	<li>from <a href="<?php site_url('catalog/category/4') ?>">karaoke parties</a> in <a href="http://acs.pub.ro/" target="_blank" ref="nofollow">Automatic Control and Computers Faculty</a></li>
</ul>
<p>All available movies are currently seeded by 5 peers with high bandwidth (1Gbit) kindly provided by the<a title="NCIT-Cluster" href="http://cluster.ncit.pub.ro/" target="_blank" ref="nofollow"> NCIT-Cluster</a>. Anyone that watches a movie will take part in the swarm and ensure greater availability to provided content.</p>

<h1>The Platform</h1>

<p>The P2P video distribution platform used by this site is written in <a href="http://www.php.net/">PHP</a>. It makes use of the excellent <a href="http://codeigniter.com/">CodeIgniter"</a> web application framework.</p>

<p>To install and configure the platform on your local system, please download <a href="http://p2p-next.cs.pub.ro/gitweb/?p=living-lab-site.git;a=snapshot;h=HEAD;sf=tgz">the latest tarball</a> and follow the instructions in the INSTALL file or the wiki (<strong>TODO</strong>).</p>

<p>We welcome contributions to the development and testing of this platform. If you would like to contribute, we recommend you <a href="https://p2p-next.cs.pub.ro/redmine/account/register">register on the Redmine development site</a> and ...
<ul>
	<li>... ask questions or provide answers on the <a href="https://p2p-next.cs.pub.ro/redmine/projects/site/boards">forums</a>;</li>
	<li>... add issues to the <a href="https://p2p-next.cs.pub.ro/redmine/site/issues">issue tracker</a>, signaling problems and absent features;</li>
	<li>... clone the Git repository, do code reviews and send patches;<li>
	<li>... <?php echo safe_mailto('p2p-next-contact@cs.pub.ro', 'ask us') ?> for write access to the <a href="https://p2p-next.cs.pub.ro/redmine/site/wiki">wiki</a> and update pages.</li>
</ul>
