<?php
namespace Radas\Lib\Elements;

use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Classes\Abstracts\Wrapper;

/** 
 * @package Radas\Lib\Elements 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @inheritDoc
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read array $elements
*/
class Tab extends Wrapper {    

    /** @var string $label */
    protected $label;

    /**
     * @inheritdoc
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->add_class("rds-tab hide");
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
     * @inheritdoc
     */
    public function render(){
        if( is_callable($this->render_cb) ){
            call_user_func($this->render_cb, $this);
            return;
        }

        ?>
        <div <?php $this->render_attributes(); ?>>
            <?php 
                // if($this->label) {
                //     printf('<h2>%s</h2>', esc_html($this->label));
                // }

                if($this->description) {
                    printf('<p class="rds-description description">%s</p>', esc_html($this->description));
                }

                // if elements contain field then wrap it with form table
                foreach($this->elements as $element){
                    if($element instanceof Field) {
                        $form_table = new Form_Table($this->id . '_aft');
                        break;
                    }
                }
                
                if(isset($form_table)){
                    /** @var Form_Table $form_table */
                    foreach($this->elements as $element){
                        $form_table->add_element($element);
                    }
                    $form_table->render();
                } else {
                    /** @var Element $element */
                    foreach($this->elements as $element){
                        $element->render();            
                    }    
                }
            ?>
        </div>
        <?php
    }
}