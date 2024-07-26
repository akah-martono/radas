import { fBoxList } from "./m-fboxlist.js";
import { fieldBox } from "./m-fieldbox.js";

const fBoxMoveUp = (fBox) =>{    
    // cari seluruh field box kecuali child dari fBox
    const fBoxes = document.querySelectorAll(`.rds-field-box:not([id="${fBox.id}"] .rds-field-box)`);
    let iMove = 0;

    for (let i = 0; i < fBoxes.length; ++i) {
        if (fBoxes[i].id == fBox.id){ 
            iMove = i;
            break; 
        }
    }    

    if(iMove <= 0) return;
    
    let iPrev = 0;
    let found = false, i = iMove;    
    
    let fBoxMove, fBoxPrev, fBoxPrevParentType;
    fBoxMove = fBoxList.list[fBoxes[iMove].id];

    i--;        
    while( !found && i >=0 ){
        fBoxPrev = fBoxList.list[fBoxes[i].id];

        if(fBoxPrev.parentID == 'root'){
            fBoxPrevParentType = 'root';    
        } else {
            fBoxPrevParentType = fBoxList.list[fBoxPrev.parentID].type;
        }

        // jika satu parent
        if( fBoxPrev.parentID == fBoxMove.parentID ) {
            iPrev = i;
            found = true;
            break;
        }

        // jika container dan masuk kriteria dan bukan parentnya  
        if( fBoxMove.wrappers.includes( fBoxPrev.type ) && fBoxPrev.id != fBoxMove.parentID) {
            iPrev = i;
            found = true;
            break;
        }

        // jika parent berikutnya masuk kriteria
        if( fBoxMove.wrappers.includes( fBoxPrevParentType ) ) {
            iPrev = i;
            found = true;
            break;
        }

        i--;
    }        

    if(!found) return;

    if( fBoxMove.wrappers.includes( fBoxPrev.type ) && fBoxPrev.id != fBoxMove.parentID) { // jika container kosong
        fBoxes[iPrev].querySelector('.rds-field-boxes').append(fBoxes[iMove]);
    } else {
        if( fBoxPrev.parentID != fBoxMove.parentID && !['tabs', 'tab', 'container'].includes( fBoxPrev.type )) {
            fBoxes[iPrev].parentElement.append(fBoxes[iMove]);
        } else {
            fBoxes[iPrev].parentElement.insertBefore(fBoxes[iMove], fBoxes[iPrev]);
        }            
    }                

}

const fBoxMoveDown = (fBox) =>{
    // cari seluruh field box kecuali child dari fBox
    const fBoxes = document.querySelectorAll(`.rds-field-box:not([id="${fBox.id}"] .rds-field-box)`);
    let iMove = 0;

    for (let i = 0; i < fBoxes.length; ++i) {
        if (fBoxes[i].id == fBox.id){ 
            iMove = i;
            break; 
        }
    }    

    if(iMove >= fBoxes.length) return;

    let iNext = 0;
    let found = false, i = iMove;

    let fBoxMove, fBoxNext, fBoxNextParentType;
    fBoxMove = fBoxList.list[fBoxes[iMove].id];

    // jika terkahir namun bukan di root
    if( iMove == fBoxes.length - 1 && fBoxMove.parentID != 'root' && fBoxMove.wrappers.includes('root')  ){
        document.querySelector('.rds-field-boxes').append(fBoxes[iMove]);
        return;
    }

    i++;
    while( !found && i < fBoxes.length  ){
        fBoxNext = fBoxList.list[fBoxes[i].id];
        if(fBoxNext.parentID == 'root'){
            fBoxNextParentType = 'root';    
        } else {
            fBoxNextParentType = fBoxList.list[fBoxNext.parentID].type;
        }
        

        // jika parent berikutnya masuk kriteria
        if( (fBoxNext.parentID !='root' ) && fBoxMove.wrappers.includes( fBoxNextParentType )) {
            iNext = i;
            found = true;
            break;
        }

        // jika container dan masuk kriteria
        if( fBoxMove.wrappers.includes( fBoxNext.type )) {
            iNext = i;
            found = true;
            break;
        }

        // jika bukan container
        if( !['tabs', 'tab', 'container'].includes( fBoxNext.type ) && fBoxMove.wrappers.includes(fBoxNextParentType)) {
            iNext = i;
            found = true;
            break;
        }

        i++;
    }
    
    if(!found) return;

    if( fBoxMove.wrappers.includes( fBoxNext.type ) ) { // jika container
        fBoxes[iNext].querySelector('.rds-field-boxes').prepend(fBoxes[iMove]);
    } else {            
        if(fBoxMove.parentID == fBoxNext.parentID) {
            const afterNext = fBoxes[iNext].nextSibling;
            if(null === afterNext){
                fBoxes[iNext].parentElement.appendChild(fBoxes[iMove]); // taruh paling akhir
            }else {
                afterNext.parentElement.insertBefore(fBoxes[iMove], afterNext)
            }
        } else {
            fBoxes[iNext].parentElement.insertBefore(fBoxes[iMove], fBoxes[iNext]);
        }
    }
}

const keydownMoveIconFBox = (e) => { 
    const icon = e.target;
    const fBox = fBoxList.get(icon.closest('.rds-field-box').id)
    
    if((e.code == "ArrowUp" || e.code == "ArrowLeft") ) {
        e.preventDefault();             
        fBoxMoveUp(fBox);
    }

    if( (e.code == "ArrowDown" || e.code == "ArrowRight") ) {
        e.preventDefault();
        fBoxMoveDown(fBox);
    }

    icon.focus();
}

const clickAddNew = (parentID) => {
    const fBox = new fieldBox();
    fBox.type = "text"; 
    fBox.node.classList.add("expanded");
    fBox.parentID = parentID;                
    fBox.node.scrollIntoView({ behavior: "smooth", block: "start", inline: "start" });
    fBox.elBinded('type').focus()        
}

const createFBox = (field, parentID, param = {}) => {
    const fBox = new fieldBox();
    const before = param.hasOwnProperty('before') ? param.before : null;
    const after = param.hasOwnProperty('after') ? param.after : null;

    if( 'function' == typeof before ){
        before(fBox, field);
    }    

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
        createFBox(field, fBox.id, param);
    })

    if( 'function' == typeof after ){
        after(fBox, field);
    }

    return fBox;
} 

export {fBoxMoveUp, fBoxMoveDown, keydownMoveIconFBox, clickAddNew, createFBox}