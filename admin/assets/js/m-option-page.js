import { showSpinner, popupAlert, getFormValue} from "../../../lib/assets/js/m-general.js";
import { adminFlashNotice } from "../../../lib/assets/js/m-admin.js";

const $=jQuery;


$(document).ready(()=>{
    let lastFormData;
    
    const form = document.querySelector('form'); 
    lastFormData = getFormValue(form);

    form.addEventListener("submit", (e) => {
        showSpinner(true);   

        e.preventDefault();

        if(lastFormData == getFormValue(form)){
            popupAlert('No Update', "You haven't changed the data");
            showSpinner(false);
            return;
        }
        
        const formData = new FormData(form); 
    
        const url = wpApiSettings.root + form.getAttribute('data-endpoint');
        const method = 'post';    
        $.ajax({ 
            url: url, 
            method: method,             
            data: formData,   
            processData: false, 
            contentType: false,
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },              
            success: (response) => {
                showSpinner(false);
                adminFlashNotice('success', response.message);
                lastFormData = getFormValue(form);
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
    });
});