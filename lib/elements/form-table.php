<?php
namespace Radas\Lib\Elements;

use Radas\Lib\Classes\Abstracts\Wrapper;
use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Elements\Fields\Hidden_Field;

/** 
 * @package Radas\Lib\Elements 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1
 * 
 * @property-read string $id
 * @property-read array $attributes
 * @property-read array $classes
 * @property-read array $elements
*/
class Form_Table extends Wrapper {    
    
    /**
     * @inheritdoc
     */
    public function __construct(string $id) {
        parent::__construct($id);
        $this->add_class("rds-table form-table");
    }

    /**
     * @inheritdoc
     */
    public function render(){
        if( is_callable($this->render_cb) ){
            call_user_func($this->render_cb, $this);
            return;
        }
        $hiddens = [];
        echo "<table ";
        $this->render_attributes();
        echo "><tbody>";
        /** @var Element $element */
        foreach($this->elements as $element){
            // skip hidden and put at the bottom of table
            if( $element instanceof Hidden_Field ){
                $hiddens[] = $element;
                continue;
            }

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
                    <td colspan="2" class="rg-p-0">
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

        /** @var Hidden_Field $hidden */
        foreach($hiddens as $hidden){
            $hidden->render_input();
        }
    }
}