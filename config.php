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
	define("DB_USER", "root");  
	define("DB_PASS", "A0a1s2d3f4g5h6"); 
	define("DB_NAME", "admin_alias");

	/*
		root path for webpage
	*/
	define("ROOT_PATH", "/alias/");

	/*
		Root url for webpage
	*/
	define("ROOT_URL", "http://".$_SERVER['HTTP_HOST'].ROOT_PATH);

	define("MAIN_PAGE", ROOT_URL);

	define('ADMIN_GUI', false);

	$GLOBALS['lang'] = json_decode(file_get_contents(__DIR__.'/core/locale.json'), true);
	$GLOBALS['lang_content'] = file_get_contents(__DIR__.'/core/locale.json');
	/*
		css gobal version
	*/
	define('VERSION', '2.8.334');
