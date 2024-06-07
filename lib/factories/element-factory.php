<?php
namespace Radas\Lib\Factories;

use Radas\Lib\Classes\Abstracts\Element;
use Radas\Lib\Classes\Abstracts\Field;
use Radas\Lib\Classes\Abstracts\Wrapper;
use Radas\Lib\Elements\Container;
use Radas\Lib\Elements\Form_Table;
use Radas\Lib\Elements\Tab;
use Radas\Lib\Elements\Tabs;
use Radas\Lib\Elements\Fields\Checkbox_Field;
use Radas\Lib\Elements\Fields\Date_Field;
use Radas\Lib\Elements\Fields\Number_Field;
use Radas\Lib\Elements\Fields\Phone_Field;
use Radas\Lib\Elements\Fields\Radio_Field;
use Radas\Lib\Elements\Fields\Select_Field;
use Radas\Lib\Elements\Fields\Text_Field;
use Radas\Lib\Elements\Fields\TextArea_Field;
use Radas\Lib\Elements\Fields\URL_Field;
use Radas\Lib\Classes\Trait\Singleton;
use Radas\Lib\Elements\Fields\Color_Field;
use Radas\Lib\Elements\Fields\Editor_Field;
use Radas\Lib\Elements\Fields\Media_Field;
use Radas\Lib\Elements\Fields\Password_Field;

class Element_Factory {    
    use Singleton;

    public function __invoke(array $params): null|Element {

        // print_r($params);
        // die();
        
        if( $params['type'] == 'checkbox' ) {
            $element = new Checkbox_Field($params['name']);
        }
        if( $params['type'] == 'color' ) {
            $element = new Color_Field($params['name']);
        }
        if( $params['type'] == 'date' ) {
            $element = new Date_Field($params['name']);
        }
        if( $params['type'] == 'number' ) {
            $element = new Number_Field($params['name']);
        }
        if( $params['type'] == 'password' ) {
            $element = new Password_Field($params['name']);
        }        
        if( $params['type'] == 'phone' ) {
            $element = new Phone_Field($params['name']);
        }
        if( $params['type'] == 'radio' ) {
            $element = new Radio_Field($params['name']);
        }
        if( $params['type'] == 'select' ) {
            $element = new Select_Field($params['name']);
        }
        if( $params['type'] == 'text' ) {
            $element = new Text_Field($params['name']);
        }
        if( $params['type'] == 'textarea' ) {
            $element = new TextArea_Field($params['name']);
        }
        if( $params['type'] == 'url' ) {
            $element = new URL_Field($params['name']);
        }
        if( $params['type'] == 'media' ) {
            $element = new Media_Field($params['name']);
        }
        if( $params['type'] == 'editor' ) {
            $element = new Editor_Field($params['name']);
        }
        if( $params['type'] == 'container' ) {
            $element = new Container($params['name']);
        }
        if( $params['type'] == 'tabs' ) {
            $element = new Tabs($params['name']);
        }
        if( $params['type'] == 'tab' ) {
            $element = new Tab($params['name']);
        }
        if( $params['type'] == 'form_table' ) {
            $element = new Form_Table($params['name']);
        }
        if( $params['type'] == 'container' ) {
            $element = new Container($params['name']);
        }        
        
 
        if( !isset($element) ) return null;        

        if( array_key_exists('label', $params) ) {
            $element->set_label($params['label']);
        }

        if( array_key_exists('description', $params) ) {
            $element->set_description($params['description']);
        }

        if( array_key_exists('attributes', $params) ) {
            foreach( $params['attributes'] as $attribute ) {
                $element->add_attributes($attribute['name'], $attribute['value']);
            }
        }

        if( array_key_exists('classes', $params) && $params['classes']) {
            // $element->remove_class('regular-text');
            $element->add_class($params['classes']);
        }

        /** @var Field $element*/
        if( $element instanceof Field) {
            $has_options = ['select', 'radio'];
    
            if( array_key_exists('default_value', $params) && $params['default_value']) {
                $element->set_default_value($params['default_value']);
            }    

            if( array_key_exists('options', $params) && in_array($params['type'], $has_options)) {
                /** @var Select_Field | Radio_Field $element*/
                $options = [];
                foreach($params['options'] as $option) {
                    $options[$option['value']] = $option['text'];
                    if($option['default']) {
                        $element->set_default_value($option['value']);
                    }
                }
                $element->set_options($options);
            }
        }

       
        /** @var Wrapper $element*/
        if( $element instanceof Wrapper ){
            if( array_key_exists('fields', $params) ) {
                foreach($params['fields'] as $child){
                    $el_child = $this($child);                       
                    if( $el_child instanceof Element) {
                        $element->add_element($el_child);
                    }
                }
            }    
        }

        // /** @var Tabs $element*/
        // if( $element instanceof Tabs ){
        //     if( array_key_exists('fields', $params) ) {
        //         foreach($params['fields'] as $tab){
        //             $el_child = $this($tab);
        //             if( $el_child instanceof Tab ) {
        //                 $element->add_tab($el_child);
        //             }
        //         }
        //     }    
        // }        
        return $element;
    }
 }