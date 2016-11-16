/**
 * From @asfunction tag in <?php echo $asf['tag_file'] ?> 
 *
 * @param string $instance_name
 * @return <?php if($asf['target_type'] != 'function'){?><?php echo $asf['target'] ?><?php }else{ ?><?php if(isset($asf['params'][0])) echo $asf['params'][0] ?><?php } ?>
 */
function <?php echo $asf['function'] ?>(){

<?php if ($asf['target_type'] == 'class'){ ?>
	$constructor_args = func_get_args();

	// make a new instance wirh passing arguments to constructor
	if($constructor_args){
		$ref = new \ReflectionClass('<?php echo $asf['target'] ?>');
		return $ref->newInstanceArgs($constructor_args);
	}

	// make a new instance
	return new <?php echo $asf['target'] ?>();
<?php } else if ($asf['target_type'] == 'function'){ ?>
	return call_user_func_array('<?php echo $asf['target'] ?>', func_get_args());
<?php } ?>

}

