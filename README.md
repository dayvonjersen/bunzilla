## GETTING THIS WORKING IN 2023
## GETTING THIS WORKING IN 2023
## GETTING THIS WORKING IN 2023

### SET UP THE DATABASE
```
sudo mysql -u root < bunzilla.sql
sudo mysql -u root
mysql> CREATE USER 'bunzilla'@'localhost';
mysql> GRANT ALL PRIVILEGES ON bunzilla.* TO 'bunzilla'@'localhost';
Query OK, 0 rows affected (0.11 sec)
```

### FIX FILE PERMISSIONS THE RIGHT WAY
```
sudo chmod -R 0777 */* */.*
```

### CONFIGURE THE APP

>edit and save res/example.*.ini without the "example."

### CONFIGURE NGINX

>nvim /etc/nginx/sites-enabled/default

```
server {
    listen 1028;
    root /srv/bunzilla;
    # index index.php;

    location /tpl/ {
        root /srv/bunzilla;
    }
    location / {
        return 307 http://oysterpail:1028/index.php?url=$request_uri;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }
}
:wq
```

### COMMENT OUT THESE LINES:

>nvim /path/to/bunzilla/Bunzilla.php
```
168         if(is_array($url) && !preg_match('/^[a-z]+$/',$url[0]))
169              exit('fuk u');
170
```

### YES I COULD JUST UPDATE Bunzilla.php BUT I AM LAZY AF

### MAKE SURE YOU HAVE pdo_mysql LOADED (CHECK BY USING phpinfo();)
### IF NOT
```
sudo apt-get install php7.4-mysql
sudo service php7.4-fpm restart
sudo service nginx restart
```

### FIX THE FUCKING DB
```
ALTER TABLE `reports`
	CHANGE COLUMN `closed` `closed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `actual`;
```

# WORKS ON MY MACHINE

>-day Fri 09 Jun 2023 10:19:18 PM EDT

# bunzilla

#### current version: 0.2b

Bunzilla is a simple, standalone bug tracker with a bright and colorful UI designed for small teams.

>NOTE(day): 
>**god bless archive.org for caching all the images on the internet that ever existed**
>-day Fri 09 Jun 2023 08:06:09 PM EDT

![](https://dayvonjersen.com/bz/001.png)

---

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

### Prerequisites

 - PHP &gt;= 5.4
 - Apache 2 \(with mod\_rewrite\)
 - MySQL &gt;= 5.6.23 (for INET6\_\* functions)

---

### Installation

Clone the repository and get ready to edit a few files:

```
$ git clone https://github.com/dayvonjersen/bunzilla.git
$ cd bunzilla
```

Create the database using the [sql file](bunzilla.sql), e.g.:
```
$ mysql -u root < bunzilla.sql
```

Create a file named **res/db.config.ini**:
```
[mysql]
host = localhost
user = root
pass = password123
dbname = bunzilla
```
There is an an example in [res/example.db.config.ini](res/example.db.config.ini)

Create a file named **res/settings.ini**, there is an example in [res/example.settings.ini](res/example.settings.ini).

Create a file named **.htaccess**:
```
RewriteEngine On
RewriteBase /bunzilla
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
```

There is an example in [example.htaccess](example.htaccess)

Run [res/generatepasswd.php](res/generatepasswd.php) to create a compatible .htpasswd
 
```
$ cd res/
$ ./generatepasswd.php
```

The username and password you specify with this can be used to log in and gain total administrative access. 

(At the moment, all administrative access (login) is done through HTTP Authentication.)

Optionally, if you want to hide bunzilla entirely behind HTTP Authentication, edit `.htaccess` to include:

```
AuthType Basic
AuthName Bunzilla
AuthUserFile /path/to/bunzilla/res/.htpasswd
Require valid-user
```

Open up bunzilla in your web browser, log in, go to the cpanel (link in the sidebar), add some metadata and start posting some reports!

**UPDATE Wed 01 Apr 2015 11:04:40 PM EDT**

You should be able to upgrade bunzilla at any time with `git pull`

**UPDATE Fri 24 Apr 2015 03:03:42 PM EDT**

If you want minified assets, set `BUNZ_DEVELOPMENT_MODE` to false in [Bunzilla.php](Bunzilla.php).

How these files got generated is shown in the [HOW2MINIFY](HOW2MINIFY) file. 

If you know how to get uglify-js to --mangle-props without breaking everything please tell me.

---

#### Other software used in this project

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
 - Captcha courtesy [textcaptcha](http://textcaptcha.com)

All of the above are included with Bunzilla and some of the above has been modified in some way by yours truly.

The JavaScript widgets I have written from scratch for this include the toolbar for inserting HTML, the preview functionality, and tag cloud on the index.

### A note about highlight.js:
The packaged version of highlight.js included here has support for *every language* for demonstration purposes only. 

I *highly* recommend heading over to https://highlightjs.org/ and customizing a lightweight package for your project that includes only the languages you actually use.

Put it in `tpl/material/assets/js/` as `highlight.js`

And edit [tpl/material/toolsModal.html](tpl/material/toolsModal.html)

### Features

#### Customization

![](https://dayvonjersen.com/bz/002.png)

All of the colors, icons, and of course text labels can be changed either through the cpanel or in `res/settings.ini`

#### Categories

![](https://dayvonjersen.com/bz/003.png)

Categories are merely how reports are grouped. You can create as many categories as you like. Whatever applies to your project and your workflow can be set up.

In addition to "subject" and "description", the traditional "reproduce", "expected", and "actual" are available fields for each category to require. The only mandatory field for a category to have is "subject", however.

This means that you can require as much or as little detail as appropriate for the category at hand. 

If you want to have a category for just simple one-line micro-blog style notes, do it. 

If you want to have just "expected" and "actual" because you can't be bothered, go for it. 

If you want all 4 or just description; ... it's up to you

**Example Categories**

 - Bug Reports
 - Feature Requests
 - TODO

#### Statuses

![](https://dayvonjersen.com/bz/004.png)

Statuses are what you can use to mark the progress of a report. Again, it can be anything you like so make it make sense for you.

All reports will start off with a (configurable) default status. There is no limit to the number of statuses you can have, though they are limited to 25 characters for visual purposes

**Example Statuses**

 - Unassigned **(default)**
 - Assigned
 - Resolved
 - Unresolved
 - Can't Fix
 - Won't fix
 - Don't know how to fix

The sky's the limit!

#### Tags

![](https://dayvonjersen.com/bz/005.png)

Tags describe issues at a glance when such descriptions pan many categories. 

They can also be used as convenient search terms.

Tags are completely optional and can be added or removed from a report via the edit page

If you don't know where to start, I suggest tags based on what applies *specifically to your project in particular*, e.g.:

**Example Tags for Webdev:**
 - Software and programming languages used by your project e.g.:
	- linux, apache, mysql, php
	- html, css, javascript
 - Web browsers which you aim to support e.g.:
	- firefox, internet explorer, opera, chrome, safari
 - Frameworks you are using e.g.:
	- angularjs, bootstrap, polymer, materializecss, jquery

**Example Tags for Desktop Applications:**
 - Programming languages used in your application e.g.:
	- c#, c++, java, python
 - SDKs and toolkits e.g.:
	- gtk, qt
 - IDEs, e.g.:
	- visualstudio, eclipse, emacs
 - Target platforms e.g.:
	- windows, linux, unix, mac

**Example Tags for Embedded programming**
 - ASM, C, arduinoc
 - arduino, raspi, beaglebone
 - circuit design, hardware

Try to avoid general tag names such as "bugfix", "idea", "experimental", "user experience", "design", "functionality", or other similarly descriptive terms. While they might be relevant to the report itself, they will diminish the relevant search value if every report is tagged like this.

The idea here is to be able to say "I want to write some python today", search for "tag:python" and get a bunch of high-priority, previously unassigned reports to work on.

Speaking of which...

### Priorities

![](https://dayvonjersen.com/bz/006.png)

Priorities are an additional layer of classifying meta-data which affect the order of reports in a category list. Assigning priorities appropriately will let you see what's most important so that you can see where you should focus first.

Like statuses, a default priority will be assigned to all new reports submitted. Unlike statuses, the ID determines the importance of the priority.

**Example Priorities**
 - Trivial (0)
 - Minor (1)
 - Normal (2)
 - Major (3)
 - Critical (4)

You should probably avoid having too many priorities. 

### Closed Reports
Closed reports can still be commented upon---intentionally; by design. 

There's no reason to make drama llamas out of "closed" issues or "necromancy" (unless it's literal). If an issue resurfaces, reopening it and changing the status can be done with ease.

If a report is a duplicate, there is an alternative to the "MARKED AS DUPLICATE (CLOSED) (NO SHUT UP)" paradigm that you might have come across.

### Merging

![](https://dayvonjersen.com/bz/007.png)

Merging is an experimental feature that takes a report and turns it (and its associated comments) into a quotereply to another report.

### Quotereply
Quotereply lets you directly reply to a comment, but this only goes 1 level deep at the moment.

### Changelog 

![](https://dayvonjersen.com/bz/012.png)

Changelog entries can be automatically generated with any comment.

After fixing a bug you can comment (before or after closing it!) like so
> - **BUGFIX** fixed the memory leak
> - Added awesome new feature!
> - Removed crappy old feature no one liked!

And it will show up on the changelog for the current project version. If you increment the project version in `res/settings.ini` new entries will go under that version.

Changelog can additionally be viewed as plaintext or unstyled HTML for all versions or any particular version.

**Note Fri 24 Apr 2015 03:11:46 PM EDT**

This is not meant to replace the changelog for your project, as right now you cannot easily edit the entries in it, but rather make it easier to generate one when you plan to package up a release.

### Search

![](https://dayvonjersen.com/bz/008.png)

Working on making it better but as far as options:

 - typing in words tries to match those words

 - you can search by category, status, priority, or tag with either id or title, e.g.
 
   - tag:1 

   - category:todo

 - you can do the opposite of this by prepending "-"

   - -status:done 

   - -priority:low

**Note: Fri 24 Apr 2015 03:16:25 PM EDT**

Being able to do "some string -category:5" is meant to work, but it might break at the moment.

### Alternate templates

#### nofrills

![](https://dayvonjersen.com/bz/009.png)

Unstyled, pure HTML designed for text-only browsers such as lynx. Can be used as a basis for new templates.

Append "?nofrills" to the url to use.

**Note:** not yet implemented for all pages

**Note: Fri 24 Apr 2015 03:17:17 PM EDT**

"?nofrills" is meant to be persistent, append "?material" to get back.

#### RSS Feeds

![](https://dayvonjersen.com/bz/010.png)

*Currently only available for categories and individual reports.*

Append "?rss" to the url to use.

**Note:** you will only receive new reports and not updates on their status. I may address this in the future, or this non-spammy behavior might actually be a good thing.

#### JSON 

![](https://dayvonjersen.com/bz/011.png)

Append "?json" to the url to use alternate views which export the page data as JSON.

**Note:** Not yet implemented for all pages. Notably any of the cpanel pages

I am using it in a few places to introduce some useful ajax features. If someone wants to create a single-page application view (template) for Bunzilla, this is how it could be done.

If you wish to do API stuff for some reason, you _can_ make curl requests to either POST or to GET pages that require logging in with

`--basic --user username:password`

Otherwise, the CSRF filtering will deny you the POST request. Further, there are pages that do redirects with HTTP Location headers

You must POST form data in the old-fashioned "param1=value&param2=anothervalue" way. Look at the HTML forms or PHP source for what data to send.

---

## License

[http://www.wtfpl.net/about](http://www.wtfpl.net/about)

```
            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.
```
