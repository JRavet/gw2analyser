<?php

/**
 * database.php
 *
 * application database configuration
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

$config['default']['plugin'] = 'TinyMVC_PDO'; // plugin for db access
$config['default']['type'] = 'mysql';      // connection type
$config['default']['host'] = 'localhost';  // db hostname
$config['default']['name'] = 'Gw2Analyser';     // db name
$config['default']['user'] = 'gw2datacollector';     // db username
$config['default']['pass'] = 'egamirrorimeht';     // db password
$config['default']['persistent'] = false;  // db connection persistence?

?>