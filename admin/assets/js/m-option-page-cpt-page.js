import { fieldBox, fBoxList, fBoxInit } from "./m-fieldbox.js";
import { iconBox } from  "../../../lib/assets/js/m-iconbox.js";
import { setErrorMessage } from "../../../lib/assets/js/m-utils.js";
import { elBinder, showSpinner, popupAlert, dialogBox, getFormValue} from "../../../lib/assets/js/m-general.js";
import { adminFlashNotice } from "../../../lib/assets/js/m-admin.js";

window.fBoxList = fBoxList;

const $=jQuery;
const dashiconBox = new iconBox('iconbox');

const dialogShortCode = (fBox)=>{
    const dialog = new dialogBox('dlg-shortcode');
    

    const elShortcode = (id, label, shortCode) => {
        const elDiv = document.createElement('div');
        const elWrapper = document.createElement('div');
        const elText = document.createElement('input');
        const elLabel = document.createElement('label');
        const elButton = document.createElement('button');

        elLabel.textContent = label
        elLabel.setAttribute('for', id);

        elText.id = id;
        elText.value = shortCode;        
        elText.setAttribute('style', 'width: 320px;');
        elText.setAttribute("type", "text");
        elText.readOnly = true;        

        elButton.setAttribute('class', 'no-button dashicons dashicons-clipboard');
        elButton.addEventListener('click', async (e)=>{
            // Select the text field
            elText.select();
            elText.setSelectionRange(0, 99999); // For mobile devices

            try {
                await navigator.clipboard.writeText(elText.value);
            } catch (err) {
                document.execCommand("copy");
            }

            adminFlashNotice('success', 'Copied the text: ' + elText.value);
        });


        elWrapper.setAttribute('style', 'display: flex;align-items: center;column-gap: 4px;');
        elWrapper.append(elText);
        elWrapper.append(elButton);

        elDiv.setAttribute('style', 'display: flex; flex-direction: column; row-gap: 4px; padding: 8px 0;');
        elDiv.append(elLabel);
        elDiv.append(elWrapper);
        
        return elDiv;
    }
    
    dialog.title = 'Code for ' + fBox.elBinded('fbox_title').innerHTML + ' field';

    const shortCode = `[${page.slug} field=${fBox.name}]`
    dialog.appendContent(elShortcode('sc', 'WordPress Shortcode:', `[${page.slug} field=${fBox.name}]`));
    dialog.appendContent(elShortcode('go', 'PHP:', `get_option("${page.slug}")("${fBox.name}")`));
    dialog.addButton('Close');
    dialog.addEventListener('buttonClick', (e)=>{
        e.target.close();
    });

    dialog.addEventListener('close', (e)=>{        
        e.target.node.remove();
        fBox.elBinded('btn_shortcode').focus();
    });

    return dialog;    
}

class optPage extends elBinder {

    node;
    #form = document.querySelector('form'); 

    constructor() {
        super();

        const _this = this;
        
        this.node = document.getElementById('rds-form');
        this.node.querySelectorAll("[data-handle]").forEach((element) => {
            this._bind(element, element.getAttribute('data-handle'));
        });
        
        this.addEventListener('input', (e)=>{
            
            const handle = e.detail.handle;

            if(handle == 'post_id') {
                if(_this.post_id){
                    $('.btn-save-page').html('Update');
                    _this.elBinded('slug').readOnly = true;
                } else {
                    $('.btn-save-page').html('Create');
                    _this.elBinded('slug').readOnly = false;
                }
            }
            if(handle == 'type'){
                const menuPage = (e.detail.value == "menu");    
                _this.elBinded('parent_menu').closest('tr').classList.toggle('hide', menuPage);
                _this.elBinded('menu_title').closest('tr').classList.toggle('hide', !menuPage);
                _this.elBinded('page_icon').closest('tr').classList.toggle('hide', !menuPage);
            }
    
    
            if( handle == 'page_icon' ) {                
                _this.elBinded('btn_icon').removeAttribute('class');
                _this.elBinded('btn_icon').setAttribute('class', 'button button-secondary dashicons-before ' + _this.page_icon);
            }              
        });
    
        this.addEventListener('buttonClick', (e)=>{
            const handle = e.detail.handle;
            if(handle == 'btn_icon'){
                dashiconBox.open();
            }            
        });  

        this.addEventListener('saved', (e)=>{
            _this.post_id = e.detail.id; 

            /** enable shortcode and add event listener on it */
            Object.values(fBoxList.list).forEach((fBox) => {
                _this.#setShortcode(fBox);
            })
            
            _this.setLastData();            
        })
        
        this.elBinded('slug').setAttribute('data-valid-handle', 'page_slug');

        this.#iconBoxInit();    

        fBoxInit();
    
        // Listen for input events on the form
        this.#form.addEventListener('input', this.#onInput);   
        // document.querySelector('form').addEventListener('input', this.#onInput);   

        $('.btn-save-page').on('click', ()=>{
            _this.saveData();
        });        
    }
    
    #lastData;
    setLastData(){
        this.#lastData = getFormValue(this.#form);
    }

    saveData() {        
        const _this = this;
        
        showSpinner(true);   
        //nanti expand dulu
        $(".rds-input:visible").each(function(){
            inputValidation(this);
        });

        // validasi fbox yang ketutup
        Object.values(fBoxList.list).forEach((fBox) => {
            if(!fBox.node.classList.contains('expanded')){
                fBox.node.classList.add('expanded');
                const $fBox = $(fBox.node); 
                $fBox.find('.rds-input:visible').each(function(){
                    inputValidation(this);
                });
                // kalau tidak ada error tutup lagi
                if($fBox.find(".rds-error-message:visible").length == 0) {
                    fBox.node.classList.remove('expanded');
                }
            }
        })        
    
        if($(".rds-error-message:visible").length != 0) {
            popupAlert('Data Validation', 'Save failed, please check your input!');
            showSpinner(false);
            return;
        }
    
        const opt_page = {
            title : this.title,
            slug : this.slug,
            description : this.description,
            capability : this.capability,
            type : this.type,
            parent : this.parent_menu,
            menu_title : this.menu_title,
            icon : this.page_icon,
            position : this.position,
            fields : [
                {
                    type : 'form_table',
                    name : this.slug + "_aft",
                    fields : fBoxList.getValues(),
                },            
            ],
        }

        const url = wpApiSettings.root + this.#form.getAttribute('data-endpoint') + (this.post_id ? '/' + this.post_id : '');
        const method = this.post_id ? 'put' : 'post';    
        $.ajax({ 
            url: url, 
            method: method,          
            data: JSON.stringify(opt_page), 
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },            
            success: (response) => {
                
                _this.dispatchEvent(new CustomEvent('saved',{
                    detail: { 
                        id: response.data.id,
                        json: opt_page,
                    },
                }));

                // _this.post_id = response.data.id; 

                // /** enable shortcode and add event listener on it */
                // Object.values(fBoxList.list).forEach((fBox) => {
                //     _this.#setShortcode(fBox);
                // })

                // _this.setLastData();

                showSpinner(false);

                adminFlashNotice('success', response.message);
            }, 
            error: (xhr) => {
                showSpinner(false);

                if(xhr.hasOwnProperty('responseJSON') && xhr.responseJSON.hasOwnProperty('message')){
                    popupAlert('Save Failed', xhr.responseJSON.message);
                } else {
                    adminFlashNotice('error', xhr.responseText, 10000);
                }
            } 
        });
    } 
    
    fillData (id, data) {
        try {
            this.post_id = id;
            this.title = data.title;
            this.slug = data.slug;
            this.description = data.description;
            this.capability = data.capability;
            this.type = data.type;
            this.parent_menu = data.parent;
            this.menu_title = data.menu_title;
            this.page_icon = data.icon;
            this.position = data.position;
        
            data.fields[0].fields.forEach((field)=>{
                this.#createFBox(field, 0);
            })
        } catch (e) {
            console.error(e.message);
        }        
    }

    #createFBox (field, parentID) {
        const fBox = new fieldBox();

        // ini harus sebelum set options dan attributes, karena harus wrapped dulu
        fBox.parentID = parentID;

        // ini harus sebelum set fBox.type supaya nggak buat 2 option kosong
        field.options.forEach((option)=>{
            fBox.addOption(option);
        })

        field.attributes.forEach((attribute)=>{
            fBox.addAttribute(attribute);
        })        

        fBox.type = field.type;    
        fBox.name = field.name;
        fBox.label = field.label;
        fBox.description = field.description;
        fBox.default_value = field.default_value;
        fBox.classes = field.classes;

        field.fields.forEach((field)=>{
            this.#createFBox(field, fBox.id);
        })

        this.#setShortcode(fBox);        
    } 

    #setShortcode(fBox) {        
        if(!fBox.elBinded('btn_shortcode').disabled) return;

        fBox.elBinded('btn_shortcode').disabled = false;
        fBox.addEventListener('buttonClick', (e)=>{
            if(e.detail.handle == 'btn_shortcode'){
                dialogShortCode(fBox).open();
            }
        });        
    }

    #iconBoxInit() {
        const _this = this;
        dashiconBox.addEventListener('open', (e)=>{
            if(_this.page_icon){
                e.target.elBinded(_this.page_icon).focus();
            }
        });
    
        dashiconBox.addEventListener('close', (e)=>{
            _this.elBinded('btn_icon').focus();
        });        
    
        dashiconBox.addEventListener('iconClick', (e)=>{
            _this.page_icon = e.detail.icon
            e.target.close();
        })
    }
    
    #onInput(e) {
        inputValidation(e.target);
    }

    clear() {
        this.post_id = '';
        this.title = '';
        this.slug = '';
        this.description = '';
        this.capability = 'manage_options';
        this.type = 'menu';
        this.parent_menu = ""
        this.menu_title = '';
        this.page_icon = 'dashicons-admin-generic';
        this.position = '';
        this.slug = '';
    
        Object.keys(fBoxList.list).forEach((fBox)=>{
            fBoxList.remove(fBox);
        })

        $('.rds-error-message').html('');
    }
    
    show() {
        $('#rds-form').removeClass('hide');
        $('.btn-warpper-header').removeClass('hide');
    
        this.elBinded('title').focus();
        this.setLastData();
    }  

    #dialogClose() {
        const dialog = new dialogBox('dlg-close-page');
        dialog.title = "Close Confirmation";
        dialog.content = "Are you sure that you want to close the current page? The changes that you made won't be saved."
    
        dialog.addButton(['Yes', 'No']);
        dialog.addEventListener('buttonClick', (e)=>{
            if(e.detail.text == 'Yes'){
                $('#rds-list').removeClass('hide');
                $('.btn-add-page').removeClass('hide');

                $('#rds-form').addClass('hide');
                $('.btn-warpper-header').addClass('hide');                
            }
            e.target.close();
        });
    
        dialog.addEventListener('close', (e)=>{        
            e.target.node.remove();
        });
    
        return dialog;    
    }

    close() {
        if(this.#lastData != getFormValue(this.#form)){
            this.#dialogClose().open();
        } else {
            $('#rds-list').removeClass('hide');
            $('.btn-add-page').removeClass('hide');
                            
            $('#rds-form').addClass('hide');
            $('.btn-warpper-header').addClass('hide');                
        }
    }    
}

const inputValidation= (input) => {
    setErrorMessage(input);
    const handle = input.getAttribute('data-valid-handle');
    
    if(handle == 'page_slug'){            
        let regex = /^\w+$/;
        if (input.value  != "" && !regex.test(input.value)) {
            setErrorMessage(input, 'This field may only contain letters, numbers, and underscores');
            return false;
        }
    }

    if(handle == 'field_name'){
        input.setAttribute('data-val', input.value);        
        
        let regex = /^\w+$/;
        if (input.value != "" && !regex.test(input.value)) {
            setErrorMessage(input, 'This field may only contain letters, numbers, and underscores');
            return false;
        }

        const duplicateId = document.querySelectorAll('[data-valid-handle="'+handle+'"][data-val="'+input.value+'"]');
        if (duplicateId.length > 1) {
            duplicateId.forEach((duplicateId_)=>{
                duplicateId_.setAttribute('data-duplicate', input.value); 
                setErrorMessage(duplicateId_, 'This field cannot be duplicates');
            })
            return false;
        } else {
            const dataDuplicate = input.getAttribute("data-duplicate");            
            if( dataDuplicate ) {
                input.removeAttribute("data-duplicate");
                const prevDuplicate = document.querySelectorAll( '[data-valid-handle="'+handle+'"][data-val="'+dataDuplicate+'"]' );
                if(prevDuplicate.length == 1){
                    prevDuplicate.forEach((prevDuplicate_)=>{
                        prevDuplicate_.removeAttribute('data-duplicate');
                        setErrorMessage(prevDuplicate_);
                    })
                }
            }
        }
    }

    // general validation
    if (!input.checkValidity()) {
        setErrorMessage(input, input.validationMessage);
    }           
}    
const page = new optPage();
export {optPage, page}