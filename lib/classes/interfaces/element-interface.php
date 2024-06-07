<?php
namespace Radas\Lib\Classes\Interfaces;

/** 
 * @package Radas\Lib\Classes\Interfaces 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1  
 * */
interface Element_Interface {
    
    public function render();

    /**
     * @param callable $render_cb 
     * @return $this  
     */
    public function set_render_cb(callable $render_cb);
    
    /**
     * @param string $class 
     * @return $this 
     */
    public function add_class(string $class);

    /**
     * @param string $class 
     * @return $this 
     */
    public function remove_class(string $class);

    /**
     * @param string $key 
     * @param string $value 
     * @return $this 
     */
    public function add_attributes(string $key, string $value);


    /**
     * @param mixed $name 
     * @return mixed 
     */
    public function __get($name): mixed ;
}