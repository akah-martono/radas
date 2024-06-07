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
class Email_Field extends Field{
    /** @var bool $enable_avatar default = false */
    protected $enable_avatar = false;

    /**
     * @inheritDoc
     */      
    public function __construct($id) {
        $this->type = 'email';
        parent::__construct($id);
    }

    /**
     * @param bool $enable_avatar 
     * @return $this 
     */
    public function set_enable_avatar($enable_avatar){
        $this->enable_avatar = $enable_avatar;
        return $this;
    }    
    
    /**
     * @inheritDoc
     */  
    public function get_sanitized_value(){
        return sanitize_email($this->get_value());
    }      
    
    /**
     * @inheritDoc
     */ 
    public function render_input() {
		?>
        <input type="email" value="<?php echo $this->get_sanitized_value(); ?>" <?php $this->render_attributes(); ?>>
        <?php        
    }    
}