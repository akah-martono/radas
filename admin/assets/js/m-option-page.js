import { showSpinner, popupAlert, getFormValue} from "../../../lib/assets/js/m-general.js";
import { adminFlashNotice } from "../../../lib/assets/js/m-admin.js";
import { setErrorMessage } from "../../../lib/assets/js/m-utils.js";

const $=jQuery;

const inputValidation= (input) => {
    setErrorMessage(input);

    // general validation
    if (!input.checkValidity()) {
        setErrorMessage(input, input.validationMessage);
        return false;
    }           

    return true;
}    

$(document).ready(()=>{
    let lastFormData;
    
    const form = document.querySelector('form'); 
    lastFormData = getFormValue(form);

    form.setAttribute('novalidate', true);

    form.addEventListener('input', (e)=>{
        inputValidation(e.target);
    })

    form.addEventListener("submit", (e) => {
        showSpinner(true);   

        e.preventDefault();

        if(lastFormData == getFormValue(form)){
            popupAlert('No Update', "You haven't changed the data");
            showSpinner(false);
            return;
        }
        
        let isValid = true;
        Object.values(document.querySelectorAll('.rds-input')).forEach((input) => {            
            if (window.getComputedStyle(input.closest('.rds-field')).display !== "none"){
                if(!inputValidation(input)) {                    
                    isValid = false;
                }
            };
        })   
        
        if(!isValid) {
            popupAlert('Data Validation', 'Save failed, please check your input!');
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