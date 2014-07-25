=== Spam Destroyer ===
Contributors: ryanhellyer, bjornjohansen, dimadin, brianlayman, forsite
Donate link: http://geek.ryanhellyer.net/products/spam-destroyer/
Tags: spam, anti-spam, antispam, buddypress, bbpress, kill, destroy, eliminate
Requires at least: 3.9
Stable tag: 1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Kills spam dead in it's tracks. Be gone evil demon spam!

== Description ==

Stops automated spam while remaining as unobtrusive as possible to regular commenters. <a href="http://geek.ryanhellyer.net/products/spam-destroyer/">The Spam Destroyer plugin</a> is intended to be effortless to use, simply install and enjoy a spam free website :)

<small>This plugin does not currently work very well for user registrations, bbPress or BuddyPress. We are looking for someone to assist with ensuring this works well in future, so if you would like to help you then please <a href="http://geek.ryanhellyer.net/contact/">get in touch</a>.</small>

== Installation ==

Simply install and activate the plugin. No settings necessary.

For more information, visit the <a href="http://geek.ryanhellyer.net/products/spam-destroyer/">Spam Destroyer plugin page</a>.

<small>Note: Spam Destroyer apparently does not work in conjunction with the Jetpack plugin. That plugin has a nasty way of handling it's commenting system which requires you to connect to an external service and will not work if your installation is not connected to the internet (which is the case for all my development sites) and as such this problem will not be fixed any time soon. If the Jetpack team fix these major problems I will happily make the plugin compatible with it, but in the mean time I suggest avoiding the Jetpack plugin.</small>


== Frequently Asked Questions ==

Check out the FAQ on the <a href="http://geek.ryanhellyer.net/products/spam-destroyer/">Spam Destroyer plugin</a> page.


== Changelog ==

1.8
= Addition of black-list for higher spam settings
= Implemented time-limit for answering CAPTCHA questions
= Moved to a modular system for handling extra protective levels
= Addition of notices in back-end to describe what checks the comment passed

1.7.1
= Removal of spam cleanout since redundant after addition of CAPTCHA fallback

1.7
= Addition of text image CAPTCHA
= Modification of key setup - two way encryption
= Low, key never changes; medium, key changes with nonce

1.6
= Addition of math CAPTCHA

1.5
= Addition of crude API for handling CAPTCHA's

= 1.4.1 (19/4/2014) =
* Cleaning up PHPDocs
= 1.4 (21/3/2014) =
* Now automatically deletes spam comments older than a set time (5 days)
* Removed kill.php from trunk
* Updated readme file for current WordPress Release 
= 1.3.2 (24/6/2013) =
* Fixed short cookie time bug thanks to Milan Dinić
= 1.3.1 (18/3/2013) =
* Fixed bug which prevented user registration
* Thanks to Marte Sollund and Ingvild Evje of <a href="http://nettsett.no/">Nettsett</a> for an excellent bug report
= 1.3 (6/3/2013) =
* Instantiated class to variable to allow for remove hooks and filters when necessary
* Added redirect after spam comment detected
* Added error notice on redirection due to spam comment detection
= 1.2.5 (19/8/2012) =
* Changed from kill.php file to kill.js file
* Allows for caching of payload
* Allows for automatic script concatentation
* Cookie creation achieved via raw JS
* Key is passed to script via wp_localize_script()
= 1.2.4 (11/8/2012) =
* Re-removed requirement for jQuery
* Added try / catch to JS to ensure it doesn't fail
* Moved JS enqueue to form field area so that it only loads when needed
* Added Bjørn Johansen to the contributor list
* Added correct mime-type to JS file
= 1.2.3 (9/8/2012) =
* Added requirement for jQuery due to bug with code introduced in 1.2.2
= 1.2.2 (9/8/2012) =
* Removed need for jQuery
= 1.2.1 (9/8/2012) =
* Moved script to footer on advice of Ronald Huereca and Bjørn Johansen
* Fixed potential security flaw in kill.php
= 1.2 (5/8/2012) =
* Fixed multisite and BuddyPress bugs
* Added support for bbPress registrations
* Added support for bbPress guest posting protection
* Removed the "bad word" list
= 1.1 (5/8/2012) =
* Added support for BuddyPress signup page
* Added support for WordPress multisite signup page
= 1.0.3 (30/7/2012) =
* Upgrade to documentation
= 1.0.2 (30/7/2012) =
* Changed name to 'spam-destroyer'
= 1.0.1 (30/7/2012) =
* Cleaned up some legacy code from older implementations
= 1.0 (29/7/2012) =
* Initial release

<small>Any beta/alpha versions to be released in future, will be posted for download on the <a href="http://geek.ryanhellyer.net/products/spam-destroyer/">Spam Destroyer plugin page</a>.</small>


== Credits ==

* <a href="http://ocaoimh.ie/">Donncha O Caoimh</a> - Developer of Cookies for Comments, functionality of which is incorporated into Spam Destroyer<br />
* <a href="http://elliottback.com/">Elliot Back</a> - Developer of WP Hashcash, functionality of which is incorporated into Spam Destroyer<br />
* <a href="Marte Sollund and Ingvild Evje</a> - Bug reporting<br />
* <a href="http://eHermitsInc.com/">Brian Layman</a> - Code advice<br />
* <a href="http://ronalfy.com/">Ronald Huereca</a> - JS advice<br />
* <a href="https://twitter.com/shawngaffney">Shawn Gaffney</a> - Bug reporting<br />
* <a href="http://konstruktors.com/">Kaspars Dambis</a> - Bug reporting<br />
* <a href="http://www.wanderingjon.com/">Jon Brown</a> - Added error message filter</br />