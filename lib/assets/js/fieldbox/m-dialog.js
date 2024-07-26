import { dialogBox} from "../general/m-general.js";

const dialogRemoveFieldBox = () => {
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

const dialogRemoveAttribute = (att) => {
    const _this = this;
    const dialog = new dialogBox('dlg-remove-att');
    
    dialog.title = "Delete Confirmation";
    dialog.content = "Are you sure you want to remove this attribute?"

    dialog.addButton(['Yes', 'No']);
    dialog.addEventListener('buttonClick', (e)=>{
        if(e.detail.text == 'Yes'){
            // _this.#removeAttribute(att);
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

const dialogRemoveOption = (opt) => {
    const _this = this;
    const dialog = new dialogBox('dlg-remove-opt');
    
    dialog.title = "Delete Confirmation";
    dialog.content = "Are you sure you want to remove this option?"

    dialog.addButton(['Yes', 'No']);
    dialog.addEventListener('buttonClick', (e)=>{
        if(e.detail.text == 'Yes'){
            // _this.#removeOption(opt);
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
