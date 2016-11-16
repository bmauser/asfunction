<?php

namespace Asfunction;


include __DIR__ . '/Exception.php';
include __DIR__ . '/Dully.php';


/**
 * Class with methods for generating asfunctions.php file.
 */
class Generator{


	/**
	 * Options.
	 *
	 * @var array
	 */
	protected $options;


	/**
	 * Constructor.
	 *
	 * Sets default options.
	 *
	 * @param array $options
	 */
	function __construct(array $options = []){

		$default_options = array(
				'destFile'    => 'asfunctions.php',
				'sourceFiles' => ['index.php'],
				'templateDir' => __DIR__ . '/../tpl',
				'workingDir'  => getcwd(),
				'tag'         => '@asfunction',
		);

		// set options
		foreach ($default_options as $option_name => $option_value) {
			if(array_key_exists($option_name, $options))
				$this->options[$option_name] = $options[$option_name];
			else
				$this->options[$option_name] = $default_options[$option_name];
		}
	}


	/**
	 * Looks for @asfunction definitions in files or folders.
	 *
	 * For folders it will check each .php file in the folder.
	 *
	 * @return array
	 */
	function scan(){

		$return = array();

		$sources = $this->options['sourceFiles'];

		if(!is_array($sources))
			$sources = [$sources];

		foreach($sources as $path){

			// if relative path
			if($path[0]!= DIRECTORY_SEPARATOR)
				$path = $this->options['workingDir'] . DIRECTORY_SEPARATOR . $path;

			$realpath = realpath($path);

			if(!$realpath)
				throw new Exception("Cannot open $path", 512);

			if(is_dir($realpath))
				$return = $this->checkDir($realpath);

			else if(is_file($realpath))
				$return = $this->checkFile($realpath);
		}

		$this->files_data = $return;

		return $return;
	}


	/**
	 * Looks for @asfunction definitions in all php files in the folder.
	 *
	 * Returns data for asfunction.php file.
	 *
	 * @param string $dir
	 * @param bool $recursively
	 * @return array
	 */
	protected function checkDir($dir, $recursively = false){

		$asfunction_data = array();

		if(!file_exists($dir) or !$handle = opendir($dir)){
			throw new Exception("Cannot open $dir.", 505);
		}

		while(($file = readdir($handle)) !== false){

			// skip files which start with .
			if($file[0] == '.'){
				continue;
			}

			// full filesystem path
			$filepath = $dir . '/' . $file;

			if($recursively && is_dir($filepath)){
				// call self for this directory
				$this->checkDir($filepath, $recursively);
			}			// foreach file
			else{
				// if file extension is '.php'
				if(substr($file, -4, 4) == '.php'){

					// file full path
					$filepath = realpath($filepath);

					// get data from @asfunction tags
					$asfunction_data = $this->checkFile($filepath);
				}
			}
		}

		closedir($handle);

		return $asfunction_data;
	}


	/**
	 * Looks for @asfunction definitions in a file and returns data for asfunction.php file.
	 *
	 * @param string $filepath
	 * @return array
	 */
	protected function checkFile($filepath){

		if(!file_exists($filepath)){
			throw new Exception("Cannot open file $filepath.", 511);
		}

		// file content
		$source = file_get_contents($filepath);
		return $this->parseTxt($source, $filepath);
	}



	/**
	 * Looks for @asfunction definitions in $txt and returns data for asfunction.php file.
	 *
	 * @param string $txt
	 * @param string $filepath
	 * @return array
	 */
	protected function parseTxt($txt, $filepath = ''){

		$return = array();

		// if there is @asfunction tag
		if(strpos($txt, $this->options['tag']) !== false){

			// find all @asfunction tags in phpdoc or // comments
			preg_match_all('/(\*|\/\/)\s+' . $this->options['tag'] . '\s+(.*?)\n/', $txt, $asfunction_comments);

			if(isset($asfunction_comments[2])){
				foreach($asfunction_comments[2] as $doc_tag){

					$asfunction_line = trim(preg_replace('/\s+/', ' ', $doc_tag));
					$params = explode(' ', $asfunction_line, 4);

					// invalid asfunction comment
					if(count($params) < 3)
						throw new Exception("Invalid {$this->options['tag']} tag $doc_tag in $filepath", E_USER_NOTICE);

					// name of the @asfunction
					$function_name = trim($params[0]);
					if(substr($function_name, -2) == '()') // remove last () from function name
						$function_name = substr($function_name, 0, -2);

					// namespace from $function_name
					$has_namespace = strrpos($function_name, '\\');
					if($has_namespace !== false){
						$namespace = substr($function_name, 0, $has_namespace);
						$function_name = substr($function_name, $has_namespace + 1);

						// if namespace is not absolute
						if($namespace[0] != '\\')
							throw new Exception("Not absolute namespace for $function_name", 510);
					}else
						throw new Exception("Namespace not set for $function_name", 509);

					// target
					$target = trim($params[2]);

					// target type
					$target_type = 'class';
					if(substr($target, -2) == '()')
						$target_type = 'function';
					else if(substr_count($target, '$'))
						$target_type = 'var';

					// remove last ()
					if($target_type == 'function')
						$target = substr($target, 0, -2);

					//if(isset($return['asfunction'][$namespace][$function_name]))
					//	trigger_error("Replaceing {$namespace}\\{$function_name} from {$return['asfunction'][$namespace][$function_name]['tag_file']}", E_USER_NOTICE);

					$template_params = array();

					// get template params
					if(isset($params[3])){

						$tparams_csv_arr = str_getcsv(trim($params[3]), ' ');

						foreach($tparams_csv_arr as $param_value){

							$key_val = explode('=', $param_value, 2);

							if(isset($key_val[1]))
								$template_params[$key_val[0]] = $key_val[1];
							else
								$template_params[] = $param_value;
						}
					}

					// data for template
					$return['asfunction'][$namespace][$function_name] = array(
							'function'    => $function_name,
							'target'      => $target,
							'target_type' => $target_type,
							'template'    => trim($params[1]),
							'tag_file'    => $filepath,
							'params'      => $template_params,
							'namespace'   => $namespace,
					);
				}
			}
		}

		return $return;
	}


	/**
	 * Writes asfunction.php file.
	 *
	 * @param array $data
	 * @return int 1 for sucess
	 */
	public function write($data = null){

		// call register() method if no data is passed
		if(!$data)
			$data = $this->scan();

		if(!$data){
			throw new Exception('No data to write to ' . $this->options['destFile'], 508);
		}

		$file_content = $this->getAsfunctionFileContent($data['asfunction']);

		// save file
		if(file_put_contents($this->options['destFile'], $file_content))
			return 1;

		throw new Exception('Error saving functions file to ' . $this->options['destFile'], 506);
	}


	/**
	 * Returns content for the asfunction.php file.
	 *
	 * @param array $data
	 * @return string
	 */
	protected function getAsfunctionFileContent($data){

		$dully = new Dully($this->options['templateDir']);

		$content = "<?php" . PHP_EOL;
		$content .= "// This file is generated with asfunction" . PHP_EOL; // date('d.m.Y H:i:s')

		$fetch_no = array();

		foreach($data as $namespace => $asfunctions){

			$content .= PHP_EOL . PHP_EOL . "namespace " . substr($namespace, 1) . ";" . PHP_EOL . PHP_EOL . PHP_EOL;

			foreach($asfunctions as $asfunction){

				$template_file = $asfunction['template'] . '.tpl';
				$template_path = $this->options['templateDir'] . '/' . $template_file;

				// template fetch counter
				if(!isset($fetch_no[$asfunction['template']]))
					$fetch_no[$asfunction['template']] = 0;

				if(file_exists($template_path)){
					$dully->assign('asf', $asfunction);
					$dully->assign('fetch_no', ++$fetch_no[$asfunction['template']]);
					$content .= $dully->fetch($template_file);
				}else{
					throw new Exception("Template file $template_path doesn't exists", 507);
				}
			}
		}

		// add getPropertyBySelector() function into the sfunction.php file
		if(isset($fetch_no['propSelector']))
			$content .= $dully->fetch('getPropertyBySelector.tpl');

		return $content;
	}
}
