bunzilla
-
Dead Simple Bug Tracking.
disclaimer
-
**right now this is in a very prototypical phase**
I'm working on it
literally

about
-
a very simple CMS to make bug tracking for a small team fun and easy

the idea here is a bright, colorful, upbeat can-do approach to organizing what needs to be done with regard to your project.

make custom categories which apply to your needs, whether that's one project or many, without complicating it all

make custom status messages to let everyone know what's going on

>**Example Status Messages for Bugs**.:

>- Can't Fix
- Won't fix
- Don't know how to fix

>The sky's the limit!

there's no reason to make drama llamas out of "closed" issues or "necromancy" (unless it's literal)

closed issues can still be commented upon---intentionally; by design. if an issue resurfaces, reopening it and changing the status can be done with ease

in other words, 
-
Bunzilla is merely a way to organize and keep track of notes and noteworthy dialogue about your project(s) rather than having a big, complicated, overly-professional (but ugly) over-glorified forum.

For discussions, face-to-face, telephones, e-mail, IRC, bbs, forums and social media are some more preferable communication mediums (in that order)

##it's alive
current working version: [https://var.abl.cl/bunzilla](https://var.abl.cl/bunzilla)

##installation
- php5.4
- apache2.whatever
- mysql but it uses PDO so you could use anything really*
######* might have to adjust the queries tho

Modify the .ini files in res/

I added some hacky HTTP Authentication:

Run 
`you@your-server:/var/www/bunzilla$ res/generatehtpasswd.php`

It's manual! And you need console access on the server! Hoorj
##steals from
- [purecss.io](http://purecss.io) -- requires a lot of hacky bullshit on top of it but >muh responsive
- [jscolor](http://jscolor.com) -- perfect JS color picker widget
- [highlightjs](http://highlightjs.org) -- perfect syntax highlighting thinger
- [Adobe](http://adobe.com) -- Source Code Pro font
- and previous very shitty forum software I wrote 10 years ago which was stolen from an arab, a furry, several psychopaths and some [lovable mute git who thinks he's a dragon](http://flussence.eu)


##license
still WTFPL
> Written with [StackEdit](https://stackedit.io/).

