<?php

namespace Asfunction;

/**
 * Simple template engine class.
 */
class Dully{

	/**
	 * Array that holds template values.
	 *
	 * @var array
	 * @ignore
	 */
	protected $template_values = array();


	/**
	 * Path to folder with templates.
	 * Without / at the end.
	 *
	 * @var string
	 * @ignore
	 */
	protected $template_dir;


	/**
	 * Constructor.
	 *
	 * @param string $template_dir Path to the the directory with templates.
	 */
	function __construct($template_dir){
		$this->template_dir = $template_dir;
	}


	/**
	 * Assigns values to the templates.
	 *
	 * @param string $name The name of the variable being assigned.
	 * @param mixed $value The value being assigned.
	 * @see display()
	 * @see fetch()
	 */
	function assign($name, $value){
		$this->template_values[$name] = $value;
	}


	/**
	 * Returns the fetched template as a string.
	 *
	 * @param string $template_file Template file name or relative path from the path passed to the constructor.
	 * @return string Fetched (rendered) template.
	 * @see display()
	 */
	function fetch($template_file){

		// this name will be in template vars scope
		$psa_sdf33saf2342as8dmm32 = $template_file;

		// extract the template_values to local namespace
		extract($this->template_values);

		// start output buffering
		ob_start();

		// include template file
		include $this->template_dir . '/' . $psa_sdf33saf2342as8dmm32;

		// get the contents and clean the buffer
		return ob_get_clean();
	}
}
