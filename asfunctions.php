<?php
// This file is generated with asfunction


namespace Test1;


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $instance_name
 * @return \App\TestClass1 
 */
function Func1(){

	static $instance = null;

	if($instance === null){
		$constructor_args = func_get_args();

		// make a new instance with passing arguments to constructor
		if($constructor_args){
				$ref = new \ReflectionClass('\App\TestClass1');
				$instance = $ref->newInstanceArgs($constructor_args);
			}
		// make a new instance
		else
			$instance = new \App\TestClass1();
	}
	else if(func_get_args()){
		throw new \Asfunction\Exception("Trying to call constructor for existing \App\TestClass1 instance.");
	}

	return $instance;
}


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $instance_name
 * @return \App\Test1\Test2\TestClass2 
 */
function Func2(){

	static $instance = null;

	if($instance === null){
		$constructor_args = func_get_args();

		// make a new instance with passing arguments to constructor
		if($constructor_args){
				$ref = new \ReflectionClass('\App\Test1\Test2\TestClass2');
				$instance = $ref->newInstanceArgs($constructor_args);
			}
		// make a new instance
		else
			$instance = new \App\Test1\Test2\TestClass2();
	}
	else if(func_get_args()){
		throw new \Asfunction\Exception("Trying to call constructor for existing \App\Test1\Test2\TestClass2 instance.");
	}

	return $instance;
}


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $instance_name
 * @return  
 */

function Func3(){

	static $instance = null;

	if($instance === null){
		$instance = call_user_func_array('\App\Test1\Test2\TestFunc1', func_get_args());
		// todo: check $instance to be an object
	}
	else if(func_get_args()){
		throw new \Asfunction\Exception("Trying to call constructor for existing instance returned from \App\Test1\Test2\TestFunc1.");
	}

	return $instance;
}





namespace Test2;


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $instance_name
 * @return \App\TestClass1 */
function Func1(){

	$constructor_args = func_get_args();

	// make a new instance wirh passing arguments to constructor
	if($constructor_args){
		$ref = new \ReflectionClass('\App\TestClass1');
		return $ref->newInstanceArgs($constructor_args);
	}

	// make a new instance
	return new \App\TestClass1();

}

/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $instance_name
 * @return  */
function Func2(){

	return call_user_func_array('\App\Test1\Test2\TestFunc1', func_get_args());

}



namespace Test3;


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $selector
 */
function &GlobalsN($selector = null){

	static $obj = null;

	
	if($obj === null){
		$obj = &$GLOBALS;
		 
	}

	if(!$selector)
		return $obj;

	 
	$return = \Asfunction\getPropertyBySelector($obj, $selector, '', '');

	return $return;
}


/**
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 *
 * @param string $selector
 */
function &Cfg($selector = null){

	static $obj = null;

	
	if($obj === null){
		$obj = \App\getAppConfig();
		 
	}

	if(!$selector)
		return $obj;

	 
	$return = \Asfunction\getPropertyBySelector($obj, $selector);

	return $return;
}




namespace Test4\Views;


/**
 * Alias for \App\Server 
 * 
 * From @asfunction tag in /w/webroot/asfunction/test/test_data/definitions.php 
 */
function Server(){
	return call_user_func_array('\App\Server', func_get_args());
}


namespace Asfunction;


/**
 * Returns a reference to an object property or an array member by selector.
 *
 * @param object|array $object
 * @param string $selector
 * @param string $exception_class_name
 * @param string $exception_message
 */
function &getPropertyBySelector(&$object, $selector, $exception_class_name = 'Asfunction\Exception', $exception_message = null){

	if(!$exception_message)
		$exception_message = 'Value ' . $selector . ' not set';

	$parts1 = explode('->', $selector);
	$ref = &$object;

	// find reference by selector
	foreach($parts1 as $k1 => $v1){
		$parts2 = explode('.', $v1);
		foreach($parts2 as $k2 => $v2){
			if($k2 > 0 or ($k1 == 0 && is_array($object))){
				if(isset($ref[$v2])){
					$ref = &$ref[$v2];
					continue;
				}
			}
			else if(isset($ref->$v2)){
				$ref = &$ref->$v2;
				continue;
			}

			$not_isset = 1;
			break 2;
		}
	}

	// if not set
	if(isset($not_isset)){
		if($exception_class_name){
			throw new $exception_class_name($exception_message);
		}
		else{
			$return = null;
			return $return;
		}
	}

	return $ref;
}


