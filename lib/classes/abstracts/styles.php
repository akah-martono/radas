<?php
namespace Radas\Lib\Classes\Abstracts;

/** 
 * @package Radas\Lib\Classes\Abstracts 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * */
abstract class Styles {
    protected const script_hook = 'admin_enqueue_scripts';
    protected static $styles = [];

    final public static function enqueue(string|array $handle){
        if(is_array($handle)){
            foreach($handle as $str_handle){
                static::enqueue($str_handle);
            }
            return;
        } 

        if(!array_key_exists($handle, static::$styles)) {            
            return;
        }        

        if( 0 === did_action(static::script_hook) ){
            add_action( static::script_hook, function() use ($handle) {
                wp_enqueue_style($handle);
            });
            return;
        } 

        wp_enqueue_style($handle);
    }

    final public static function register(){
        $register = function(array $styles) {
            foreach($styles as $handle => $style){
                if( !( defined( 'RADAS_DEV_MODE' ) && RADAS_DEV_MODE ) ){
                    $style['src'] = str_replace('.css', '.min.css', $style['src']);
                }                
                wp_register_style($handle, $style['src'], $style['deps'], $style['ver'], $style['media']);
            }    
        };

        $styles = static::$styles;
        add_action( static::script_hook, function() use ($register, $styles) {
            $register($styles);
        }, 1);
    }    
}