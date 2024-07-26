import { fBoxList } from "./m-fboxlist.js";

const $=jQuery;

const sortFBoxParams = {
    handle:".fbox-move-handle",
    placeholder: "rds-sortable-placeholder",

    connectWith: ".rds-field-boxes",
    forceHelperSize: true,
    receive: function(e, ui) {
        const fBoxReceiver = e.target.closest('.rds-field-box');
        const typeReceiver = (null === fBoxReceiver) ? 'root' : fBoxReceiver.getAttribute('data-type');
        const fBox = fBoxList.list[ui.item[0].id];

        if(!fBox.wrappers.includes(typeReceiver)){
            $(ui.sender).sortable("cancel");  
        }
    },    
    over: function(e, ui) {
        const fBoxReceiver = e.target.closest('.rds-field-box');
        const typeReceiver = (null === fBoxReceiver) ? 'root' : fBoxReceiver.getAttribute('data-type');
        const fBox = fBoxList.list[ui.item[0].id];        
        if(!fBox.wrappers.includes(typeReceiver)){
            document.querySelector('.rds-sortable-placeholder').classList.toggle('hide', true);
        } else {
            document.querySelector('.rds-sortable-placeholder').classList.toggle('hide', false);
        }
    }
}

$(".rds-field-boxes").sortable(sortFBoxParams);

fBoxList.addEventListener('added', (e) => {
    $(e.detail.item.node.querySelector('.rds-field-boxes')).sortable(sortFBoxParams); 

    $(e.detail.item.select('table.rds-field-attributes-table tbody')).sortable({
        handle: ".move-handle",
    });

    $(e.detail.item.select('table.rds-field-options-table tbody')).sortable({
        handle: ".move-handle",
    });                 
}); 