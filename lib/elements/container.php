<?php
namespace Radas\Lib\Elements;

use Radas\Lib\Classes\Abstracts\Wrapper;
use Radas\Lib\Classes\Abstracts\Element;
use Radas\Lib\Classes\Abstracts\Field;

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
class Container extends Wrapper {    
    public function renderx(){
        if( is_callable($this->render_cb) ){
            call_user_func($this->render_cb, $this);
            return;
        }

        if($this->wrapper == 'div') {
            $this->render_div();
            return;
        }

        if($this->wrapper == 'form_table') {
            $this->render_form_table();
            return;
        }
    }

    public function render(){
        $this->add_class("rds-container");
        
        if( is_callable($this->render_cb) ){
            call_user_func($this->render_cb, $this);
            return;
        }

        echo "<div "; 
        $this->render_attributes(); 
        echo ">";        

        if($this->label) {
            printf('<h2>%s</h2>', esc_html($this->label));
        }

        if($this->description) {
            printf('<p class="rds-description description">%s</p>', esc_html($this->description));
        }

        foreach($this->elements as $element){
            if($element instanceof Field) {
                $form_table = new Form_Table($this->id . '_aft');
                break;
            }
        }
        
        // if elements contain field then wrap it with form table
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

        echo "</div>";
    }    

    public function render_x(){
        $this->add_class("rds-container");
        
        if( is_callable($this->render_cb) ){
            call_user_func($this->render_cb, $this);
            return;
        }

        echo "<div "; 
        $this->render_attributes(); 
        echo ">";        

        if($this->label) {
            printf('<h2>%s</h2>', esc_html($this->label));
        }

        if($this->description) {
            printf('<p class="rds-description description">%s</p>', esc_html($this->description));
        }

        foreach($this->elements as $element){
            /** @var Field $element */
            if($element instanceof Field) {
                ?>
                <div class="rds-field <?php echo esc_attr($element->id); ?>">
                    <div class="rds-label-wrapper">
                        <?php $element->render_label(); ?>
                    </div>
                    <div class="rds-input-wrapper">
                        <?php 
                        $element->render_input(); 
                        $element->render_text_right(); 
                        $element->render_description();
                        ?>
                    </div>                    
                </div>
                <?php
            } else {
                /** @var Element $element */
                $element->render();
            }
        }
        echo "</div>";
    }        
}