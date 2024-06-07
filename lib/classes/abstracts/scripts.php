<?php
namespace Radas\Lib\Classes\Abstracts;

/** 
 * @package Radas\Lib\Classes\Abstracts 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * */
abstract class Scripts {
    protected const script_hook = '';
    protected static $scripts = [];
    protected static $modules = [];

    final public static function enqueue(string|array $handle){
        if(is_array($handle)){
            foreach($handle as $str_handle){
                static::enqueue($str_handle);
            }
            return;
        } 

        if(!array_key_exists($handle, static::$scripts)) {
            return;
        }

        if( 0 === did_action(static::script_hook) ){
            add_action( static::script_hook, function() use ($handle) {
                wp_enqueue_script($handle);
            });
            return;
        } 

        wp_enqueue_script($handle);
    }
    
    final public static function register(){
        $scripts = static::$scripts;
        add_action( static::script_hook, function() use ($scripts) {
            foreach($scripts as $handle => $script){
                if( !( defined( 'RADAS_DEV_MODE' ) && RADAS_DEV_MODE ) ){
                    $script['src'] = str_replace('.js', '.min.js', $script['src']);
                }                

                if(array_key_exists('type', $script)){
                    $rds_handle = $handle; $type = $script['type'];
                    add_filter( 'script_loader_tag', function($tag, $handle) use ($rds_handle, $type){
                        if ($handle === $rds_handle){
                            $tag = str_replace( '<script ', '<script type="' . esc_attr($type) . '" ', $tag );
                        }
                        return $tag;
                    }, 10, 2 );
                }                    
                
                wp_register_script($handle, $script['src'], $script['deps'], $script['ver'], $script['args']);                

                if(array_key_exists('rest', $script) && $script['rest']){
                    wp_localize_script( $handle, 'wpApiSettings', [
                        'root' => esc_url_raw( rest_url() ),
                        'nonce' => wp_create_nonce( 'wp_rest'),
                    ] );                     
                }                
            }    
        }, 1);
    }

    final public static function register_modules(){
        /**
         * waiting for WordPress 6.6 released 
        */
        return;
        $modules = static::$modules;
        add_action( static::script_hook, function() use ($modules) {
            foreach($modules as $id => $module){
                if(!defined('RADAS_DEV_MODE')){
                    $module['src'] = str_replace('.js', '.min.js', $module['src']);
                }                
                wp_register_script_module( $id, $module['src'], $module['deps'], $module['ver'] );    

                if(array_key_exists('rest', $module) && $module['rest']){
                    wp_localize_script( $id, 'wpApiSettings', [
                        'root' => esc_url_raw( rest_url() ),
                        'nonce' => wp_create_nonce( 'wp_rest'),
                    ] );                     
                }                
            }
        }, 1);
    }

    final public static function enqueue_module(string|array $id){
        /**
         * waiting for WordPress 6.6 released 
        */
        return;     
        
        if(is_array($id)){
            foreach($id as $the_id){
                static::enqueue($the_id);
            }
            return;
        } 

        if(!array_key_exists($id, static::$modules)) {
            return;
        }

        if( 0 === did_action(static::script_hook) ){
            add_action( static::script_hook, function() use ($id) {
                wp_enqueue_script_module($id);
            });
            return;
        }

        wp_enqueue_script_module($id);
    }       

    final public static function add_data_by_id($handle, $id, $key, $value){        
        wp_add_inline_script( $handle, "jQuery(\"#{$id}\").data(\"{$key}\", {$value});");        
    }

    final protected static function tag_filter_for_type(string $rds_handle, string $type){
        add_filter( 'script_loader_tag', function($tag, $handle) use ($rds_handle, $type){
            if ($handle === $rds_handle){
                $tag = str_replace( '<script ', '<script type="' . esc_attr($type) . '" ', $tag );
            }
            return $tag;
        }, 10, 2 );        
    }
}