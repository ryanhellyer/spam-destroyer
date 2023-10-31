=== Spam Destroyer ===
Contributors: ryanhellyer, bjornjohansen, dimadin, brianlayman
Donate link: https://geek.hellyer.kiwi/products/spam-destroyer/
Tags: spam, comments, anti-spam, antispam, kill, destroy, eliminate
Requires at least: 6.4
Tested up to: 6.4
Stable tag: 3.0
Text Domain: spam-destroyer
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

<em><strong>Alpha:</strong> This is an alpha product. For the stable version, please visit <a href="https://wordpress.org/plugins/spam-destroyer/">https://wordpress.org/plugins/spam-destroyer/</a>.</em>

Kills spam dead in it's tracks. Be gone evil demon spam!

== Description ==

Stops automated spam to the WordPress commenting system, while remaining as unobtrusive as possible to regular commenters. <a href="https://geek.hellyer.kiwi/products/spam-destroyer/">The Spam Destroyer plugin</a> is intended to be effortless to use. Simply install, and enjoy a spam free website :)


== Installation ==

Simply install and activate the plugin. No settings necessary.

For more information, visit the <a href="https://geek.hellyer.kiwi/products/spam-destroyer/">Spam Destroyer plugin page</a>.


== Frequently Asked Questions ==

* What types of spam does this remove?
Comment spam added via the traditional WordPress comments system

* Do you intend to add support for other types of spam?
Yes, bbPress, BuddyPress, JetPack contact form, Gravity Forms and other types of spam will be supported soon.

= Support =
If you would like to file a bug report or ask a question, please do so in the WordPress.org support forums.

= Who made the plugin? =
The original developer of the plugin was <a href="https://geek.hellyer.kiwi/">Ryan Hellyer</a>, but many others have contributed code to this project and are now listed as co-authors of the plugin.


== Changelog ==

= 3.0 alpha  (2023-10-30) =
* Major update to improve code

= 2.1.3  (2021-10-21) =
* Minor change to remove "out of date" notice on WordPress.org

= 2.1.3  (2019-04-19) =
* Minor change to remove "out of date" notice on WordPress.org

= 2.1.2  (2019-03-07) =
* Fixing debbug notice in error returns

= 2.1.1  (2018-03-18) =
* Fixing bug in admin notice GD detection

= 2.1.0  (2018-03-18) =
* Added prefix to cookie to provide less work for those trying to do cookie auditing
* Blocking comments when CAPTCHA is served but GD not enabled
* Providing notice to let the admin know that the CAPTCHA system is not working
* Providing option to disable the admin notice about CAPTCHA system not working

= 2.0.7  (2016-19-06) =
* Added checks in file to see if WordPress is loaded.
* Hooking class instantiation in later, due to taxonomies sometimes not being loaded in time.
* Updating website domain in readme.txt file.

= 2.0.6 (2015-11-26) =
* Fixing logged in user bug.

= 2.0.5 (2015-10-29) =
* Removed the plugin review class due to strange errors.

= 2.0.4 (2015-10-27) =
* Upgraded the plugin review class

= 2.0.3 (2015-10-27) =
* Upgraded the plugin review class

= 2.0.2 (2015-10-26) =
* Upgraded the plugin review class

= 2.0.1 (2015-8-3) =
* Implemented text image CAPTCHA fallback for when comment detected as spam

= 2.0 (2015-8-1) =
* Implemented text image CAPTCHA fallback for when comment detected as spam
* Improved performance via selective loading of PHP files
* Improved documentation
* Added additional links on plugins page
* Implemented time-limit for answering CAPTCHA questions
* Addition of notices in back-end to describe what checks the comment passed
* Removal of spam cleanout since redundant after addition of CAPTCHA fallback
* Fixed bug which triggered legit comments to be detected as spam due to commenting too quickly. Time limit was adjusted from five seconds to two seconds to fix this.

= 1.4.3 (2014-12-24) =
* Support for WordPress 4.1+ added.
* Added translation string specification in header.

= 1.4.2 (2014-10-17) =
* Fixed a bug in the spam checking that (I think) would have cleared the content of bbPress posts for logged in users
* Fixed various spellling errors and grammar wrongs
* Updated version compatibility

= 1.4.1 (2014-4-19) =
* Cleaning up PHPDocs

= 1.4 (2014-3-21) =
* Now automatically deletes spam comments older than a set time (5 days)
* Removed kill.php from trunk
* Updated readme file for current WordPress Release 

= 1.3.2 (2013-6-24) =
* Fixed short cookie time bug thanks to Milan Dinić

= 1.3.1 (2013-3-18) =
* Fixed bug which prevented user registration
* Thanks to Marte Sollund and Ingvild Evje of <a href="http://nettsett.no/">Nettsett</a> for an excellent bug report

= 1.3 (2013-3-6) =
* Instantiated class to variable to allow for remove hooks and filters when necessary
* Added redirect after spam comment detected
* Added error notice on redirection due to spam comment detection

= 1.2.5 (2012-8-19) =
* Changed from kill.php file to kill.js file
* Allows for caching of payload
* Allows for automatic script concatentation
* Cookie creation achieved via raw JS
* Key is passed to script via wp_localize_script()

= 1.2.4 (2012-8-11) =
* Re-removed requirement for jQuery
* Added try / catch to JS to ensure it doesn't fail
* Moved JS enqueue to form field area so that it only loads when needed
* Added Bjørn Johansen to the contributor list
* Added correct mime-type to JS file

= 1.2.3 (2012-8-9) =
* Added requirement for jQuery due to bug with code introduced in 1.2.2

= 1.2.2 (2012-8-9) =
* Removed need for jQuery

= 1.2.1 (2012-8-9) =
* Moved script to footer on advice of Ronald Huereca and Bjørn Johansen
* Fixed potential security flaw in kill.php

= 1.2 (2012-8-5) =
* Fixed multisite and BuddyPress bugs
* Added support for bbPress registrations
* Added support for bbPress guest posting protection
* Removed the "bad word" list

= 1.1 (2012-8-5) =
* Added support for BuddyPress signup page
* Added support for WordPress multisite signup page

= 1.0.3 (2012-7-30) =
* Upgrade to documentation

= 1.0.2 (2012-7-30) =
* Changed name to 'spam-destroyer'

= 1.0.1 (2012-7-30) =
* Cleaned up some legacy code from older implementations

= 1.0 (2012-7-29) =
* Initial release

<small>Any beta/alpha versions to be released in future, will be posted for download on the <a href="https://geek.hellyer.kiwi/products/spam-destroyer/">Spam Destroyer plugin page</a>.</small>


== Credits ==

* <a href="https://wordpress.org/support/profile/stromhalm">Stromhalm</a> - Bug reporting<br />
* <a href="http://ocaoimh.ie/">Donncha O Caoimh</a> - Developer of Cookies for Comments, functionality of which is incorporated into Spam Destroyer<br />
* <a href="http://elliottback.com/">Elliot Back</a> - Developer of WP Hashcash, functionality of which is incorporated into Spam Destroyer<br />
* <a href="http://nettsett.no/">Marte Sollund and Ingvild Evje</a> - Bug reporting<br />
* <a href="http://eHermitsInc.com/">Brian Layman</a> - Code advice<br />
* <a href="http://ronalfy.com/">Ronald Huereca</a> - JS advice<br />
* <a href="https://twitter.com/shawngaffney">Shawn Gaffney</a> - Bug reporting<br />
* <a href="http://konstruktors.com/">Kaspars Dambis</a> - Bug reporting<br />
* <a href="http://www.wanderingjon.com/">Jon Brown</a> - Added error message filter</br />