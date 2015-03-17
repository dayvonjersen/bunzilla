#bunzilla
####current version: 0.2
"Dead Simple Bug Tracking"
###[Live Demo and Meta-Tracker](https://meta.bunzilla.ga)

---

[About](#about)

 - [Other software used in this project](#stolen)
  
[Disclaimer](#disclaimer)
[Prerequisites](#prerequisites)
[Installation](#installation)
[Features](#features)

 - [Customization](#customization)
 - [Categories](#categories)
 - [Statuses](#statuses)
 - [Tags](#tags)
 - [Priorities](#priorities)
 - [Closed isn't a four-letter word](#closed-reports)
 - [Merge Reports Together](#merging)
 - [>implying](#quotereply)
 - [Integrated Changelog](#changelog)
 - [Search](#search)
 - [Alternate Views](#alternate-templates)


[License](#license)

---

###About

Bunzilla is a very simple <abbr title="Content Management System">CMS</abbr> to make bug tracking for a small team fun and easy.

The idea here is a bright, colorful, upbeat **can-do** approach to organizing what needs to be done with respect to your project.

--- 
###Stolen
...stuff used in this project include:

 - [MaterializeCSS](http://materializecss.com/)
 - Source Code Pro font from [Adobe](http://www.adobe.com/)
 - Icons courtesy [Fontello](http://www.fontello.com/)
 - Dynamic code highlighting courtesy [highlight.js](https://highlightjs.org/)
 - Sidebar design by [codrops](http://tympanus.net/codrops/?p=16030) inspired by [Google](http://web.archive.org/web/20130731035203/http://www.google.com/nexus/)
 - Modal window by [codrops](http://tympanus.net/codrops/2013/06/25/nifty-modal-window-effects/)
 - Hiding the header courtesy [Headroom.js](http://wicky.nillia.ms/headroom.js)
 - Color picker by [JSColor](http://jscolor.com/)
 - Sorting courtesy [List.js](http://www.listjs.com/)
 - And [jQuery](http://jquery.com) is in there too so yeah.

All of the above are included with Bunzilla and some of the above has been modified in some way by yours truly.

The only JavaScript widget I actually made for this is toolsModal.js in `tpl/material/assets/js`

###A note about highlight.js:
The packaged version of highlight.js included here has support for *every language* for demonstration purposes only. 

I *highly* recommend heading over to https://highlightjs.org/ and customizing a lightweight package for your project that includes only the languages you actually use.

Put it in `tpl/material/assets/js/` as `highlight.js`
~~and change `highlight.js-languages.json`~~
 edit `tpl/material/toolsModal.html`

---
###Disclaimer

**Right now this is *still* in a very prototypical phase**
######(I'm working on it).

---

###Prerequisites
 - PHP5.4
 - Apache 2 (with mod_rewrite)
 - MySQL5

---

###Installation

1. Clone the repository and get ready to edit a few files
`$ git clone https://github.com/generaltso/bunzilla.git`
`$ cd bunzilla`

2. Setup the DB and configure the db connection credentials
`$ mysql -u root < bunzilla.sql`
`$ vim res/db.config.ini`

3. Edit `res/settings.ini` and customize it to your liking, filling in your project's details.

4. Edit `.htaccess` for mod_rewrite, make sure it looks like this:
	`RewriteEngine On`
	`# for http://example.com/bunzilla`
	`RewriteBase /bunzilla`
	`RewriteCond %{REQUEST_FILENAME} !-d`
	`RewriteCond %{REQUEST_FILENAME} !-f`
	`RewriteCond %{REQUEST_FILENAME} !-l`
	`RewriteRule ^(.+)$ index.php?$1 [QSA,L]`

4. **Right now all administrative access (login) is done through HTTP Authentication.**

	In a future version there will be a more traditional, predictable, secure user system complete with access levels, handles, change email/password/display preferences and all that good stuff you've come to know and love on the Internet and that doesn't require such a lengthy explanation.

	But for now **run generatepasswd.php** to create a compatible .htpasswd
 `$ ./res/generatepasswd.php`
	
	The username and password you specify with this can be used to log in and gain total administrative access. 

	###If you want to *optionally* hide bunzilla completely behind HTTP Authentication, edit .htaccess to have:
	`AuthType Basic`
	`AuthName Bunzilla`
	`AuthUserFile /path/to/bunzilla/res/.htpasswd`
	`Require valid-user`

	But again, I must stress that this is optional. 
	
5. Open up bunzilla in your web browser, log in, go to the cpanel (link in the sidebar), add some categories, statuses, tags, and start posting some reports!

---

###Features

####Customization
Just thought I'd mention that all of the colors, icons, and of course text can be changed either through the cpanel or in `res/settings.ini`

####Categories
...are merely how reports are grouped. You can create as many categories as you like. Whatever applies to your project and your workflow can be set up.

In addition to "subject" and "description", the traditional "reproduce", "expected", and "actual" are available fields for each category to require. The only mandatory field for a category to have is "subject", however.

This means that you can require as much or as little detail as appropriate for the category at hand. 

If you want to have a category for just simple one-line micro-blog style notes, do it. 

If you want to have just "expected" and "actual" because you can't be bothered, go for it. 

If you want all 4 or just description; ... it's up to you

>**Example Categories**

> - Bug Reports
> - Feature Requests
> - TODO

####Statuses
..are what you can use to mark the status or progress of a report. Again, it can be anything you like so make it make sense for you.

All reports will start off with a (configurable) default status. There is no limit to the number of statuses you can have, though they are limited to 25 characters for visual purposes

>**Example Statuses**

> - Unassigned (set as default)
> - Assigned
> - Resolved
> - Unresolved
> - Can't Fix
> - Won't fix
> - Don't know how to fix
> - ...The sky's the limit!

####Tags
...describe issues at a glance when such descriptions pan many categories. 

They can also be used as convenient search terms.

Tags are completely optional and can be added or removed from a report via the edit page

If you don't know where to start, I suggest tags based on what applies *specifically to your project in particular*, e.g.:

>**Example Tags for Webdev:**
> ######Software and programming languages used by your project e.g.
> - linux, apache, mysql, php
> - html, css, javascript
> ######Web browsers which you aim to support e.g.
> - firefox, internet explorer, opera, chrome, safari
> ######Frameworks you are using e.g.
> - angularjs, bootstrap, polymer, materializecss, jquery
> 
> **Example Tags for Desktop Applications:**
> ######Programming languages used in your application e.g.
> - c#, c++, java, python
> ######Software development kits and toolkits e.g.
> - gtk, qt
> ######Integrated development environments e.g.
> - visualstudio, eclipse, emacs
> ######Target platforms e.g.
> - windows, linux, unix, mac

>**Example Tags for Embedded programming**
>
> - ASM, C, arduinoc
> - arduino, raspi, beaglebone
> - circuit design, hardware

Just *be careful with more general tag names* such as "bugfix", "idea", "experimental", "user experience", "design", "functionality", or other similarly descriptive terms. While they might be relevant to the report itself, they will diminish the relevant search value if every report is tagged like this.

The idea here is to be able to say "I want to write some python today", search for "tag:python" and get a bunch of high-priority, previously unassigned reports to work on.

Speaking of which...

###Priorities
...are an additional layer of classifying meta-data which affect the order of reports in a category list. Assigning priorities appropriately will let you see what's most important so that you can see where you should focus first.

Like statuses, a default priority will be assigned to all new reports submitted. Unlike statuses, the ID determines the importance of the priority.

> **Example Priorities**
> - Trivial (0)
> - Minor (1)
> - Normal (2)
> - Major (3)
> - Critical (4)

You should probably avoid having too many priorities. 

###Closed Reports
...can still be commented upon---intentionally; by design. 

There's no reason to make drama llamas out of "closed" issues or "necromancy" (unless it's literal). If an issue resurfaces, reopening it and changing the status can be done with ease.

If a report is a duplicate, there is...

###Merging
...which is an experimental feature that takes a report and turns it (and its associated comments) into a quotereply to another report.

###Quotereply
...lets you directly reply to a comment, but this only goes 1 level deep at the moment.

I'm tempted to keep this way as the nested reply structure of e-mail lists, slashdot, and reddit quickly turns into an incomprehensible knot of hard-to-follow eyestrain, in my humble opinion.

###Changelog 
...entries can be automatically generated with any comment.

After fixing a bug you can comment (before or after closing it!) like so
> - **BUGFIX** fixed the memory leak
> - Added awesome new feature!
> - Removed crappy old feature no one liked!

And it will show up on the changelog for the current project version. If you increment the project version in `res/settings.ini` new entries will go under that version.

Changelog can additionally be viewed as plaintext or unstyled HTML for all versions or any particular version.

###Search
...is being worked on. You can use it currently via the sidebar on any page

A more robust advanced search is planned with the ability to addtionally filter and sort results.

###Alternate templates
...to the default "material" template are also being worked on including:

####nofrills
Unstyled, pure HTML designed for text-only browsers such as lynx. Can be used as a basis for new templates.

Append "?nofrills" to the url to use.

***Note: not yet implemented for all pages **

####RSS Feeds
Currently only available for categories and individual reports.

Append "?rss" to the url to use.

####JSON API
Append "?json" to the url to use.
**\*Note: Not yet implemented for *any* page**

---
##License
http://www.wtfpl.net/about
>DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
>                    Version 2, December 2004 
>Copyright (C) 2004 Sam Hocevar <sam@hocevar.net> 

>Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long  as the name is changed. 

>DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
>TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION 
>0. You just DO WHAT THE FUCK YOU WANT TO.

---
In conclusion,
-
Bunzilla is merely a way to organize and keep track of notes and noteworthy dialogue about your project(s) rather than having a big, complicated, overly-professional (but ugly) over-glorified forum.

For discussions, face-to-face, telephones, e-mail, IRC, bbs, forums and social media are some more preferable communication mediums (in that order)

> Written with [StackEdit](https://stackedit.io/).
