<?php
namespace Radas\Lib\Classes\Abstracts;

use Radas\Lib\Classes\Abstracts\Element;

/** 
 * @package Radas\Lib\Classes\Abstracts
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @inheritDoc
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read array $elements
*/
abstract class Wrapper extends Element {    
    /**
     * @var array $elements
     */
    protected $elements = [];

    /**
     * @var string
     */
    protected $wrapper = "form_table";
        
    /**
     * @param Element $element 
     * @return $this 
     */
    public function add_element(Element $element){
        $this->elements[$element->id] = $element;
        return $this;
    }

    public function set_wrapper($wrapper){
        $allowed = ['div', 'form_table'];
        if(in_array($wrapper, $allowed)){
            $this->wrapper = $wrapper;
        }
    }

    protected function render_div(){
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
                <div class="rds-field <?php $element->id ?>">
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

    protected function render_form_table(){
        $this->add_class("rds-table form-table");

        echo "<table ";
        $this->render_attributes();
        echo "><tbody>";
        /** @var Element $element */
        foreach($this->elements as $element){
            /** @var Field $element */
            if($element instanceof Field) {
                ?>
                <tr class="rds-field <?php echo esc_attr($element->id); ?>">
                    <th>
                        <?php $element->render_label(); ?>
                    </th>
                    <td>
                        <?php 
                        $element->render_input(); 
                        $element->render_text_right(); 
                        $element->render_description();
                        ?>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr class="rds-field <?php echo esc_attr($element->id); ?>">
                    <td colspan="2">
                        <?php 
                        /** @var Element $element */
                        $element->render();
                        ?>
                    </td>
                </tr>
                <?php
            }        
        }
        echo "</tbody></table>";        
    }

    protected function render_fieldset(){
        echo "<div "; 
        $this->render_attributes(); 
        echo ">";        
        echo "<fieldset>";
            if($this->label) {
                printf('<legend>%s</legend>', esc_html($this->label));
            }
            if($this->description) {
                printf('<p class="rds-description description">%s</p>', esc_html($this->description));
            }
            foreach($this->elements as $element){
                /** @var Field $element */
                if($element instanceof Field) {
                    ?>
                    <div class="rds-field <?php $element->id ?>">
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
        echo "</fieldset>";
        echo "</div>";
    }    
}