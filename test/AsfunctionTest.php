<?php


use PHPUnit\Framework\TestCase;


// composer autoloader
//require '/home/dev/.composer/vendor/autoload.php';
require __DIR__ . '/../lib/Generator.php';




// start tests:
// /home/dev/.composer/vendor/bin/phpunit AsfunctionTest.php
// /home/dev/.composer/vendor/bin/phpunit /w/webroot/asfunction/test/AsfunctionTest.php
// /home/dev/.composer/vendor/bin/phpunit AsfunctionTest.php --filter=testTemplatePropSelectorN

class AsfunctionTest extends TestCase{


	protected $testDir = __DIR__ . '/test_data';
	protected $testFile = '/definitions.php';


	public function testParsedFileData(){

		// @asfunction generator object
		$generator = new GeneratorTest;

		// get data from test_data/definitions.php
		$asfunction_data = $generator->checkFile($this->testDir . '/' . $this->testFile);

		// count of namespaces
		$this->assertEquals(4, count($asfunction_data['asfunction']));

		// check returning array structure
		$i = 0;
		foreach ($asfunction_data['asfunction'] as $namespace => $function_data) {
			$i++;

			// check namespace names
			if($i == 1){
				$this->assertEquals('\Test1', $namespace);
				$this->assertArrayHasKey('Func1', $function_data);
				$this->assertArrayHasKey('Func2', $function_data);
				$this->assertArrayHasKey('Func3', $function_data);
			}
			else if($i == 2){
				$this->assertEquals('\Test2', $namespace);
				$this->assertArrayHasKey('Func1', $function_data);
				$this->assertArrayHasKey('Func2', $function_data);
			}
			else if($i == 3){
				$this->assertEquals('\Test3', $namespace);
				$this->assertArrayHasKey('GlobalsN', $function_data);
				$this->assertArrayHasKey('Cfg', $function_data);
			}
			else if($i == 4){
				$this->assertEquals('\Test4\Views', $namespace);
				$this->assertArrayHasKey('Server', $function_data);
			}
		}
	}


	public function testParseTxtClass(){

		$txt = '// @asfunction \Test\Func1() singleInstance \App\TestClass1' . "\n";

		$generator = new GeneratorTest;
		$data = $generator->parseTxt($txt);

		$expected['asfunction']['\Test']['Func1'] = array(
				'function'    => 'Func1',
				'target'      => '\App\TestClass1',
				'target_type' => 'class',
				'template'    => 'singleInstance',
				'tag_file'    => '',
				'params'      => array(),
				'namespace'   => '\Test',
		);

		$this->assertEquals($expected, $data);
	}


	public function testParseTxtClassTabsParams(){

		$txt = '	//	@asfunction  \Test\Func2()	singleInstance	\App\Test1\Test2\TestClass1 param5=555 param6=666' . "\n";

		$generator = new GeneratorTest;
		$data = $generator->parseTxt($txt);

		$expected['asfunction']['\Test']['Func2'] = array(
				'function'    => 'Func2',
				'target'      => '\App\Test1\Test2\TestClass1',
				'target_type' => 'class',
				'template'    => 'singleInstance',
				'tag_file'    => '',
				'params'      => array('param5' => '555', 'param6' => '666'),
				'namespace'   => '\Test',
		);

		$this->assertEquals($expected, $data);
	}


	public function testParseTxtVarParams(){

		$txt = '// @asfunction \Test1\Server() testtest  $_SERVER exception=null param1=111 param2=222' . "\n";

		$generator = new GeneratorTest;
		$data = $generator->parseTxt($txt);

		$expected['asfunction']['\Test1']['Server'] = array(
				'function'    => 'Server',
				'target'      => '$_SERVER',
				'target_type' => 'var',
				'template'    => 'testtest',
				'tag_file'    => '',
				'params'      => array('exception' => 'null', 'param1' => '111', 'param2' => '222'),
				'namespace'   => '\Test1',
		);

		$this->assertEquals($expected, $data);
	}


	public function testParseTxtFuncionParams(){

		$txt = ' *   @asfunction \Test2\Views\Server() alias \App\Server()  param3=333 param4=444' . "\n";

		$generator = new GeneratorTest;
		$data = $generator->parseTxt($txt);

		$expected['asfunction']['\Test2\Views']['Server'] = array(
				'function'    => 'Server',
				'target'      => '\App\Server',
				'target_type' => 'function',
				'template'    => 'alias',
				'tag_file'    => '',
				'params'      => array('param3' => '333', 'param4' => '444'),
				'namespace'   => '\Test2\Views',
		);

		$this->assertEquals($expected, $data);
	}


	public function testGenerateRelativePath(){

		$test_options = array(
				'sourceFiles' => ['test_data/definitions.php'],
		);

		$generator = new GeneratorTest($test_options);

		$this->assertEquals(1, $generator->write());
	}


	public function testGenerateAbsoluteDir(){

		$test_options = array(
				'sourceFiles' => [__DIR__ . '/test_data'],
		);

		$generator = new GeneratorTest($test_options);

		$this->assertEquals(1, $generator->write());
	}

	public function testTemplateSingleInstance(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertInstanceOf('App\TestClass1', \Test1\Func1());

		$this->assertInstanceOf('App\Test1\Test2\TestClass2', \Test1\Func2());

		$this->assertEquals('testdata1', \Test1\Func1()->testFunction());


		$object1 = \Test1\Func1();
		$object1->t = 123;

		$object2 = \Test1\Func1();
		$object2->t = 456;

		$this->assertEquals(456, $object1->t);
		$this->assertEquals(456, $object2->t);
	}

	public function testTemplateSingleInstanceConstructor(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertEquals('unset', \Test1\Func2()->getData());
		$this->assertEquals('unset', \Test1\Func2()->getData());
	}


	/**
	 * @expectedException Asfunction\Exception
	 */
	public function testTemplateSingleInstanceConstructorValues(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertEquals('234', \Test1\Func2('234')->getData());
		$this->assertEquals('234', \Test1\Func2()->getData());

		\Test\Func2('789')->getData();
	}


	public function testTemplateSingleInstanceFunction(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertInstanceOf('\App\Test1\Test2\TestClass2', \Test1\Func3());
	}


	public function testTemplateGetNewInstance(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertInstanceOf('\App\TestClass1', \Test2\Func1());

		$object1 = \Test2\Func1();
		$object1->t = 123;

		$object2 = \Test2\Func1();
		$object2->t = 456;

		$object3 = \Test2\Func1();
		$object3->t = 789;

		$this->assertEquals(123, $object1->t);
		$this->assertEquals(456, $object2->t);
		$this->assertEquals(789, $object3->t);
	}


	public function testTemplateGetNewInstanceFunction(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertInstanceOf('\App\Test1\Test2\TestClass2', \Test2\Func2());

		$object1 = \Test2\Func1();
		$object1->t = 123;

		$object2 = \Test2\Func1();
		$object2->t = 456;

		$object3 = \Test2\Func1();
		$object3->t = 789;

		$this->assertEquals(123, $object1->t);
		$this->assertEquals(456, $object2->t);
		$this->assertEquals(789, $object3->t);
	}


	public function testTemplatePropSelectorN(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$expected = $GLOBALS['settings'] = array(123, 456);

		$this->assertEquals($expected, \Test3\GlobalsN()['settings']);
		$this->assertEquals($expected, \Test3\GlobalsN('settings'));
		$this->assertEquals(null, \Test3\GlobalsN('settings123'));

		\Test3\GlobalsN()['settings'] = 789;
		$this->assertEquals(789, \Test3\GlobalsN()['settings']);

		\Test3\GlobalsN()['settings'] = array('aaa' => 111, 'bbb' => 222);
		$this->assertEquals(111, \Test3\GlobalsN()['settings']['aaa']);
		$this->assertEquals(111, \Test3\GlobalsN('settings.aaa'));
		$this->assertEquals(222, \Test3\GlobalsN()['settings']['bbb']);
		$this->assertEquals(222, \Test3\GlobalsN('settings.bbb'));

	}

	/**
	 * @expectedException Asfunction\Exception
	 */
	public function testTemplatePropSelector(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$expected = array('index1' => 123, 'index2' => 456);

		$this->assertEquals($expected, \Test3\Cfg());
		$this->assertEquals(123, \Test3\Cfg('index1'));

		\Test3\Cfg('index5');
	}



/*
	public function testTemplateSingleInstanceConstructorValuesTwice(){

		include_once __DIR__ . '/test_data/definitions.php';
		include_once __DIR__ . '/asfunctions.php';

		$this->assertEquals('234', \Test\Func2('234')->getData());
	}

*/



}








class GeneratorTest extends \Asfunction\Generator{

	function __construct($options = []){
		parent::__construct($options);
	}

	function checkDir($dir, $recursively = false){
		return parent::checkDir($dir, $recursively);
	}

	function checkFile($filepath){
		return parent::checkFile($filepath);
	}

	function parseTxt($txt, $filepath = ''){
		return parent::parseTxt($txt, $filepath);
	}
}
