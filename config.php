<?php
	
	/*
		@DEBUG global variable for error reporting
	*/
	define('DEBUG', true);

	/*
		Database constants
		@DB_HOST - db hostname
		@DB_USER - db username
		@DB_PASS - db password
		@DB_NAME - db name
	*/
	define("DB_HOST", "localhost");
	define("DB_USER", "ninja");  
	define("DB_PASS", "passhere"); 
	define("DB_NAME", "alias");

	/*
		root path for webpage
	*/
	define("ROOT_PATH", "/".basename(dirname(__FILE__))."/");

	/*
		Root url for webpage
	*/
	define("ROOT_URL", $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].ROOT_PATH);

	define("MAIN_PAGE", ROOT_URL);

	define('ADMIN_GUI', false);

	$GLOBALS['lang'] = json_decode(file_get_contents(__DIR__.'/core/locale.json'), true);
	$GLOBALS['lang_content'] = file_get_contents(__DIR__.'/core/locale.json');
	/*
		css gobal version
	*/
	define('VERSION', '2.5.334');
