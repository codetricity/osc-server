Overview
========
Mock server compliant with [Google Open Spherical (OSC) API specification]:(https://developers.google.com/open-spherical-camera/). Spherical cameras can take
360 degree still photo or video, making them useful for virtual reality
and projects that offer new experiences.


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


