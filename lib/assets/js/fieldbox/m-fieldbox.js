import { fBoxList } from "./m-fboxlist.js"; // ini harus paling atas, karena ada init
import { getCounter, dialogBox, getHTMLTemplate, animateCollapse, eventList, elBinder, animate, addCSSLink } from "../general/m-general.js";
import { keydownMoveIcon } from "../m-utils.js";
import { fBoxMoveUp, fBoxMoveDown, keydownMoveIconFBox, clickAddNew, createFBox } from "./m-functions.js";
import { types } from "./m-types.js";
import "./m-sortable.js"; // fBox sortable using jQuery 

const fBoxStyleURL = new URL('./fieldbox.css', import.meta.url);
addCSSLink('rds-fieldbox-style', fBoxStyleURL);    

// make the list only contain instanceof fieldBox
fBoxList.addEventListener('add', (e) => {
    if(!e.detail.item instanceof fieldBox) {
        e.preventDefault();
    }
})

class fieldBox extends elBinder {
    node;

    #typeHandles = {};
    #options = new eventList();
    #attributes = new eventList();
    
    #id;
    get id(){
        return this.#id;
    }

    get options(){
        let options = {};
        if(types[this.type].fields.includes('options')) {
            for (const option of this.node.querySelector('.rds-field-option-list').children) {
                options[option.id] = this.#options.get(option.id); 
            }     
        }
        return options;
    }

    get attributes(){
        let attributes = {};
        for (const attribute of this.node.querySelector('.rds-field-attribute-list').children) {
            attributes[attribute.id] = this.#attributes.get(attribute.id); 
        }
        return attributes;
    }

    get wrappers() {
        return types[this.type].wrappers;
    }

    get fields(){
        let children = {};
        if(types[this.type].fields.includes('fields')) {
            for (const child of this.node.querySelector('.rds-field-boxes').children) {
                children[child.id] = fBoxList.get(child.id);
            }    
        }
        return children;
    }      

    /**
     * @param {string} id
     */
    set parentID(id=0) {
        let wrapper = null;        

        if(id) {
            wrapper = document.querySelector("#"+ id +" > .rds-group-field > .rds-field-boxes");    
        }
        
        if(!wrapper) {
            wrapper = document.querySelector(".rds-fields-content > .rds-field-boxes");
        }

        wrapper.appendChild(this.node);

        /** start pindahan */
        const parent = fBoxList.get(id);
        if(parent){
            // untuk container parent langsung disable
            if( ["tabs", "tab", "container"].includes(parent.type) ) {                    
                parent.elBinded('type').disabled = true;
                parent.elBinded('type').classList.add('rds-bold');
            }

            // Tabs hanya boleh punya child tab saja
            if(parent.type == "tabs") {
                this.type='tab';
                this.elBinded('type').disabled = true;
                this.elBinded('type').classList.add('rds-bold');
            }                              
        }
        /** end pindahan */

        const _this = this;
        fBoxList.dispatchEvent (new CustomEvent("added", {
            bubbles: true,
            detail: { item: _this },
          }),
        );         
    }

    get parentID() {
        const parent = this.node.parentElement.closest('.rds-field-box');
        return (null === parent) ? 'root' : parent.id;
    }

    constructor() {
        super();
        this.#id =  'fb'+getCounter();
        
        this.#init();
        this.node.querySelectorAll("[data-fbox-handle]").forEach((element) => {
            this._bind(element, element.getAttribute('data-fbox-handle'));
        });          
        
        this.node.querySelectorAll("[data-type-handle]").forEach((element) => {
            this.#typeHandles[element.getAttribute('data-type-handle')] = element;
        }); 

        this.#setupEvent();
        // set validation handle
        this.elBinded('name').setAttribute('data-valid-handle', 'field_name');        


        fBoxList.add(this);   
    }

    #init(){
        const _this = this;
        
        this.node = getHTMLTemplate('fieldbox-template');
        this.node.id = this.id;
        this.node.innerHTML = this.node.innerHTML.replaceAll("[fbid]", this.id);      
    }

    addOption(optData = null){
        const opt = new optionRow(this.id);
        if(optData instanceof Object){
            opt.default = optData.default;
            opt.value = optData.value;
            opt.text = optData.text;    
        }
        this.#options.add(opt);
        return opt;
    }

    addAttribute(attData = null){
        const att = new attributeRow(this.id);
        if(attData instanceof Object){
            att.name = attData.name;
            att.value = attData.value;
        }
        this.#attributes.add(att); 
        return att;       
    }    

    getValues(){
        const props = {
            type: this.type,
            name : this.name,
            label : this.label,
            description : this.description,
            default_value : this.default_value,
            classes : this.classes,
            options : [],
            attributes: [],
            fields: [],
        };

        const withOption = ["radio", "select"];
        if( withOption.includes(props.type) ){
            Object.values(this.options).forEach((opt)=> {
                props.options.push({
                    default: opt.default,
                    value: opt.value,
                    text: opt.text,
                });
            })
        }        

        Object.values(this.attributes).forEach((att)=> {
            props.attributes.push({
                name: att.name,
                value: att.value,
            });
        })

        const group = ["tabs", "tab", "container" ];
        if( group.includes(props.type) ) {
            for (const field of this.node.querySelector('.rds-field-boxes').children) {
                props.fields.push(fBoxList.get(field.id).getValues());
            }
        }

        return props;       
    }

    /**
     * @param {string} selector
     */ 
    selects(selector){
        return this.node.querySelectorAll('.el-wrapper ' + selector);
    }

    /**
     * @param {string} selector
     */ 
    select(selector){
        return this.node.querySelector('.el-wrapper ' + selector);
    }

    remove(){
        const _this = this;  
        const removeMe = function (){
            const parent = fBoxList.get(_this.parentID);
            
            _this.node.querySelectorAll('.rds-field-box')

            // remove child
            _this.node.querySelectorAll('.rds-field-box').forEach((child) => {
                fBoxList.remove(child.id);
            });   

            fBoxList.remove(_this.id);
            _this.node.remove();
            
            if(parent && parent.node.querySelectorAll('.rds-field-boxes .rds-field-box').length == 0){
                parent.elBinded('type').removeAttribute('disabled');
                parent.elBinded('type').classList.remove('rds-bold');
            }            
        }

        animateCollapse(this.node, 100, function(){
            removeMe();        
        })        
    }
    
    dialogRemoveFieldBox() {
        const _this = this;
        const dialog = new dialogBox('dlg-remove-field');
        
        dialog.title = "Delete Confirmation";
        dialog.content = "Are you sure you want to remove this field?"

        dialog.addButton(['Yes', 'No']);
        dialog.addEventListener('buttonClick', (e)=>{
            if(e.detail.text == 'Yes'){
                _this.remove();
            }
            e.target.close();
        });

        dialog.addEventListener('open', (e)=>{
            const moveHandle = _this.select('.rds-field-box-header .move-handle-icon');
            moveHandle.classList.remove('dashicons-move');
            moveHandle.classList.add('dashicons-trash');
            _this.node.classList.add('removing');
        });

        dialog.addEventListener('close', (e)=>{
            const moveHandle = _this.select('.rds-field-box-header .move-handle-icon');
            moveHandle.classList.remove('dashicons-trash');
            moveHandle.classList.add('dashicons-move');
            _this.node.classList.remove('removing');
            _this.select('button.remove-field').focus();
            
            e.target.node.remove();
        });

        return dialog;
    }

    dialogRemoveAttribute(att) {
        const _this = this;
        const dialog = new dialogBox('dlg-remove-att');
        
        dialog.title = "Delete Confirmation";
        dialog.content = "Are you sure you want to remove this attribute?"

        dialog.addButton(['Yes', 'No']);
        dialog.addEventListener('buttonClick', (e)=>{
            if(e.detail.text == 'Yes'){
                _this.#removeAttribute(att);
            }
            e.target.close();
        });        

        dialog.addEventListener('open', (e)=>{
            const moveHandle = att.node.querySelector('.move-handle-icon');
            moveHandle.classList.remove('dashicons-move');
            moveHandle.classList.add('dashicons-trash');
        });

        dialog.addEventListener('close', (e)=>{
            const moveHandle = att.node.querySelector('.move-handle-icon');
            moveHandle.classList.remove('dashicons-trash');
            moveHandle.classList.add('dashicons-move');
            att.node.querySelector('button.remove-attr').focus();
            
            e.target.node.remove();
        });

        return dialog;
    }

    dialogRemoveOption(opt) {
        const _this = this;
        const dialog = new dialogBox('dlg-remove-opt');
        
        dialog.title = "Delete Confirmation";
        dialog.content = "Are you sure you want to remove this option?"

        dialog.addButton(['Yes', 'No']);
        dialog.addEventListener('buttonClick', (e)=>{
            if(e.detail.text == 'Yes'){
                _this.#removeOption(opt);
            }
            e.target.close();
        });        

        dialog.addEventListener('open', (e)=>{
            const moveHandle = opt.node.querySelector('.move-handle-icon');
            moveHandle.classList.remove('dashicons-move');
            moveHandle.classList.add('dashicons-trash');
        });

        dialog.addEventListener('close', (e)=>{
            const moveHandle = opt.node.querySelector('.move-handle-icon');
            moveHandle.classList.remove('dashicons-trash');
            moveHandle.classList.add('dashicons-move');
            opt.node.querySelector('button.remove-option').focus();
            
            e.target.node.remove();
        });

        return dialog;
    }

    #removeAttribute(att){
        const _this = this;

        const removeMe = function (){
            _this.#attributes.remove(att.id)
            att.node.remove();                    
        }

        animateCollapse(att.node, 60, function(){
            removeMe();        
        })     
    }

    #removeOption(opt){
        const _this = this;

        const removeMe = function (){
            _this.#options.remove(opt.id)
            opt.node.remove();                    
        }

        animateCollapse(opt.node, 60, function(){
            removeMe();        
        })     
    }

    #setupEvent(){
        const _this = this;

        this.addEventListener('input',(e)=>{
            const handle = e.detail.handle;

            if( handle == 'type'){
                const el = this.elBinded('type');
                const selectedText = el.options[el.selectedIndex].text;
    
                this.node.setAttribute("data-type", this.type); 
    
                _this.fbox_type = selectedText;

                const btnAddField = this.node.querySelector(".rds-group-field > .rds-btn-add-wrapper > button.btn-group-new-field");
                if(this.type == 'tabs') { // ini nanti dicek
                    btnAddField.textContent = "Add New Tab";
                    btnAddField.setAttribute("data-add", "tab");
                } else {
                    btnAddField.textContent = "Add New Field";
                    btnAddField.setAttribute("data-add", "field");
                }
                
                if(['select', 'radio'].includes( e.detail.value) && (Object.keys(_this.options).length == 0) ) {
                    // jumlah option minimal 2 (ini harus setelah wrapped)
                    _this.addOption();
                    _this.addOption();
                }

                if(!_this.name){
                    _this.classes = types[e.detail.value].classes;
                }

                /** untuk set visibilitas field terkait type */
                let hide = false;
                Object.keys(_this.#typeHandles).forEach(function(typeHandle){
                    hide = !types[e.detail.value].fields.includes(typeHandle);
                    _this.#typeHandles[typeHandle].classList.toggle('hide', hide);
                });
            }

            if( handle == 'label' || handle == 'name') {
                _this.fbox_title =_this.label ? _this.label : _this.name;
            }

            if( handle == 'name') { // untuk validasi unique
                e.detail.element.setAttribute('data-val', e.detail.element.value);    
            }
        });

        this.addEventListener('buttonClick',(e)=>{
            const handle = e.detail.handle;
            
            if(handle == 'btn_move_up') {
                fBoxMoveUp(_this);                                
                _this.node.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
                animate.fadeIn(_this.node,{delay:200})
            }

            if(handle == 'btn_move_down') {
                fBoxMoveDown(_this);                
                _this.node.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "nearest" });
                animate.fadeIn(_this.node,{delay:200})
            }

            if(handle == 'btn_duplicate') {
                const fBox = createFBox(_this.getValues(), _this.parentID, {
                    before: (fBox, field)=>{
                        field.name = '';
                    },
                    after: (fBox, field)=>{
                        fBox.node.classList.toggle("expanded", true);
                    }                    
                });
                _this.node.after(fBox.node);
                fBox.node.scrollIntoView({ behavior: "smooth", block: "start", inline: "start" });
                animate.fadeIn(fBox.node,{delay:200})
                fBox.elBinded('label').focus()
            }

            if(handle == 'btn_remove_field') {
                if(!_this.label && !_this.name){
                    _this.remove();
                    return;                 
                }
                _this.dialogRemoveFieldBox().open();                  
            }

            if(handle == 'btn_add_field') {
                clickAddNew(_this.id);
            }

            if(handle == 'title_wrapper' || handle == 'btn_expanse_collapse' ){
                _this.node.classList.toggle('expanded');
            }

            if(handle == 'btn_clear_opt_default') {
                _this.select('.opt-default input:checked').checked = false
            }

            if(handle == 'btn_generate_id') {                
                const newID =  ( _this.label ? _this.label:_this.type ).toLowerCase().replace(/[\W_]+/g,"_");
                _this.name  = newID;
                const checkID = function(){
                    Object.values(fBoxList.list).forEach(function(fBox){
                        if(fBox.id != _this.id) {
                            if(fBox.name == _this.name) {
                                if(_this.name == newID) {
                                    _this.name = newID + "_1"; } 
                                else {
                                    _this.name = newID + "_" + (Number(_this.name.split("_").pop()) + 1);        
                                }
                                checkID(); return // restart checkID with the new changed one
                            }
                        }
                    })
                } 
                checkID();
                _this.elBinded('name').setAttribute("data-val", _this.name)
            }                        
        });     
        
        this.select(".rds-field-box-header .move-handle-icon").addEventListener('keydown', keydownMoveIconFBox);  

        this.elBinded('move_handle').addEventListener('focus', (e)=>{
            _this.elBinded('title_wrapper').classList.toggle('rg-ml-8', true);
            _this.node.querySelector('.rds-field-box-header').classList.toggle('rds-moving', true);
        });

        this.elBinded('move_handle').addEventListener('blur', (e)=>{
            _this.elBinded('title_wrapper').classList.toggle('rg-ml-8', false);
            _this.node.querySelector('.rds-field-box-header').classList.toggle('rds-moving', false);
        });   

        this.#setupAttributesEvent();
        this.#setupOptionsEvent();
    }

    #setupAttributesEvent(){
        const _this = this;
        
        this.elBinded('btn_add_attribute').onclick = function(){
            _this.addAttribute().elBinded('name').focus();
        }
        
                
        this.#attributes.addEventListener('single', function(){
            _this.select("table.rds-field-attributes-table").classList.remove("hide");
            _this.select('.rds-field-attribute-footer').appendChild(_this.select('button.btn-add-attribute'));
        });

        this.#attributes.addEventListener('empty', function(){
            _this.select("table.rds-field-attributes-table").classList.add("hide");
            _this.select('.rds-field-attribute-header').appendChild(_this.select('button.btn-add-attribute'));
        });
        
        this.#attributes.addEventListener('add', function(e){
            const att = e.detail.item;

            if( !(att instanceof attributeRow) ) {
                e.preventDefault();
                return;
            }

            att.node.querySelector('.remove-attr').onclick = () => {
                if(!att.name && !att.value){
                    _this.#removeAttribute(att);
                    return;                 
                }

                _this.dialogRemoveAttribute(att).open();                
            }

            att.node.querySelector('.move-handle-icon').addEventListener('keydown', keydownMoveIcon)
        })
    }

    #setupOptionsEvent(){
        const _this = this;
        
        this.elBinded('btn_add_option').onclick = ()=>{
            _this.addOption().elBinded('value').focus();            
        }        
        
        this.#options.addEventListener('add', function(e){
            const opt = e.detail.item;

            if( !(opt instanceof optionRow) ) {
                e.preventDefault();
                return;
            }

            opt.node.querySelector('.remove-option').onclick = () => {
                if(!opt.value && !opt.text){
                    _this.#removeOption(opt);
                    return;                 
                }

                _this.dialogRemoveOption(opt).open();
            }

            opt.node.querySelector('.move-handle-icon').addEventListener('keydown', keydownMoveIcon)
        })
    } 
}

class attributeRow extends elBinder {
    node;
    #id;
    get id(){
        return this.#id;
    }

    #fbid;
    /**
     * @param {string} value
     */
    set fbid(value) {
        this.#fbid = value;
    }

    get fbid() {
        return this.#fbid;
    }

    #init(){
        this.#id = 'att'+getCounter();
        this.node = getHTMLTemplate('field-attribute-row-template');
        this.node.id = this.id;
        this.node.innerHTML = this.node.innerHTML.replaceAll("[attid]", this.id);
    }

    constructor(fbid) {    
        super();
        this.#fbid = fbid;
        this.#init();
        this.node.querySelectorAll("[data-att-handle]").forEach((element) => {
            this._bind(element, element.getAttribute('data-att-handle'));
        });
        document.querySelector('#'+fbid+' table.rds-field-attributes-table tbody').appendChild(this.node);
    }
}

class optionRow extends elBinder {
    node;
    #id;
    get id(){
        return this.#id;
    }
    
    #fbid;
    /**
     * @param {string} value
     */
    set fbid(value) {
        this.#fbid = value;
    }

    get fbid() {
        return this.#fbid;
    }

    #init(){
        this.#id = 'opt'+getCounter();
        this._bind.default = {};
        this._bind.value = {};
        this._bind.text = {};

        this.node = getHTMLTemplate('field-option-row-template');
        this.node.id = this.id;
        this.node.innerHTML = this.node.innerHTML.replaceAll("[optid]", this.id);
    }

    constructor(fbid) {
        super();
        this.#fbid = fbid;
        this.#init();
        
        this.node.querySelectorAll("[data-opt-handle]").forEach((element) => {
            this._bind(element, element.getAttribute('data-opt-handle'));
        });
        document.querySelector('#'+fbid+' table.rds-field-options-table tbody').appendChild(this.node);        
    }
}

export {fieldBox, fBoxList, createFBox}
