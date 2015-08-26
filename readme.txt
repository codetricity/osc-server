
-----------------------------------------------------------------------

Copyright (C) 2015 NCTech Ltd.

Permission is granted to copy, distribute and/or modify this
document under the terms of the GNU Free Documentation License,
Version 1.3 or any later version published by the Free Software
Foundation; with no Invariant Sections, no Front-Cover Texts and
no Back-Cover Texts.  A copy of the license is included in the
section entitled "GNU Free Documentation License".

NCTech Limited
20-22 Braid Road, Edinburgh, Scotland, United Kingdom, EH10 6AD
Registered in Scotland - Company Number: SC389309
Telephone: +44 (0) 131 202 6258
Email: enquiries@nctechimaging.com

-----------------------------------------------------------------------

Installation
============

1. Install Apache web server with PHP 5 and SQLite 3, if you don't have a
     suitable setup already

  - Windows
    - We recommend installing Uniform Server, available from 
        http://www.uniformserver.com/
    - Enable SQLite3 extension by running UniServer Control panel, clicking
        'PHP', clicking 'edit selected configuration file', and uncommenting the
        line 'extension=php_sqlite3.dll'
    - Start the server by clicking "Start Apache" on the Uniserver Control Panel

  - Ubuntu
    - You can install a full LAMP stack with the following command:
      sudo apt-get install lamp-server^ phpmyadmin php5-sqlite

2. Copy the osc directory from the zip file into a suitable location in your 
     web server's http documents directory


