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
class Number_Field extends Field{
    /** @var string $number_type */
    protected $number_type = 'int';

    /**
     * @inheritDoc
     */ 
    public function __construct(string $id) {
        $this->type = 'number';
        parent::__construct($id);
    }

    /**
     * @param int|float $min 
     * @return $this 
     */
    public function set_min(int|float $min){
        $this->attributes['min'] = $min;
        return $this;
    }

    /**
     * @param int|float $max 
     * @return $this 
     */
    public function set_max(int|float $max){
        $this->attributes['max'] = $max;
        return $this;
    }

    /**
     * @param int|float $step 
     * @return $this 
     */
    public function set_step(int|float $step){
        $this->attributes['step'] = $step;
        return $this;
    }

    /**
     * @param string $number_type 
     * @return $this 
     */
    public function set_number_type(string $number_type){
        $this->number_type = $number_type;
        return $this;
    }

    /**
     * @inheritDoc
     */  
    public function get_sanitized_value(){
        $value = $this->get_value(); 
        if (is_null($value))return $value;

        if($this->number_type  == 'int'){
            $value = intval($value);
        }else{
            $value = floatval($value);
        }

        if(array_key_exists('min', $this->attributes) ){
            if( $value < floatval($this->attributes['min']) ){
                $value = floatval($this->attributes['min']);
            }
        }

        if(array_key_exists('max', $this->attributes)){
            if( $value > floatval($this->attributes['max']) ){
                $value = floatval($this->attributes['max']);
            }
        }        
        
        return $value;
    }
    
    /**
     * @inheritDoc
     */ 
    public function render_input() {
		?>
        <input type="number" value="<?php echo esc_attr($this->get_sanitized_value()); ?>" <?php $this->render_attributes(); ?>>
        <?php            
    }
}