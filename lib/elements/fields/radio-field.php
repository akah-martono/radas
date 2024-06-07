<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Classes\Abstracts\Field;

/** 
 * @package Radas\Lib\Fields 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read string $type
 * @property-read string $label
 * @property-read string $default
 * @property-read string $description 
 * @property-read string $text_right
*/
class Radio_Field extends Field{

    /** @var array $options */
    protected $options;

    /**
     * @inheritDoc
     */ 
    public function __construct(string $id) {
        $this->type = 'radio';
        parent::__construct($id);
    }    

    /**
     * @param array $options 
     * @return $this 
     */
    public function set_options(array $options){
        $this->options = $options;
        return $this;
    }

    /**
     * @inheritDoc
     */ 
    public function get_sanitized_value(){
        return sanitize_text_field($this->get_value());
    }
    /**
     * @inheritDoc
     */ 
    public function render_input() { //nanti dicek lagi
        $i=0;
        echo '<div id="' . esc_attr($this->id) . '" style="display:flex;gap:2em;">';
        foreach($this->options as $key => $label){
            $i+=1;            
            echo '<span>
            <input type="radio" id="' . esc_attr($this->id . $i) . '" name="' . esc_attr($this->id) . '" value="' . esc_attr($key) .'" ' . checked( $key, $this->get_sanitized_value(), false ) . '> 
            <label for="' . esc_attr($this->id . $i) . '">' . esc_html($label) .'</label>
            </span>';
        }
        echo '</div>';
    }       
}