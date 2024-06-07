<?php
namespace Radas\Lib\Classes\Trait;

/** 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
*/
trait Element_Skeleton {  
    /** 
     * @var string $id 
     */    
    protected $id;

    /** @var string $label */
    protected $label;

    /** @var string $description */
    protected $description;    

    /**
     * @var callable
     */
    protected $render_cb;

    /** @var array $classes */
    protected array $classes = [];

    /** @var array $attributes */
    protected array $attributes = [];    

    /** @var array $remove_classes_list */
    protected array $remove_classes_list = [];    

    /**
     * @param string $id 
     * @return void 
     */
    public function __construct(string $id){
        $this->id = $id;
        $this->attributes["id"] = $id;
    }

    /**
     * @param string $label 
     * @return $this 
     */
    public function set_label(string $label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $description 
     * @return $this 
     */
    public function set_description(string $description) {
        $this->description = $description;
        return $this;
    } 

    /**
     * @param callable $render_cb 
     * @return $this  
     */
    public function set_render_cb(callable $render_cb){
        $this->render_cb = $render_cb;
        return $this;
    }
    
    /**
     * @param string $class 
     * @return $this 
     */
    public function add_class(string $class) {
        $class = explode(' ', $class);
        $this->classes = array_unique(array_merge($this->classes, $class)) ;
        return $this;
    }

    /**
     * @param string $class 
     * @return $this 
     */
    public function remove_class(string $class) {
        $class = explode(' ', $class);
        $this->remove_classes_list = array_unique(array_merge($this->remove_classes_list, $class)) ;
        return $this;
    }

    /**
     * @param string $key 
     * @param string $value 
     * @return $this 
     */
    public function add_attributes(string $key, string $value){
        $this->attributes[$key] = $value;
        return $this;
    }

    /** @return void  */
    protected function render_attributes(){
        foreach($this->attributes as $key=>$value){
            if(strtolower(trim($key))==='class'){
                $this->add_class($value);
            } else {
                if($value === null){
                    printf(' %s ', esc_attr( $key ));
                }else{
                    printf(' %s="%s"', esc_attr( $key ), esc_attr( $value ));
                }
            }
        }

        $this->render_classes();
    }

    /** @return void  */
    protected function render_classes(){
        foreach ($this->classes as $key => $value){
            if(in_array($value, $this->remove_classes_list )) {
                unset ($this->classes[$key]);
            }
        }
        if($this->classes)  echo ' class="' . esc_attr( implode(' ', $this->classes) ) . '"';
    }

    /**
     * @param mixed $name 
     * @return mixed 
     */
    public function __get($name): mixed {
        if(property_exists($this, $name)){
            return @$this->$name;
        }
        return null;
    }
}