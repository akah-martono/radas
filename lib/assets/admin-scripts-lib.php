<?php
namespace Radas\Lib\Assets;

use Radas\Lib\Classes\Abstracts\Scripts;

/** 
 * @package Radas\Lib\Assets 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1  
 * */
class Admin_Scripts_Lib extends Scripts{
    protected const script_hook = 'admin_enqueue_scripts';
        
    private const script_url = RADAS_URL . 'lib/assets/js/';
    protected static $scripts = [
        'rds-datatables' => [
            'src'   => self::script_url . 'datatables.js',
            'deps'  => [],
            'ver'   => '0.0.1',
            'args'  => ['strategy'=>'defer']           
        ],      
        'rds-media-box' => [
            'src'   => self::script_url . 'media-box.js',
            'deps'  => ['jquery'],
            'ver'   => '0.0.1.b',
            'args'  => ['strategy'=>'defer']           
        ],  
        'rds-tabs' => [
            'src'   => self::script_url . 'tabs.js',
            'deps'  => ['jquery'],
            'ver'   => '0.0.1.b',
            'args'  => ['strategy'=>'defer']           
        ], 
        'rds-page' => [
            'src'   => self::script_url . 'page.js',
            'deps'  => ['jquery'],
            'ver'   => '0.0.1.c',
            'args'  => ['strategy'=>'defer']           
        ],     
        'jquery-ui-touch-punch' => [
            'src'   => self::script_url . 'jquery.ui.touch-punch.js',
            'deps'  => ['jquery', 'jquery-ui-sortable', 'jquery-ui-droppable'],
            'ver'   => '0.0.1.b',
            'args'  => ['strategy'=>'defer']           
        ],     
    ];

    protected static $modules = [  
        '@radas/admin' => [
            'src'   => self::script_url . 'm-admin.js',
            'deps'  => ['@radas/general'],
            'ver'   => '0.0.1',
        ],       
        '@radas/general' => [
            'src'   => self::script_url . 'm-general.js',
            'deps'  => [],
            'ver'   => '0.0.1',
        ],       
        '@radas/iconbox' => [
            'src'   => self::script_url . 'm-iconbox.js',
            'deps'  => ['@radas/general'],
            'ver'   => '0.0.1',
        ],       
        '@radas/public' => [
            'src'   => self::script_url . 'm-public.js',
            'deps'  => [],
            'ver'   => '0.0.1',
        ],       
        '@radas/utils' => [
            'src'   => self::script_url . 'm-utils.js',
            'deps'  => [],
            'ver'   => '0.0.1',
        ],       
        
    ];    
}