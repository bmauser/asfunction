/**
 * From @asfunction tag in <?php echo $asf['tag_file'] ?> 
 *
 * @param string $selector
 */
function &<?php echo $asf['function'] ?>($selector = null){

	static $obj = null;

	<?php if(isset($asf['params']['use_cache'])){ ?>
	static $cache;
	if(isset($cache[$selector])){
		return $cache[$selector];
	}
	<?php } ?>

	if($obj === null){
		<?php if($asf['target_type'] == 'function'){ ?>$obj = <?php echo $asf['target'] ?>();
		<?php } elseif($asf['target_type'] == 'var'){ ?>$obj = &<?php echo $asf['target'] ?>;
		<?php } else { ?>$obj = new <?php echo $asf['target'] ?>();<?php } ?> 
	}

	if(!$selector)
		return $obj;

	<?php if(isset($asf['params']['exception'])){
		if($asf['params']['exception'] == 'null')
			$asf['params']['exception'] = '';
	?> 
	$return = \Asfunction\getPropertyBySelector($obj, $selector, '<?php echo @$asf['params']['exception'] ?>', '<?php echo @$asf['params']['exceptionMsg'] ?>');
<?php } else { ?> 
	$return = \Asfunction\getPropertyBySelector($obj, $selector);
<?php } ?>

<?php if(isset($asf['params']['use_cache'])){ ?>
	$cache[$selector] = &$return;
<?php } ?>
	return $return;
}


