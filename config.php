<?php // Configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->adminpw = 'changeme';

$CFG->database  = 'csonline';
$CFG->dbhost    = '127.0.0.1';
$CFG->dbport    = '8889';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'root';

// Cookie parameters
$CFG->cookiesecret = 'dj21jt848s9f9ah2hhhtt13was8491rhq9fsuh93';
$CFG->cookiename = 'identity';
$CFG->cookiepad = 'ucitd';

