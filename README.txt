 First Installation of PLUME CMS
=================================

Unpack the archive of PLUME CMS into a folder of your website, it can be
the "root" folder. Create a MySQL database with phpMyAdmin or use the one
given by your service profider.
Go with your browser to "http://yoursite.com/manager/install/" and follow
the instructions.

Then uncomment the line:
#deny from all
To have
deny from all

of the .htaccess file in the manager/install/ folder if you want
to protect your installer. It is normally made in a way that after
the install and the upgrade, another person can't arm your installation
but we never know really.

Register to the security mailing list. This is very important, you
will receive an email only when a security issue is found, maybe not a
single email during 6 months, but the day you will receive one, it may
save your website.  
http://groups.google.com/group/plume-cms-security

 Upgrade of a PLUME CMS Installation
=====================================

Do a backup of your files and database, we tested the procedure, but
nobody can be sure. I repeat, DO A BACKUP OF YOUR DATABASE.

Unpack the new version of PLUME CMS on the top of the old one. Your
current configuration file must be kept. That is the files:
manager/conf/config.php
manager/conf/configweb_default.php
manager/conf/configweb_idOfYourSite.php
must be kept.
Go with your browser to "http://yoursite.com/manager/install/" and follow
the instructions. You must see an "upgrade procedure" if not, stop 
immediately.

Then uncomment the line:
#deny from all
To have
deny from all

of the .htaccess file in the manager/install/ folder if you want
to protect your installer. It is normally made in a way that after
the install and the upgrade, another person can't arm your installation
but we never know really.

