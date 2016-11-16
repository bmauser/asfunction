/**
 * From @asfunction tag in <?php echo $asf['tag_file'] ?> 
 *
 * @param string $instance_name
 * @return <?php if($asf['target_type'] != 'function'){?><?php echo $asf['target'] ?><?php }else{ ?><?php if(isset($asf['params'][0])) echo $asf['params'][0] ?><?php } ?> 
 */
<?php
if ($asf['target_type'] == 'var'){
	trigger_error("{$asf['target']} should be class or function.", E_USER_NOTICE);
}
else if ($asf['target_type'] == 'class'){ ?>
function <?php echo $asf['function'] ?>(){

	static $instance = null;

	if($instance === null){
		$constructor_args = func_get_args();

		// make a new instance with passing arguments to constructor
		if($constructor_args){
				$ref = new \ReflectionClass('<?php echo $asf['target'] ?>');
				$instance = $ref->newInstanceArgs($constructor_args);
			}
		// make a new instance
		else
			$instance = new <?php echo $asf['target'] ?>();
	}
	else if(func_get_args()){
		throw new \Asfunction\Exception("Trying to call constructor for existing <?php echo $asf['target'] ?> instance.");
	}

	return $instance;
}
<?php } else if ($asf['target_type'] == 'function'){ ?>

function <?php echo $asf['function'] ?>(){

	static $instance = null;

	if($instance === null){
		$instance = call_user_func_array('<?php echo $asf['target'] ?>', func_get_args());
		// todo: check $instance to be an object
	}
	else if(func_get_args()){
		throw new \Asfunction\Exception("Trying to call constructor for existing instance returned from <?php echo $asf['target'] ?>.");
	}

	return $instance;
}

<?php } ?>


