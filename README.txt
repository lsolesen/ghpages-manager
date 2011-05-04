Github Pages Manager
====================

Proof of concept for creating a frontend for Github Pages using PHP and to play around with [webwork](http://github.com/troelskn/webwork)

Requirements
------------

* PHP > 5.2
* Git
* [webwork](http://github.com/troelskn/webwork)

Setup
-----

Make sure that you create an environments.local.inc.php in the config directory with this content:

    $GLOBALS['git_repository'] = '/path/to/your/checkout/gh-pages/branch';
    
Make sure that the directory is writable by your php user.

Disclaimer
==========

This is only a proof of concept. Do not use on a live site. There are a lot of improvements necessary:

* Security concerns
* Better error handling
* Making it possible to actually push changes to github.com
* Make the layout better
* A lot of code cleanups
* Make Are you sure? questions

