<?php
namespace Radas\Lib\Elements\Fields;

use Radas\Lib\Classes\Abstracts\Field;

class URL_Field extends Field{
     /** @var bool $is_image default = false */
    protected $is_image = false;
    /**
     * @inheritDoc
     */     
    public function __construct(string $id) {
        $this->type = 'url';
        parent::__construct($id);
    }

    /**
     * @param bool $is_image 
     * @return $this 
     */
    public function set_is_image(bool $is_image){
        $this->is_image = $is_image;
        return $this;
    }
    
    /**
     * @inheritDoc
     */ 
    public function get_sanitized_value(){
        return sanitize_url($this->get_value());
    }
    
    /**
     * @inheritDoc
     */ 
    public function render_input() {  
		?>
        <input type="url" value="<?php echo $this->get_sanitized_value(); ?>" <?php $this->render_attributes(); ?>>
        <?php        
    }
}