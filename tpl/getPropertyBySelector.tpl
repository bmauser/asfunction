namespace Asfunction;


/**
 * Returns a reference to an object property or an array member by selector.
 *
 * @param object|array $object
 * @param string $selector
 * @param string $exception_class_name
 * @param string $exception_message
 */
if (!function_exists('\Asfunction\getPropertyBySelector')) {
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
}


