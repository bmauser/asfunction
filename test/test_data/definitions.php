<?php

// @asfunction \Test1\Func1()         singleInstance   \App\TestClass1
// @asfunction \Test1\Func2()         singleInstance   \App\Test1\Test2\TestClass2      param5=555 param6=666
// @asfunction \Test1\Func3()         singleInstance   \App\Test1\Test2\TestFunc1()

// @asfunction \Test2\Func1()         newInstance      \App\TestClass1
// @asfunction \Test2\Func2()         newInstance      \App\Test1\Test2\aaa()
// @asfunction \Test2\Func2()         newInstance      \App\Test1\Test2\TestFunc1()

// @asfunction \Test3\GlobalsN()      propSelector     $GLOBALS                         exception=null param1=111 param2=222
// @asfunction \Test3\Cfg()           propSelector     \App\getAppConfig()

// @asfunction \Test4\Views\Server()  alias            \App\Server()                    param3=333 param4=444




namespace App;

class TestClass1{

	public function testFunction(){
		return 'testdata1';
	}

}

function getAppConfig(){
	return array('index1' => 123, 'index2' => 456);
}

function Server(){
	new TestClass1();
}



namespace App\Test1\Test2;

class TestClass2{

	public function __construct($data = null){
		if(!$data)
			$this->data = 'unset';
		else
			$this->data = $data;
	}

	public function getData(){
		return $this->data;
	}

}

function TestFunc1(){
	return new TestClass2('TestFunc1');
}


function TestFunc2(){
	return array('index1' => 'TestFunc2');
}



