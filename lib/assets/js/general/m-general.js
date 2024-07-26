const dialogBoxStyleURL = new URL('./dialogbox.css', import.meta.url);
const spinnerStyleURL = new URL('./spinner.css', import.meta.url);

let iCounter = 0;    
export const getCounter = () => {
    return ++iCounter;
}

export class elBinder extends EventTarget {
    #bind = {};  
    
    constructor() {
        super();
    }

    /**
     * 
     * @param {Element} element 
     * @param {string} handle 
     */
    _bind(element, handle) {            
        const _this = this;
        const tagName = element.tagName;

        if(!this.#bind.hasOwnProperty(handle)){
            this.#bind[handle] = {};
        }
        
        this.#bind[handle].tagName = tagName;
        this.#bind[handle].element = element;

        if(tagName == 'INPUT') {
            this.#bind[handle].inputType = element.type;
        }

        if(this.#isInputableTag(tagName)) {
            this.#bind[handle].value = element.value;

            if(this.#isCheckableInput(handle)){
                element.addEventListener("input", (e)=>{
                    _this[handle] = e.target.checked;
                })                
            } else {
                element.addEventListener("input", (e)=>{
                    _this[handle] = e.target.value;
                })    
            }
        } else {
            this.#bind[handle].value = element.textContent;
        }

        if(tagName == 'BUTTON' || element.hasAttribute('data-clickable')) {
            element.addEventListener("click", ()=>{
                this.dispatchEvent (new CustomEvent("buttonClick", {
                    bubbles: true,
                    detail: { 
                        handle: handle,
                        element: element 
                    },
                }));            
            })            
        }

        Object.defineProperty(this, handle, {
            get() {
                return _this.#getValue(handle)
            },
            set(value) {
                _this.#setValue(handle, value)
            }
        });
    }

    /**
     * @param {string} handle
     */ 
    elBinded(handle) {
        if(!this.#bind.hasOwnProperty(handle)) {
            console.warn(`Property '${handle}' is not defined`);
            return null;
        }

        return this.#bind[handle].element;
    }

    #getValue(handle) {
        if(this.#isCheckableInput(handle)){
            return this.#bind[handle].element.checked;
        }

        return this.#bind[handle].value;
    }
    
    #setValue(handle, value) {
        if(!this.#bind.hasOwnProperty(handle)) {
            console.warn(`Property '${handle}' is not defined`);
            return;
        }

        if(!this.#bind[handle].hasOwnProperty('element')) {
            console.warn(`Property '${handle}' is not binded yet!`);
            return;
        }

        this.#setElementValue(handle, value);
        this.#setPropertyValue(handle, value);            
    }

    #setPropertyValue(handle, value) {
        if(this.#bind[handle].value == value){
            return;            
        }
        
        this.#bind[handle].value = value;

        this.dispatchEvent (new CustomEvent("input", {
            bubbles: true,
            detail: { 
                element: this.#bind[handle].element,
                handle: handle,
                value: value 
            },
          }),
        );
    }
    
    #setElementValue(handle, value) {        
        if(this.#isCheckableInput(handle)){
            value = Boolean(value);
            if(this.#bind[handle].element.checked != value){
                this.#bind[handle].element.checked = value;
            }            
            return;
        }

        if(this.#isInputableTag(this.#bind[handle].tagName)){
            if(this.#bind[handle].element.value != value){
                this.#bind[handle].element.value = value;
            }
            return;
        } 

        if (this.#bind[handle].element.textContent != value) {
            this.#bind[handle].element.textContent = value;
        }
    }

    #isInputableTag(tagName) {
        return ['INPUT', 'TEXTAREA', 'SELECT'].includes(tagName);
    }

    #isCheckableInput(handle) {
        return this.#bind[handle].tagName == 'INPUT' && 
            (this.#bind[handle].inputType == 'radio' || this.#bind[handle].inputType == 'checkbox');
    }
}

export class eventList extends EventTarget{
    #eEmpty = new CustomEvent("empty");
    #eSingle = new CustomEvent("single");
    #eMany = new CustomEvent("many");
    #items={};
    
    get count(){
        return Object.keys(this.#items).length;
    }

    get list() {
        return this.#items;
    }

    constructor() {
        super();
    }

    #listChange(){
        if(this.count == 0) {
            this.dispatchEvent(this.#eEmpty);
        }
        if(this.count == 1) {
            this.dispatchEvent(this.#eSingle);
        }
        if(this.count == 2) {
            this.dispatchEvent(this.#eMany);
        }        
    }

    /**
     * @param {object} item
     */ 
    add(item){
        const dispatchAdd = this.dispatchEvent (new CustomEvent("add", {
            bubbles: true,
            cancelable: true,
            detail: { 
                item: item 
            },
          }),
        );
        
        if(dispatchAdd){
            this.#items[item.id] = item;            
            this.#listChange();
            item.addEventListener('input', (e)=>{
                this.dispatchEvent (new CustomEvent("input", {
                    bubbles: true,
                    detail: { 
                        item: item,
                        element: e.detail.element,
                        handle: e.detail.handle,
                        value: e.detail.value,
                    },
                  }),
                );            
            })
    
        }
    }

    /**
     * @param {string} itemID
     */ 
    remove(itemID){
        const _this = this;
        const dispatchRemove = this.dispatchEvent (new CustomEvent("remove", {
            bubbles: true,
            cancelable: true,
            detail: { 
                itemID: itemID,
                item: _this.#items[itemID]
            },
          }),
        );

        if(dispatchRemove) {
            this.#items[itemID].node.remove();
            this.#items[itemID] = null;
            delete this.#items[itemID];
            this.#listChange();    
        }
    }

    /**
     * @param {string} itemID
     */ 
    get(itemID){
        if(!this.#items.hasOwnProperty(itemID)){
            return null;
        }
        
        return this.#items[itemID];
    }
}

export class dialogBox extends EventTarget{
    node;

    #elements = {
        overlay: document.createElement('div'),
        wrapper: document.createElement('div'),
        header: document.createElement('div'),
        content: document.createElement('div'),
        footer: document.createElement('div'),
        title: document.createElement('h3'),
        btnClose: document.createElement('button')
    }
    
    #width;
    set width(value) {
        this.#width = value;
        this.#elements.wrapper.style.width = this.#width;
    }
    get width() {
        return this.#width;
    }

    #maxWidth;
    set maxWidth(value) {
        this.#maxWidth = value;
        this.#elements.wrapper.style.maxWidth = this.#maxWidth;
    }
    get maxWidth() {
        return this.#maxWidth;
    }    

    #title;
    set title(value) {
        this.#title = value;
        this.#elements.title.textContent = this.#title;
    }
    get title() {
        return this.#title;
    }

    #content;
    set content(value) {
        this.#content = value;
        if (typeof this.#content === "function") {
            this.#elements.content.innerHTML = this.#content();
        } else {
            this.#elements.content.innerHTML = `<p class="text-content">${this.#content}</p>`;
        }        
    }

    get content() {
        return this.#content;
    }        

    constructor(id){
        super();
        
        this.createDialog(id);

        this.width = 'auto';
        this.maxWidth = '480px';
    }


    async createDialog(id) {
        
        if(document.getElementById(id) !== null) return;
        
        await addCSSLink('rds-dialog-style', dialogBoxStyleURL);
        
        const _this = this;
        const el = this.#elements;

        /** pointing overlay as node of this object, so can be removed from outside */
        this.node = el.overlay;
        this.node.id = id;

        el.overlay.classList.add('rds-dialog-overlay');
        el.wrapper.classList.add('rds-dialog-wrapper');
        el.header.classList.add('rds-dialog-header');
        el.content.classList.add('rds-dialog-content');
        el.footer.classList.add('rds-dialog-footer');
        el.btnClose.classList.add('btn-close', 'dashicons', 'dashicons-no');

        el.overlay.append(el.wrapper);

        el.wrapper.append(
            el.header, 
            el.content, 
            el.footer, 
        );

        el.header.append(
            el.title,
            el.btnClose
        );        
        
        el.btnClose.onclick =function() {
            _this.close();
        }

        el.overlay.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                _this.close();
            }
        });          

        document.body.appendChild(el.overlay);
    }
    
    /**
     * 
     * @param {Array} buttonText 
     * @param {*} callBack 
     * @returns 
     */
    addButton(buttonText) {  
        const _this = this;
        const newButton = (text) => {
            const button = document.createElement("button")   
            button.classList.add('button', 'button-secondary');
            button.textContent = text;
            this.#elements.footer.appendChild(button);

            button.onclick = () => {
                _this.dispatchEvent (new CustomEvent("buttonClick", {
                    bubbles: true,
                    detail: { 
                        text: text,
                    },
                  }),
                );
            };    
        }

        if(Array.isArray(buttonText)){
            buttonText.forEach((text)=>{
                newButton(text);
            })
        }

        if(typeof buttonText === 'string') {
            newButton(buttonText);
        }

        return this;
    }

    appendContent(element) {
        this.#elements.content.append(element);
    }    

    close(){
        const _this = this;
        fadeOut(this.#elements.overlay, 70, ()=>{
            this.#elements.overlay.classList.remove('active');
            this.dispatchEvent (
                new CustomEvent("close", {
                    bubbles: true,
                }),
            );  
        }) 
    }

    open(){
        this.dispatchEvent (new CustomEvent("open", {
            bubbles: true,
          }),
        )
        
        this.#elements.overlay.style.opacity=0;
        this.#elements.overlay.classList.add('active')
        this.#elements.btnClose.focus();    
        fadeIn(this.#elements.overlay, 70, ()=>{})            
    }
}

export const popupAlert = (title, content) => {
    const dialogAlert = new dialogBox('dlg-alert');
    dialogAlert.title = title;
    dialogAlert.content = content;    
    dialogAlert.addButton('OK');
    
    dialogAlert.addEventListener('buttonClick', (e)=>{
        if(e.detail.text == 'OK'){
            e.target.close();
        }
    });

    dialogAlert.addEventListener('close', (e)=>{        
        e.target.node.remove();
    });        

    dialogAlert.open();    
}

export const showSpinner = (isActive = true) => {
    const getWrapper = () => {
        let wrapper = document.getElementById('rds-spinner');
        if(null === wrapper){
            addCSSLink('rds-spinner-style', spinnerStyleURL)

            let spinner = document.createElement('span');
            wrapper = document.createElement('div');            

            wrapper.id = 'rds-spinner';
            spinner.id = 'rds-spinner-loader';

            wrapper.append(spinner);

            document.body.prepend(wrapper);
        }
        return wrapper;
    } 
    getWrapper().classList.toggle('active', isActive)    
}

export const getHTMLTemplate = (id) => {
    const wrapper = document.createElement("div");
    wrapper.appendChild(document.getElementById(id).content.cloneNode(true))
    return wrapper.firstElementChild;
}

export const getFormValue = (form, withValueOnly = true) => {
    let result={};
    Object.values(form.elements).forEach((element) => {
        if(['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName) && (element.value && withValueOnly)) {
            result[element.id] = element.value 
            
            if(element.tagName == 'INPUT' && ['radio', 'checkbox'].includes(element.type)){
                result[element.id] = element.checked 
            }
        }        
    } );    
    return JSON.stringify(result);
}

export const addCSSLink = (id, fileURL) => {    
    if(document.getElementById(id) !== null) return;

    const link = document.createElement('link');

    link.id = id;
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = fileURL;

    document.head.appendChild(link);
}

export const addStyle = (id, style) => {        
    if(document.getElementById(id) !== null) return;

    const elStyle = document.createElement('style');
    elStyle.id = id;
    elStyle.innerHTML = style;

    document.head.appendChild(elStyle);
}

// ---- nanti pindah
export const animate = {    
    fadeIn(element, param = {}) {
        const delay = param.hasOwnProperty('delay') ? param.delay :500;
        const toggleClass = param.hasOwnProperty('toggleClass') ? param.toggleClass : '';
        const onComplete = param.hasOwnProperty('onComplete') ? param.onComplete : null;

        element.style.opacity = 0;
        if(toggleClass) element.classList.toggle(toggleClass);

        let opacity = 0;
        const divOpacity = 1/delay;
    
        const id = setInterval(() => {
            element.style.opacity = opacity;
            if(opacity >= 1) {
                if( 'function' == typeof onComplete ){
                    onComplete();
                }
                clearInterval(id);
            }
            opacity +=divOpacity;
        }, 1)           

    }, 

    fadeOut(element, param  = {}) {
        const delay = param.hasOwnProperty('delay') ? param.delay :500;
        const toggleClass = param.hasOwnProperty('toggleClass') ? param.toggleClass : '';
        const onComplete = param.hasOwnProperty('onComplete') ? param.onComplete : null;

        let opacity = 1;
        const divOpacity = 1/delay;
    
        const id = setInterval(() => {
            element.style.opacity = opacity;
            if(opacity <= 0) {
                if( 'function' == typeof onComplete ){
                    onComplete();
                }
                clearInterval(id);
                if(toggleClass) element.classList.toggle(toggleClass);
            }
            opacity -=divOpacity;
        }, 1)    
    },

    /* belum ditest*/
    Expand (element, param = {}) { 
        const delay = param.hasOwnProperty('delay') ? param.delay :500;
        const toggleClass = param.hasOwnProperty('toggleClass') ? param.toggleClass : '';
        const onComplete = param.hasOwnProperty('onComplete') ? param.onComplete : null;

        element.style.opacity = 0;
        if(toggleClass) element.classList.toggle(toggleClass);

        let offsetHeight = element.offsetHeight;
        let opacity = 0;
        const divHeiht = offsetHeight/delay;
        const divOpacity = 1/delay;
    
        const iID = setInterval(() => {
            offsetHeight +=divHeiht;
            element.style.height = offsetHeight + 'px';
    
            opacity +=divOpacity;
            element.style.opacity = opacity;
    
            if(opacity >= 1) {
                if( 'function' == typeof onComplete ){
                    onComplete();
                }
                clearInterval(iID);                
            }
        }, 1)    
    },

    Collapse (element, param = {}) {
        const delay = param.hasOwnProperty('delay') ? param.delay :500;
        const toggleClass = param.hasOwnProperty('toggleClass') ? param.toggleClass : '';
        const onComplete = param.hasOwnProperty('onComplete') ? param.onComplete : null;

        let height = element.clientHeight ;
        let opacity = 1;
        const divHeiht = height/delay;
        const divOpacity = 1/delay;
    
        const iID = setInterval(() => {
            height -=divHeiht;
            element.style.height = height + 'px';
    
            opacity -=divOpacity;
            element.style.opacity = opacity;
    
            if(opacity <= 0) {
                if( 'function' == typeof onComplete ){
                    onComplete();
                }
                clearInterval(iID);
                if(toggleClass) element.classList.toggle(toggleClass);
            }
        }, 1)    
    }   
}

/** ini dpakai di fieldBox */
export const animateCollapse = (element, delay, callBack) => {
    let offsetHeight = element.offsetHeight;
    let opacity = 1;
    const divHeiht = offsetHeight/delay;
    const divOpacity = 1/delay;

    const iID = setInterval(() => {
        offsetHeight -=divHeiht;
        element.style.height = offsetHeight + 'px';

        opacity -=divOpacity;
        element.style.opacity = opacity;

        if(opacity <= 0) {
            callBack();
            clearInterval(iID);
        }
    }, 1)    
}

export const fadeIn = (element, delay, callBack) => {
    // element.style.opacity=0

    let opacity = 0;
    const divOpacity = 1/delay;

    const id = setInterval(() => {
        element.style.opacity = opacity;
        if(opacity >= 1) {
            callBack();
            clearInterval(id);
        }
        opacity +=divOpacity;
    }, 1)    
}

export const fadeOut = (element, delay, callBack) => {
    let opacity = 1;
    const divOpacity = 1/delay;

    const id = setInterval(() => {
        element.style.opacity = opacity;
        if(opacity <= 0) {
            callBack();
            clearInterval(id);
        }
        opacity -=divOpacity;
    }, 1)    
}
