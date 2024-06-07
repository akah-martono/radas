(function($) {
    function observe(id){
        // Select the node that will be observed for mutations
        const targetNode = document.getElementById(id);

        // Options for the observer (which mutations to observe)
        const config = { attributes: true, childList: true, subtree: true };

        // Callback function to execute when mutations are observed
        const callback = (mutationList, observer) => {
        for (const mutation of mutationList) {
            if (mutation.type === "childList") {
            console.log("A child node has been added or removed.");
            } else if (mutation.type === "attributes") {
            console.log(`The ${mutation.attributeName} attribute was modified.`);
            }
        }
        };

        // Create an observer instance linked to the callback function
        const observer = new MutationObserver(callback);

        // Start observing the target node for configured mutations
        observer.observe(targetNode, config);

        // Later, you can stop observing
        observer.disconnect();
    }

    const inputValidationCallBack = [];
    const saveValidationCallBack = [];
    const actions = {};
    
    $.fn.setRCS = function(){
        $(this).find("[data-rcs]").each(function(){
            let styles = this.getAttribute('data-rcs').split(";");
            styles.forEach(element => {
                const arStyle = element.trim().split(":");
                const tag = arStyle[0].trim();
                if (tag && arStyle.length > 1) {
                    this.style.setProperty('--rds-'+tag, arStyle[1].trim());
                }
            });
        });
    }

    window.rdsPage = {};

    window.rdsPage.addAction = function(name, callBack) {
        if( !(name in actions) ) {
            actions[name] = [];
        }
        actions[name].push(callBack);
    }
    
    window.rdsPage.ErrorMessage = function (input, message="") {    
        
        if(!input.$){input = $(input);}
        
        let errMessage = input.siblings(".rds-error-message")

        if (!errMessage.length) {
            errMessage = $('<div class="rds-error-message"></div>');
            const textRight = input.siblings(".rds-text-right");
            if(textRight.length) {
                errMessage.insertAfter(textRight);    
            } else {
                errMessage.insertAfter(input);
            }
            
        }

        $(errMessage).text(message);
        
        if(message == "") {
            input.removeClass("error");
        } else {
            input.addClass("error");
        }
    }
    
    function onInput(){
        inputValidation(this);
    }

    function inputValidation(input){
        let valid = true;

        rdsPage.ErrorMessage(input);
        if ("inputValidation" in actions) {
            for (var i = 0; i < actions.inputValidation.length; i++) {
                valid = actions.inputValidation[i](input);
                if(!valid) {
                    break;
                }
            }    
        }

        if(!valid) return;

        // general validation
        if (!input.checkValidity()) {
            rdsPage.ErrorMessage(input, input.validationMessage);
        }           
    }

    function saveData(){
        $(".rds-input:visible").each(function(){
            inputValidation(this);
        });

        if ("beforeSave" in actions) {
            for (var i = 0; i < actions.beforeSave.length; i++) {
                actions.beforeSave[i]();
            }    
        }

        if($(".rds-error-message:visible").length != 0) {
            alert("please check your input!");
            return;
        }


        const opt_page = {};

        opt_page.title = $("#page_name").val();
        opt_page.slug = $("#page_slug").val();
        opt_page.description = $("#description").val();
        opt_page.capability = $("#capability").val();

        opt_page.type = $("#page_type").val();
        opt_page.parent = $("#parent_menu").val();
        opt_page.menu_title = $("#menu_name").val();
        opt_page.icon = $("#page_icon").val();
        opt_page.position = $("#position").val();

        
        mainWrapper = {}
        mainWrapper.type = 'form_table';
        mainWrapper.id = $("#page_slug").val() + "_aft";
        mainWrapper.fields =[];
        mainWrapper.fields = window.getFields();

        opt_page.fields =[];
        // opt_page.fields = getFields();
        opt_page.fields.push(mainWrapper);

        console.log(JSON.stringify(opt_page));
        // return;

        var formData = new FormData(); 
        formData.append('action', 'rds_create_opt_page');
        formData.append('title', $("#page_name").val());
        formData.append('name', $("#page_slug").val());
        formData.append('op_json', JSON.stringify(opt_page));

        $.ajax({ 
            url: 'admin-ajax.php', 
            method: 'post', 
            data: formData, 
            processData: false, 
            contentType: false, 
            success: function (response) {
                console.log(response);
                alert("Sukses");
                
            }, 
            error: function (xhr) {                        
                console.log(xhr);
                alert(xhr);            
            } 
        });        
    }

    window.getData = function(id){
        $("#rds-fields-box-wrapper").addClass("hide");
        $.ajax({ 
            url: '/wp-json/radas/v1/op-factory/'+id, 
            method: 'get', 
            processData: false, 
            contentType: false, 
            success: function (response) {
                console.log(response);
                const op = response.data.op;
                $("#post_id").val(response.data.id);
                $("#page_name").val(op.title);
                $("#page_slug").val(op.slug);
                $("#description").val(op.description);
                
                op.fields[0].fields.forEach(function(field){
                    window.fillFBox(field);
                })

                // alert("Sukses");
                
            }, 
            error: function (xhr) {                        
                console.log(xhr);
                alert('error');            
            } 
        }); 
        $("#rds-fields-box-wrapper").removeClass("hide"); 
    }

    $(document).ready(function(){
        $('#rds-page').on('input', '.rds-input', onInput);
        $('#rds-page').setRCS();   
        $(".btn-save-page").click(saveData);     
        $(".rds-input:visible").first().focus();
    });


})(jQuery);