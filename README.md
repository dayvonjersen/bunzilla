bunzilla
======
#####Dead Simple Bug Tracking.
##disclaimer
**right now this is in a very prototypical phase and is not very useful or pretty**

I'm working on it

literally

##about
a very simple CMS to make bug tracking for a small team fun and easy

every option I've come across has been cumbersome and designed for very large teams

every major project's bug tracker resembles more of a forum where unpopular opinions and dissent run rampant amongst power-mad moderators

and they're all hideously designed

##it's alive
current working version: [https://var.abl.cl/bunzilla](https://var.abl.cl/bunzilla)

##installation
- php5.4
- apache2.whatever
- mysql but it uses PDO so you could use anything really*

######* might have to adjust the queries tho

There's no authorization mechanism except for a very crude one in `Bunzilla.php` : `Controller::requireLogin()` 

I would recommend if you want to actually use this at this stage to setup HTTP Authorization with `.htaccess`

##usage
1. create the categories and statuses which fit your project's development needs 
2. ???
3. Profit.

##steals from
- [jscolor](http://jscolor.com) -- perfect JS color picker widget
- [highlightjs](http://highlightjs.org) -- perfect syntax highlighting thinger
- [Adobe](http://adobe.com) -- Source Code Pro font
- and previous very shitty forum software I wrote 10 years ago which was stolen from an arab, a furry, several psychopaths and some [lovable mute git who thinks he's a dragon](http://flussence.eu)


##license
let's say WTFPL for now
> Written with [StackEdit](https://stackedit.io/).

