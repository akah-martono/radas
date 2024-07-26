import { eventList, getHTMLTemplate } from "../general/m-general.js";
import { clickAddNew } from "./m-functions.js";

const fBoxWrapper = document.getElementById('rds-fields-box-wrapper');
fBoxWrapper.replaceWith(getHTMLTemplate('fieldbox-wrapper-template'));

const fBoxList = new eventList();

fBoxList.addEventListener('empty', () => {
    document.querySelector('.rds-fields-header .rds-fields-title').appendChild(document.querySelector('.btn-new-field'));
})

fBoxList.addEventListener('single', () => {
    document.querySelector('.rds-btn-add-wrapper.rds-btn-add-footer').appendChild(document.querySelector('.btn-new-field'));
    document.querySelector('.expand-collapse-wrapper').classList.add('hide');
})

fBoxList.addEventListener('many', () => {
    document.querySelector('.expand-collapse-wrapper').classList.remove('hide');
})

fBoxList.getValues = () => {
    const fields = [];
    for (const field of document.querySelector('.rds-field-boxes').children) {
        fields.push(fBoxList.get(field.id).getValues());
    }
    return fields;    
}    

document.querySelector('button.btn-new-field').onclick = () => {
    clickAddNew();
};

const toggleExpanded = (expand) => {
    Object.values(fBoxList.list).forEach((fBox) => {
        fBox.node.classList.toggle('expanded', expand);
    })
}

document.querySelector('button.btn-expand-all').onclick = () => toggleExpanded(true);
document.querySelector('button.btn-collapse-all').onclick = () => toggleExpanded(false);      

export {fBoxList};