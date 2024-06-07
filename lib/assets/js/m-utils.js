const $=jQuery;

export const setErrorMessage = (input, message="") => {       
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
}

export const keydownMoveIcon = (e) => { // ini nanti dicek
    const icon = $( e.target);
    const elMove = icon.closest(icon.attr("data-move-el"));
    if(e.code == "ArrowUp" || e.code == "ArrowLeft") {
        e.preventDefault();
        elMove.insertBefore(elMove.prev());
        
    }
    if(e.code == "ArrowDown" || e.code == "ArrowRight") {
        e.preventDefault();
        elMove.insertAfter(elMove.next()); 
    } 
    icon.focus();
}