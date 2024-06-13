<?php
namespace Radas\Admin\Assets;

use Radas\Lib\Classes\Abstracts\Scripts;

class Admin_Scripts_Rds extends Scripts {
    protected const script_hook = 'admin_enqueue_scripts';
    
    private const script_url = RADAS_URL . 'admin/assets/js/';

    protected static $scripts = [        
        '_rds-option-page-cpt' => [
            'src'   => self::script_url . 'm-option-page-cpt.js',
            'deps'  => ['jquery', 'rds-datatables', 'jquery-ui-sortable', 'jquery-ui-droppable'],
            'ver'   => '0.0.1.b',
            'args'  => ['strategy'=>'defer'],
            'type'  => 'module',
            'rest'  => true,
        ],    
        '_rds-option-page' => [
            'src'   => self::script_url . 'm-option-page.js',
            'deps'  => ['jquery'],
            'ver'   => '0.0.1.b',
            'args'  => ['strategy'=>'defer'],
            'type'  => 'module',
            'rest'  => true,
        ],                   
    ];

    protected static $modules = [  
        '_radas/fieldbox' => [
            'src'   => self::script_url . 'm-fieldbox.js',
            'deps'  => [
                '@radas/general', 
                '@radas/utils'
            ],
            'ver'   => '0.0.1',            
        ], 
        '_radas/option-page-cpt-table' => [
            'src'   => self::script_url . 'm-option-page-cpt-table.js',
            'deps'  => [],
            'ver'   => '0.0.1',
        ],         
        '_radas/option-page-cpt-page' => [
            'src'   => self::script_url . 'm-option-page-cpt-page.js',
            'deps'  => [
                '@radas/fieldbox', 
                '@radas/iconbox',
                '@radas/utils',
                '@radas/general',
                '@radas/admin',
            ],
            'ver'   => '0.0.1',
        ], 

        '_radas/option-page-cpt' => [
            'src'   => self::script_url . 'm-option-page-cpt.js',
            'deps'  => [
                '_radas/fieldbox', 
                '_radas/option-page-cpt-table', 
                '_radas/option-page-cpt-page',
                '@radas/general',
                '@radas/admin',
            ],
            'ver'   => '0.0.1',
            'rest'  => true,
        ], 
        '_radas/option-page' => [
            'src'   => self::script_url . 'm-option-page.js',
            'deps'  => [
                '@radas/general',
                '@radas/admin',
            ],
            'ver'   => '0.0.1',
            'rest'  => true,
        ],        
    ];    
}