THIS FORK IS STILL A WORK IN PROGRESS, SOME BITS HAVE BEEN UPDATED AND WORK ON PHP 7.4 BUT NOT EVERYTHING HAS BEEN CHECKED AND TESTED YET. 
USE AT OWN RISK!!!

plogger-php7
=======
A simple php7-based web gallery that can be integrated into your website.
This fork is based on the fork by wuffleton which uses PDO as the database backend instead of the depreciated mysql library. This includes support for mysql and pgsql out of the box. I've updated and altered all the code so it will run on PHP 7.4.

This install also includes a Bootstrap 4 and Font Awesome 5 based theme. To use Font Awesome some functions in plog-functions.php have been altered to support this. The 3 base themes will be updated to work with these changes, this is all still a work in progress.

Installation & Usage
--------------------
To install, upload all of the files in the Plogger distribution to your web server.
Next, create a SQL database and user from your web hosting control panel or database shell.
Then, run the install script (/plog-admin/_install.php) in the web browser of your choice.
The script will guide you through the rest of the installation process.

Integration
-----------
To integrate Plogger into your website, place the following PHP statements in the .php
file you wish display the gallery in:

First line of the .php file -> ```<?php require("path/to/plogger.php");``` ?>
In the HEAD section -> ```<?php the_plogger_head(); ?>```
In the BODY section where you want the gallery -> ```<?php the_plogger_gallery(); ?>```

UPGRADING from original Plogger
-------------------------------
- Add the following lines to your config file preferably above the line `/* The name of the database for Plogger */`
and enter yout DB type, mysql or pgsql.

`/* Database Type */`
`/* Currently supports 'mysql' and 'pgsql' */`
`define('PLOGGER_DB_TYPE', '');`

`/* Database Port (Ignored for MySQL, optional for PgSQL) */`
`define('PLOGGER_DB_PORT', '');`

- Alter your PREFIX_config table structure and after gallery_url add
cdn_url	varchar(255)	utf8_general_ci

- I will need to find out how I can do this via the _upgrade.php.
Help welcome.

Upgrading from -RC1
-------------------
- Edit your DB information and table prefix into /plog-admin/_upgrade.php 
- Navigate to the script in your browser.
- This is a simple one-shot PHP script, so randomly refreshing or running it more than once may result in unpredictable behavior. 
- As with any database schema upgrade, your mileage may vary. I STRONGLY recommend you use mysqldump or similar to make a backup of your database first. 

Differences from -RC1
---------------------
- Database schema has been slightly changed to accomodate changes to the codebase. 
  If you are coming from RC1, see upgrade information above.
- Upgrade code for revisions older than RC1 has been removed since it would be overly complex and time consuming to update properly.
- Gallery URL handling improved. 
  Install script will detect HTTP/HTTPS, and admin panel will now allow relative paths to be used for the gallery location.
- CDN URL has been added so you can serve static files via a CDN.
- Certain error messages adapted to PDO changes. 
  Most DB errors will be hidden by default unless PLOGGER_DEBUG is on for security and elegance reasons.
- The phpthumb library has been updated to the latest version (1.7.14)
- Exifer 1.7 library has been updated to 2009 zenphoto version and improved for PHP7 compatability.
- More elegant handling of the EXIF information boxes (hides blank fields, option to turn off display completely). 
- SQL queries have been updated to be a more database agnostic and allow compatibility with both MySQL and PostgreSQL with minimal conditionals. 
- Other minor fixes

Licensing, Support, and other Credits
-------------------------------------
- Original Project: http://www.plogger.org
- Updated fork I used as the basis of mine https://github.com/wuffleton/plogger-pdo 
  which is based on https://github.com/alexzhuustc/plogger
- phpThumb: https://github.com/JamesHeinrich/phpThumb
- Exifer1_7: https://github.com/zenphoto/zenphoto/tree/master/zp-core/exif
- My edits are licensed under the same terms as the original project (GPL).
- If you see something wrong or that needs improving, file a bug or submit a pull request.
  I'll try my best to maintain this, but it's mostly a free-time project.
