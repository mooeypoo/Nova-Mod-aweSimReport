# aweSimReport LITE for Nova2

So, after much begging and requests, I decided to release a smaller version of the once-large-and-lovely aweSimReport for Nova2.

This is a very stripped-down version, meant to save GMs time by doing the log report counts for them. 
If I have some more time, I will work on expanding this edition for the future.

For the moment, the result is printed on a new blank page where you can copy/paste the results into an email.

## Install
I took great care to make sure the install is as easy as possible. 
All files in this edition are completely standalone, which means all you need to do is upload the "application" folder **as-is**, to 'replace' your website's "application" folder. The files are already in their proper folders.

### To access your brand new extension
Go to yoursite.com/index.php/awesimreport

### To create a permanent menu item
* Go to your Control Panel, then to "Menu Items".
* Click on "Add Menu Item"
* Fill out the following fields:

```
Name: aweSimReport Lite
link: awesimreport
Link Type: Onsite

Type: Admin Sub Navigation
Category: Admin Control Panel

Access Control:
Login Requirement: Must Be Logged In
Use Access Control: Yes
Access Control URL: site/settings
```

SAVE!

Enjoy. 

## Version
### Version 1.2
* Fixed database prefix bug in awesimreport model
* Fixed problem presenting LOA members.

### Version 1.1
* Fixed bug related to single manifest (didn't show correctly). Thanks Jaeger!

### Version 1.0
* Initial extension working.

## Bug Reports
If you find any, or have any special requests, please use the 'issues' tab. Please take into account I'm a graduate student with very little time. I'll be doing my very very best to answer bugs and requests, but be patient with me.

## Credit
This is an extension for Anodyne Nova RPG system. It is absolutely free to use. I'd appreciate it if you keep my name in the credits, and notify me if you create any cool changes or additions so I can add them here.

## Author and Copyright
Moriel Schottlender
mooeypoo@gmail.com
Copyright: [GNU General Public License](http://www.gnu.org/licenses/gpl.txt), 2012