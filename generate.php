<?php
/**
 * Generates asfunctions.php file
 */

namespace Asfunction;


// allow execution only in cli mode
if(php_sapi_name() !== 'cli'){
	exit('This script can be run only in CLI mode.');
}


// echo function
function _echo($str){
	echo $str . "\n";
}


// working dir
$workingDir = getcwd();


// config file path
$config_file = $workingDir . '/.asfunction';


// check config file for options
if(file_exists($config_file)){

	_echo("Reading config file $config_file");

	// get content of options file
	$options = file_get_contents($config_file);

	// decode JSON to array
	$options = json_decode($options, true);

	if(!$options){

		_echo("Error getting options from $config_file");

		if(json_last_error() != JSON_ERROR_NONE)
			_echo("JSON error: " . json_last_error());

		exit (1);
	}
}
// use default option
else{
	_echo("$config_file file with options not found. Using defaults.");
	$options = Array('workingDir' => $workingDir);
}


// include asfunction generator
include __DIR__ . '/lib/Generator.php';


$generator = new Generator($options);


try{
	// save asfunctions.php file
	if($generator->write()){
		_echo("OK. Functions saved to: " . realpath($generator->getOptions()['destFile']));
		exit (0);
	}
}
catch (Exception $e){
	_echo($e->getMessage());
	exit ($e->getCode());
}

