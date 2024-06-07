<?php
namespace Radas\Lib\Classes\Abstracts;

use Radas\Lib\Classes\Abstracts\Element;

/** 
 * @package Radas\Lib\Classes\Abstracts
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @inheritDoc
 * 
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read string $type
 * @property-read string $label
 * @property-read string $default
 * @property-read string $description 
 * @property-read string $text_left
 * @property-read string $text_right
*/    
abstract class Field extends Element {
    
    /** @var string $type */
    protected $type;
    
    /** @var string $value */
    protected $value;

    /** @var string $default */
    protected $default;    

    /** @var string $text_left */
    protected $text_left;

    /** @var string $text_right */
    protected $text_right;

    /** @var array $styles */
    protected array $styles = [];

    /** @var callable $validation_cb */
    protected $validation_cb;

    /** @return mixed the value ​​returned by this function has been sanitized */
    abstract public function get_sanitized_value();

    /** @return mixed  */
    abstract public function render_input();
    
    /**
     * @param string $id 
     * @return $this 
     */
    public function __construct(string $id) {        
        parent::__construct($id);
        
        $this->attributes['name'] = $id;
        $this->add_class('rds-input');

        // $input_field = ['checkbox', 'date', 'email', 'editor', 'number', 'tel', 'radio', 'select', 'text', 'textarea', 'url'];
        // if (in_array($this->type, $input_field)){
        //     $this->add_class('rds-input');
        // }
                
        // $regular_text = ['text', 'email', 'url', 'date', 'number', 'tel', 'select', 'textarea', 'media'];
        // if (in_array($this->type, $regular_text)){
        //     $this->add_class('regular-text');
        // }
    }

    /**
     * @param string $name 
     * @return $this 
     */
    public function set_name(string $name) {
        $this->attributes['name'] = $name;
        return $this;
    }
    
    /**
     * @param string $title 
     * @return $this 
     */
    public function set_title(string $title) {
        $this->attributes['title'] = $title;
        return $this;
    }

    /**
     * @param string $pattern 
     * @return $this 
     */
    public function set_pattern(string $pattern) {
        $this->attributes['pattern'] = $pattern;
        return $this;
    }

    /**
     * @param string|callable $value 
     * @return $this 
     */
    public function set_value($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $value 
     * @return $this 
     */
    public function set_default_value($value) {
        $this->default = $value;
        return $this;
    }    

    /**
     * @param string $value 
     * @return $this 
     */
    public function set_placeholder($placeholder) {
        $this->attributes['placeholder'] = $placeholder;
        return $this;
    }

    /**
     * @param string $text_right 
     * @return $this 
     */
    public function set_text_right(string $text_right){
        $this->text_right = $text_right;
        return $this;
    }

    /**
     * @param string $style 
     * @return $this 
     */
    public function add_style(string $style) {
        $styles = explode(' ', $style);
        $this->styles = array_unique(array_merge($this->styles, $styles)) ;
        return $this;
    }    

    /**
     * @param bool $disabled 
     * @return $this 
     */
    public function set_disabled(bool $disabled) {
        if($disabled) {
            $this->attributes['disabled'] = null;
        } else {
            unset($this->attributes['disabled']);
        }        
        return $this;
    }

    /**
     * @param bool $required 
     * @return $this 
     */
    public function set_required(bool $required) {
        if($required) {
            $this->attributes['required'] = null;
        } else {
            unset($this->attributes['required']);
        }
        
        return $this;
    }

    /**
     * @param callable $validation_cb 
     * @return $this 
     */
    public function set_validation_callbak(callable $validation_cb){
        $this->validation_cb = $validation_cb;
        return $this;
    }

    /** @return mixed  */
    public function validate(){
        if(is_callable($this->validation_cb)){
            return call_user_func($this->validation_cb, $this->value);
        }
        return true;
    }

    /** @return void  */
    public function render_label(){
        ?>
            <label for="<?php echo esc_attr($this->id); ?>" class="rds-field-label"><?php echo esc_html($this->label); ?></label>
        <?php
    }
    
    /** @return void  */
    public function render_description(){
        if($this->description) {
            echo '<p class="rds-field-description description">' . esc_html($this->description) . '</p> ';
        }
    }

    /** @return void  */
    public function render_text_left(){
        if($this->text_left) {
            echo '<span class="rds-text-left">' .	esc_html($this->text_left) . '</span> ';
        }        
    }       
    /** @return void  */
    public function render_text_right(){
        if($this->text_right) {
            echo '<span class="rds-text-right">' .	esc_html($this->text_right) . '</span> ';
        }        
    }    

    /** @return void  */
    protected function render_style(){
        if($this->styles){
            $style = '';
            foreach($this->styles as $style){
                $style .= str_replace(';;', ';', trim($style) . ';') ;
            }
            echo ' style="' . esc_attr( $style ) . '"';
        }        
    }

    /** @return void  */
    public function render(){        
        if(is_callable($this->render_cb)){
            call_user_func($this->render_cb, $this);
            return;
        }        
        ?>
        <tr class="rds-field <?php echo esc_attr($this->id); ?>">
            <th>
                <?php $this->render_label(); ?>
            </th>
            <td>
                <?php 
                $this->render_input(); 
                $this->render_text_right(); 
                $this->render_description();
                ?>
            </td>
        </tr>
        <?php        
    }       
    
    /** @return mixed  */
    protected function get_value(){
        
        if(is_callable($this->value)) {
            $value = call_user_func($this->value, $this);
        } else {
            $value = $this->value;
        }
        
        if(is_null($value) && $this->default){
            $value = $this->default;
        }

        return $value;
    }
}