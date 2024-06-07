<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Helper\Util;

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
class Select_Field extends Field{

    /** @var array $options */
    protected $options;

    /**
     * @inheritDoc
     */ 
    public function __construct(string $id) {
        $this->type = 'select';
        parent::__construct($id);
    }    

    /**
     * @param array|callable $options 
     * @return $this 
     */
    public function set_options(array|callable $options){
        if(is_callable($options)){
            $return_type = Util::get_function_return_type($options); 
            if( $return_type != 'array') {
                trigger_error(
                    sprintf(
                        'RADAS: Invalid callable return type! (%s) your set_options callable should return array for Select field with id "%s"', 
                        esc_html($return_type), 
                        esc_html($this->id)
                    ),
                    E_USER_WARNING
                );
                $options = [];
            }
        }
        
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
    public function render_input() {
        // $required = array_key_exists('required', $this->attributes) || thi;
		?>
        <select <?php $this->render_attributes(); ?>>
            <option <?php echo array_key_exists('required', $this->attributes) ? 'disabled'  : '' ?> value><?php echo esc_html(__('Select...', RADAS_DOMAIN)); ?></option>
            <?php 

            if(is_callable($this->options)){
                $options = call_user_func($this->options);
            } else {
                $options = $this->options;
            }
            
            foreach($options as $key => $label){
                echo '<option value="' . esc_attr($key) .'"' . selected( $key, $this->get_sanitized_value(), false ) . '>' . esc_html($label) .'</option>';
            }
            ?>
        </select>
        <?php          
    }       
}