<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(function($class) {
    $ns_prefix = 'Radas\\';
    if ( substr($class, 0, strlen($ns_prefix))  === $ns_prefix  ) {
        $class_file = substr($class, strlen($ns_prefix));
        $class_file = str_replace('\\', DIRECTORY_SEPARATOR, $class_file);
        $class_file = str_replace('_', '-', $class_file);
        $class_file = strtolower($class_file) . '.php';        
        require_once( RADAS_PATH . $class_file );
    }
});