const $=jQuery;
let optTable;

$(document).ready(function(){
    const tableID = 'option-page-list';
    optTable = new DataTable('#'+tableID, {
        ajax: {
            url: wpApiSettings.root + document.getElementById(tableID).getAttribute('data-endpoint'),        
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },     
        },
        responsive: true,
        paging: false,
        order: [[0,'desc']],
        layout: {
            topStart: () => {
                let text = document.createElement('h2');
                text.innerHTML = 'Options Page List';
     
                return text;
            },
            topEnd: () => {
                let buttons = document.createElement('div');
                buttons.innerHTML = `
                <button class="btn-add-page button button-secondary">Add New</button>
                <button class="btn-add-json button button-secondary">Upload JSON</button>
                `;
     
                return buttons;
            },
            bottomStart: null,
            bottomEnd: null
        },        
        columns: [
            { data: 'id', visible: false },
            { data: 'title', orderable: false },
            { data: 'slug', orderable: false },
            { data: 'capability', orderable: false },
            { 
                data: 'type',
                width: '150px',
                orderable: false,
                render: ( data, type, full, meta ) => {
                    let menuType =document.querySelector('#page_type option[value="'+data+'"]')
                    menuType = menuType ? menuType.innerHTML : data;
                    return menuType;
                } 
            },
            { 
                data: 'menu', 
                orderable: false,
                "render": ( data, type, full, meta ) => {
                    let menuName =document.querySelector('#parent_menu option[value="'+data+'"]')
                    menuName = (null == menuName) ? data : menuName.innerHTML;
                    return menuName;
                } 
            },
            {     
                data: 'id',          
                width: '100px',
                className: 'dt-center',
                orderable: false,
                render: ( data, type, full, meta ) => {
                    return `
                    <button title="Edit options page" class="no-button dashicons-before dashicons-edit" onclick="actions.edit(${meta.row})"></button>                    
                    <button title="Download options page JSON file" class="no-button dashicons-before dashicons-download" onclick="actions.download(${meta.row})"></button>
                    <button title="Delete options page" class="no-button dashicons-before dashicons-trash" onclick="actions.delete(${meta.row})"></button>
                    `;
                }
            },
            { 
                data: 'details',
                visible: false
            }
        ],
    });    
});

export {optTable};