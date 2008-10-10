=== Odiogo Listen Button ===
Contributors: patricek
Donate link: http://www.odiogo.com/
Tags: podcast, mp3, text-to-speech, rss, feeds, audio, accessibility
Requires at least: 2.0.5
Tested up to: 2.6.0
Stable tag: trunk

The 'Odiogo Listen Button' allows you to offer audio versions of your posts on-screen and to millions of iPod/MP3/mobile phone owners ... for free!

== Description ==

This plugin displays a 'listen' button underneath the title of each post. When the button is clicked, the post's 
text is shifted a few lines down and a control bar is displayed from which the MP3 file can be played/paused. 
The '+Odiogo' button underneath the player provides the user with many options such as subscribing to the
podcast version of the blog, streaming the MP3 files, listening to older posts MP3 files, etc.

There is also a sidebar 'Subscribe' button widget you can activate through the Sidbar Widgets Menu. Look for the 'Odiogo Subscribe Button' widget.

Bloggers need to [register](http://www.odiogo.com/sign_up.php "Odiogo sign-up") (for free) their blog's RSS feed with Odiogo. Once in the Odiogo ecosystem, the feed 
will be monitored on a regular basis and MP3 files will be automatically generated with the addition of new posts 
to the blog. MP3 files are stored in the Odiogo environment. The Odiogo service is fully transparent and does not consume any bandwidth or disk space on the blog server system.

The system is advertising based and is free for both bloggers and end users. When listenership reaches a significant level, a percentage of the revenue generated
from advertising is redistributed to the blogger.


== Installation ==

1. Download the [plugin](http://www.odiogo.com/download/wordpress/plugin/odiogo_listen_button_latest.php) and unzip it into a temporary directory
2. Copy the directory `odiogo_listen_button` into your `/wp-content/plugins/` directory via local file copy, FTP, scp or similar
3. Login to your WordPress admin account, click menu 'Plugins' and click 'Activate' for 'Odiogo Listen Button'
4. Click menu 'Settings' > 'Odiogo Listen Button'
5. Enter your 'Odiogo Feed ID' and click 'Save'
6. Click menu 'Design' > 'Widgets' (or 'Sidebar Widgets' depending on your WP version)
7. Drag and drop 'Odiogo Subscribe Button' from 'Available Widgets' to 'Sidebar'
8. Click 'Save Changes'

If your theme doesn't support widgets:
1. Click menu 'Presentation' > 'Theme Editor'
2. Select 'Sidebar' to edit your theme's sidebar.php
3. Where desired, insert the following template tag: <?php odiogo_subscribe_button() ?> 

= Upgrade Instructions from version 1.x? =

1. Download the [plugin](http://www.odiogo.com/download/wordpress/plugin/odiogo_listen_button_latest.php) and unzip it into a temporary directory
2. Overwrite the directory `odiogo_listen_button` into your `/wp-content/plugins/` directory via local file copy, FTP, scp or similar
3. Login to your WordPress admin account, click menu 'Plugins' and make sure the 'Odiogo Listen Button' is Active
4. Click menu 'Settings' > 'Odiogo Listen Button' and click 'Save'

== Frequently Asked Questions ==

= What is Odiogo? =

Odiogo enables you to convert your blog posts into high quality audio files. Check out the [demo](http://www.odiogo.com/demo.php).

= What do I need to do to Odiogo-enable my content? =
The procedure is extremely simple and should not take more than a few hours. You just need to fill out this 
[form](http://www.odiogo.com/sign_up.php). An activation will be sent to you shortly after sign-up.
Once your feed is enabled by Odiogo, you just need to promote the service on your home page and activate the Odiogo Listen Button plugin. 

= How do I sign up? =

Sign up is free and happens [here](http://www.odiogo.com/sign_up.php).

= How do I get my Odiogo Feed ID? =

The Feed ID will be communicated upon sign-up. 

= What are the files produced by Odiogo for my feed? =

Odiogo generates the following files:

* A new RSS feed enriched with the Odiogo produced MP3 files. This is the feed your end-users would include in their podcast software such as iTunes or Juice.
* An M3U file used to stream all the news item of your feed. This file can be opened with multimedia players such as Windows Media Player or Winamp.
* "light" XHTML and WML pages. These can be accessed to download and listen to the mp3 files on a mobile phone over an Internet connection.
* An HTML page that provides:
   - Buttons to automatically subscribe to the podcast feed.
   - Link to the M3U streaming file.
   - List of all articles in the feed with a link to play each of the MP3 files.

You can view an [example](http://podcasts.odiogo.com/prompt-speech-applications-weblog/podcasts-html.php) of this page.

== Screenshots ==

1. The Odiogo Listen Button
2. The Odiogo Listen Button