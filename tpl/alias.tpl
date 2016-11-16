/**
 * Alias for <?php echo $asf['target'] ?> 
 * 
 * From @asfunction tag in <?php echo $asf['tag_file'] ?> 
 */
function <?php echo $asf['function'] ?>(){
	return call_user_func_array('<?php echo $asf['target'] ?>', func_get_args());
}


