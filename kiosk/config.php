<?php
// Simple dotenv loader
function loadEnv($path){
	if(!file_exists($path)) return;
	$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line){
		if(strpos(ltrim($line), '#') === 0) continue;
		[$name, $value] = array_map('trim', explode('=', $line, 2));
		if(!array_key_exists($name, $_ENV) && !getenv($name)){
			putenv("{$name}={$value}");
			$_ENV[$name] = $value;
		}
	}
}

loadEnv(__DIR__ . '/.env');

// Expose CORS for local kiosk usage if needed
if (php_sapi_name() !== 'cli'){
	header_remove('X-Powered-By');
}

