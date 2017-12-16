<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
if (!defined('__ROOT__')) {
	$_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
	define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
}
define('APP_PATH', __DIR__ . '/../application/');
define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
define('BADWORD_FILE', __DIR__.'/../data/badword.txt');
define('UPLOAD_PATH', __DIR__.'/static/uploads/excel/');
require __DIR__ . '/../thinkphp/start.php';
