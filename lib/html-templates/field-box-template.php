<?php
namespace Radas\Lib\HTML_Templates;

/** 
 * @package Radas\Lib\HTML_Templates 
 * @author Akah <akah@vaks.in>
 * @since 0.0.1 
 * */
class Field_Box_Template {
    public static function register(){
        add_action('admin_footer', function(){
            // Field_Box_Template::wrapper();
            Field_Box_Template::field_box();
            Field_Box_Template::field_attribute_row();
            Field_Box_Template::field_option_row();
        });        
    }
    public static function wrapper() {
        ?>
        <template id="fieldbox-wrapper-template">
            <div id="rds-fields-box-wrapper">
                <div class="rds-fields-header rm-pb-8 rg-mt-8">
                    <div class="rds-fields-title">
                        <h3>Fields</h3>
                        <button class="btn-new-field button button-secondary dashicons-before dashicons-plus"> New Field</button>
                    </div>
                    <div class="button-group expand-collapse-wrapper hide">
                        <button class="button button-secondary btn-expand-all">Expand All</button>
                        <button class="button button-secondary btn-collapse-all">Collapse All</button>                    
                    </div>                
                </div>
                <div class="rds-fields-content">
                    <div class="rds-field-boxes"></div>
                    <div class="rds-btn-add-wrapper rds-btn-add-footer rg-pt-8"></div>
                </div>
            </div>            
        </template>
        <?php
    }

    public static function field_box(){        
        ?>
        <template id="fieldbox-template">
            <div id="[fbid]" class="rds-field-box">
                <div class="el-wrapper">
                    <div class="rds-field-box-header rg-border">
                        <div class="rds-field-box-title">
                            <div class="move-handle-wrapper">
                                <button title="Move" class="no-button move-handle-icon dashicons dashicons-move" data-move-el=".rds-field-box" tabindex="0" data-fbox-handle="move_handle"></button>
                            </div>
                            <div class="rds-field-box-title-text" data-fbox-handle="title_wrapper" data-clickable>
                            <h2 data-fbox-handle="fbox_title"></h2>
                            <span class="rds-field-attr">&nbsp;(<span class="field-type" data-fbox-handle="fbox_type"></span>)<span class="field-mandatory"></span></span>
                            </div>
                        </div>
                        <div class="rds-field-box-icon-wrapper">
                            <button class="no-button remove-field dashicons dashicons-trash" title="Remove field" data-fbox-handle="btn_remove_field"></button>
                            <button class="no-button field-shortcode type-condition dashicons dashicons-shortcode pointer" title="Show shortcodes" data-fbox-handle="btn_shortcode"  data-type-handle="shortcode" disabled></button>
                            <button class="no-button btn-expanse-collapse dashicons" data-fbox-handle="btn_expanse_collapse"></button>
                        </div>
                    </div>
                    <div class="rds-field-box-inside">
                        <div class="rg-py-24 rg-border rg-border-t-0" style="display: flex;">
                            <div class="rg-basis-full rd-basis-1/2 rg-px-16 rd-px-32">
                                <table class="rds-table form-table"><tbody>
                                    <tr class="rds-field field_type">
                                        <th><label for="field_type-[fbid]">Type</label></th>
                                        <td>
                                            <select id="field_type-[fbid]" class="rds-input regular-text" required data-fbox-handle="type">
                                                <option value="" class="hide">Select</option>
                                                <optgroup label="Basic">                                                    
                                                    <option value="checkbox">Checkbox</option>
                                                    <option value="color">Color</option>
                                                    <option value="date">Date</option>
                                                    <option value="number">Number</option>
                                                    <option value="email">Email</option>
                                                    <option value="password">Password</option>
                                                    <option value="phone">Phone</option>
                                                    <option value="radio">Radio</option>
                                                    <option value="select">Select</option>
                                                    <option value="text">Text</option>
                                                    <option value="textarea">Text Area</option>
                                                    <option value="url">URL</option>
                                                </optgroup>
                                                <optgroup label="WordPress">
                                                    <option value="media">Media (Image)</option>
                                                    <option value="editor">Editor</option>
                                                </optgroup>
                                                <optgroup label="Wrapper">
                                                    <option value="container">Container</option>
                                                    <option value="tabs">Tabs</option>
                                                    <option value="tab" disabled>Tab</option>
                                                </optgroup>
                                            </select>
                                        </td>
                                    </tr>                    
                                    <tr class="rds-field field_label" data-type-handle="label">
                                        <th><label for="field_label-[fbid]">Label</label></th>
                                        <td>
                                            <input type="text" id="field_label-[fbid]" class="rds-input regular-text" data-fbox-handle="label">
                                        </td>
                                    </tr>
                                    <tr class="rds-field field_id" data-type-handle="name">
                                        <th>
                                            <label for="field_id-[fbid]">Name
                                                <!-- <div class="rds-icon magic-wand btn-generate-id" title="Click here to generate unique name based on label"></div> -->
                                                <button class="no-button dashicons dashicons-arrow-right-alt btn-generate-id" title="Click here to generate unique name" data-fbox-handle="btn_generate_id"></button>
                                            </label>
                                        </th>
                                        <td>
                                            <input type="text" id="field_id-[fbid]" class="rds-input regular-text" required data-fbox-handle="name">                                        
                                        </td>
                                    </tr>
                                    <tr class="rds-field field_description" data-type-handle="description">
                                        <th><label for="field_description-[fbid]">Description</label></th>
                                        <td>
                                            <textarea id="field_description-[fbid]" class="rds-input regular-text" data-fbox-handle="description"></textarea>
                                        </td>
                                    </tr>                                 
                                </tbody></table>                
                            </div>
                            <div class="rg-basis-full rd-basis-1/2 rg-px-16 rd-px-32">
                            <table class="rds-table form-table"><tbody>
                                    <tr class="rds-field field_default_value type-condition" data-type-handle="default_value">
                                        <th><label for="field_default_value-[fbid]">Default Value</label></th>
                                        <td>
                                            <input type="text" id="field_default_value-[fbid]" class="rds-input regular-text" data-fbox-handle="default_value">                                        
                                        </td>
                                    </tr>
                                    <tr class="rds-field field_class" data-type-handle="classes">
                                        <th><label for="field_class-[fbid]">Classes</label></th>
                                        <td>
                                            <textarea id="field_class-[fbid]" class="rds-input regular-text" data-fbox-handle="classes"></textarea>
                                        </td>
                                    </tr>                                                           
                                </tbody></table>                          
                            </div>
                        </div>
                        <div class="rds-field-options type-condition rg-py-32 rg-px16 rd-px-32 rg-border rg-border-t-0" data-type-handle="options">
                            <div class="rds-field-option-header">
                                <h3>Options</h3>
                            </div>
                            <table class="rds-field-options-table rg-mt-8">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="clear-opt-default" title="Click here to clear the default">
                                            <button class="no-button" data-fbox-handle="btn_clear_opt_default">Default</button>
                                        </th>
                                        <th class="opt-value">Value</th>
                                        <th class="opt-text">Text</th>
                                        <th class="opt-remove"></th>
                                    </tr>
                                </thead>
                                <tbody class="rds-field-option-list">
                                </tbody>
                            </table>
                            <div class="rds-field-option-footer rg-pt-16">
                                <button class="btn-add-option button button-small button-secondary" data-fbox-handle="btn_add_option">New Option</button>                
                            </div>
                        </div>
                        <div class="rds-field-attributes rg-py-32 rg-px-16 rd-px-32 rg-border rg-border-t-0">
                            <div class="rds-field-attribute-header">
                                <h3>Attributes</h3>
                                <button class="btn-add-attribute button button-small button-secondary" data-fbox-handle="btn_add_attribute">New Attribute</button>                             
                            </div>
                            <table class="rds-field-attributes-table rg-mt-8 hide">
                                <thead>
                                    <tr>
                                        <th class="att-move"></th>
                                        <th class="att-name">Name</th>
                                        <th class="att-value">Value</th>
                                        <th class="att-remove"></th>
                                    </tr>
                                </thead>
                                <tbody class="rds-field-attribute-list">
                                </tbody>
                            </table>
                            <div class="rds-field-attribute-footer hide-empty rg-pt-16"></div>
                        </div>
                    </div>
                </div>
                <div class="rds-group-field type-condition rg-p-16 rd-p-24 rg-pr-0 rd-pr-0 rd-pb-8 rg-pb-0" data-type-handle="fields">
                    <div class="rds-field-boxes"></div>      
                    <div class="rds-btn-add-wrapper rg-pt-8">
                        <button class="btn-group-new-field button button-secondary dashicons-before dashicons-plus" data-fbox-handle="btn_add_field"> New Field</button>
                    </div>                          
                </div>
            </div>  
        </template>
        <?php
    }    

    public static function field_option_row(){        
        ?>
        <template id="field-option-row-template">
            <tr id="[optid]" class="rds-field field_options">
                <td class="opt-default">
                    <input type="radio" id="opt_default-[optid]" name="opt_default-[fbid]" value="[optid]" class="rds-input" data-opt-handle="default">
                </td>        
                <td class="opt-move move-handle">
                    <span title="Move" class="move-handle-icon dashicons dashicons-move" data-move-el=".rds-field.field_options" tabindex="0"></span>
                </td>                
                <td class="opt-value">
                    <input type="text" id="opt_value-[optid]" class="rds-input large-text" required data-opt-handle="value">
                </td>
                <td class="opt-text">
                    <input type="text" id="opt_text-[optid]" class="rds-input large-text" required data-opt-handle="text">
                </td>
                <td class="opt-remove">
                    <button title="Remove" class="no-button remove-option dashicons dashicons-remove" data-optid="[optid]"></button>
                </td>
            </tr>
        </template>
        <?php
    }       

    public static function field_attribute_row(){        
        ?>
        <template id="field-attribute-row-template">
            <tr id="[attid]" class="rds-field field_attributes">
                <td class="att-move move-handle">
                    <span title="Move" class="move-handle-icon dashicons dashicons-move" data-move-el=".rds-field.field_attributes" tabindex="0"></span>
                </td>
                <td class="att-name">
                    <input type="text" id="att_name-[attid]" class="rds-input large-text" data-att-handle="name" required>
                </td>
                <td class="att-value">
                    <input type="text" id="att_value-[attid]" class="rds-input large-text" data-att-handle="value" required>
                </td>
                <td class="att-remove">
                    <button title="Remove" class="no-button remove-attr dashicons dashicons-remove" data-attid="[attid]"></button>
                </td>
            </tr>
        </template>
        <?php
    }        
}