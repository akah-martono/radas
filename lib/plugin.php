<?php
use Radas\Lib\Assets\Admin_Scripts_Lib;
use Radas\Lib\Assets\Admin_Styles_Lib;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('radas_lib_run', function () {
    /** run only once */
    if( did_action('radas_lib_start') > 1 ) return;
    
    add_action('init', 'radas_lib_register_scripts');
    add_action('init', 'radas_lib_register_styles');
});

function radas_lib_register_scripts(){
    Admin_Scripts_Lib::register();
}

function radas_lib_register_styles(){
    Admin_Styles_Lib::register();
}