import { optTable } from "./m-option-page-cpt-table.js";
import { page } from "./m-option-page-cpt-page.js";
import { showSpinner, popupAlert, dialogBox } from "../../../lib/assets/js/m-general.js";

const $=jQuery;

window.actions = {
    edit(row) {
        page.clear();
        page.fillData(optTable.row(row).data().id, optTable.row(row).data().details);
        $('#rds-list').addClass('hide');
        $('.btn-add-page').addClass('hide');
        page.show();        
    }, 
    delete(row) {        
        dialogRemove(row).open();
    },
    download(row) {
        download(JSON.stringify(table.row(row).data().details), table.row(row).data().slug+'.json', "text/plain");
    }, 
    open(row) {
        showSpinner();
        const adminURL = document.getElementById('option-page-list').getAttribute('data-admin-url');
        const data = optTable.row(row).data();        
        const page = (data.type == 'sub_menu' && data.menu.includes(".php")) ? data.menu : 'admin.php';
        
        window.open(`${adminURL}${page}?page=${data.slug}`, '_self');
    }     
}

const dialogRemove = (row)=>{
    const dialog = new dialogBox('dlg-remove-row');
    
    dialog.title = "Delete Confirmation";
    dialog.content = "Are you sure you want to remove this option?"

    dialog.addButton(['Yes', 'No']);
    dialog.addEventListener('buttonClick', (e)=>{
        if(e.detail.text == 'Yes'){
            showSpinner(true);
            const url = wpApiSettings.root + document.getElementById('option-page-list').getAttribute('data-endpoint') + '/' + table.row(row).data().id;
            const method = 'delete';    
            $.ajax({ 
                url: url, 
                method: method, 
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                },                 
                success: function (response) {
                    table.row(row).remove();
                    table.draw(false);
                    showSpinner(false);
                }, 
                error: function (xhr) {
                    const respJSON = xhr.responseJSON;                  
                    console.log(xhr);
                    popupAlert('Delete Failed', respJSON.message);
                    showSpinner(false);
                } 
            });        
        }
        e.target.close();
    });

    dialog.addEventListener('open', (e)=>{   
        table.rows(row).nodes().to$().css( 'color', 'red' ).css( 'font-weight', '600' );
    });

    dialog.addEventListener('close', (e)=>{   
        table.rows(row).nodes().to$().css( 'color', '' ).css( 'font-weight', '' );
     
        e.target.node.remove();
        document.querySelector('.btn-add-page').focus();
    });

    return dialog;    
}

const dialogJsonUpload = (row)=>{
    const dialog = new dialogBox('dlg-json-upload');
    
    dialog.title = "Choose JSON file";
    dialog.content = () => {
        return '<div style="padding: 8px;"><input type="file" id="json-file"  accept="application/JSON"/></div>';
    }

    dialog.addButton(['Upload', 'Cancel']);
    dialog.addEventListener('buttonClick', (e)=>{
        if(e.detail.text == 'Upload'){
            const file = document.getElementById("json-file");
            if (file.files.length == 0){
                popupAlert('No File Choosen', "Please choose JSON file first");
                return;
            }
            fetch(URL.createObjectURL(file.files[0]))
            .then((response) => response.json())
            .then((json) => {
                page.clear();
                $('#rds-list').addClass('hide');
                $('.btn-add-page').addClass('hide');
                page.show();               
                page.fillData("", json);
            });           
        }
        e.target.close();
    });

    dialog.addEventListener('close', (e)=>{        
        e.target.node.remove();
        document.querySelector('.btn-add-json').focus();
    });

    return dialog;    
}

function download(content, fileName, contentType) {
    const a = document.createElement("a");
    const file = new Blob([content], { type: contentType });
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}

// const page = new optPage();

$(document).ready(function(){
    page.addEventListener('saved', (e)=>{
        const id = e.target.post_id;
        let row = null;

        if(id) {
            let menu;
            table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                if(this.data().id == id){
                    menu = (e.target.type == 'menu') ? e.target.slug:e.target.parent_menu;
                    this.data().title = e.target.title;
                    this.data().slug = e.target.slug;
                    this.data().capability = e.target.capability;
                    this.data().type = e.target.type;
                    this.data().menu = menu;
                    this.data().details = e.detail.json;                    
                    
                    this.invalidate();
                    row = rowIdx;
                }
            } );
        }
                
        if(null === row){
            table.row.add({
                id: id,
                title: e.target.title,
                slug: e.target.slug,
                capability: e.target.capability,
                type: e.target.type,
                menu: e.target.menu_title,
                details: e.detail.json,                 
            });
        }
        table.draw(false);
    });
    
    $('.btn-close-page').on('click', ()=>{
        page.close();
        document.querySelector('.btn-add-page').focus();
    });

    $('.btn-add-page').on('click', ()=>{
        page.clear();
        $('#rds-list').addClass('hide');
        $('.btn-add-page').addClass('hide');
        page.show();    
    });

    $('.btn-add-json').on('click', ()=>{
        dialogJsonUpload().open();
    });
    document.querySelector('.btn-add-page').focus();
    window.table = optTable;
});