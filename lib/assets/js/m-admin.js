import { getCounter, animate } from "./m-general.js";

/** 
* @param {string} status - Available status: 
* - error: will display the message with a white background and a red left border.
* - warning: will display the message with a white background and a yellow/orange left border.
* - success: will display the message with a white background and a green left border.
* - info: will display the message with a white background a blue left border.
* @param {string} message 
* @param {int} timeOutInSeconds optional, default 5 seconds
 */
export const adminFlashNotice = (status, message, timeOutInSeconds = 5) => {
    const getWrapper = () => {
        const id = 'rds-admin-notice';
        let wrapper = document.getElementById(id);
        if(null === wrapper){
            wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.setAttribute('style', 'top: var(--wp-admin--admin-bar--height, 46px); width:480px; max-width: 96%; position: fixed; right: 0; z-index: 99999; padding-right:8px; padding-left:32px;');
            document.body.prepend(wrapper);
        }
        return wrapper;
    }
    
    const createNotice = (id, classes, message) => {
        const divNotice = document.createElement('div');
        const paraMessage = document.createElement('p');
        const buttonDismiss = document.createElement('button');
        const spanButtonInfo = document.createElement('span');
    
        paraMessage.textContent = message;
    
        buttonDismiss.setAttribute('type', 'button');
        buttonDismiss.setAttribute('class', 'notice-dismiss');
        buttonDismiss.onclick = ()=>{
            close(divNotice);
        }
    
        spanButtonInfo.setAttribute('class', 'screen-reader-text');
        spanButtonInfo.textContent = 'Dismiss this notice';
    
        divNotice.id = id
        divNotice.setAttribute('class', 'is-dismissible notice ' + classes);
    
        buttonDismiss.append(spanButtonInfo);
        divNotice.append(paraMessage, buttonDismiss);
    
        return divNotice;
    }

    const id = 'rds-admin-flash-notice-' + getCounter();  
    const wrapper = getWrapper();

    wrapper.append(createNotice(id, 'notice-' + status, message));

    setTimeout((id) => {
        const notice = document.getElementById(id);
        if(null !== notice){
            // notice.remove();
            close(notice);
        }
    }, timeOutInSeconds*1000, id);

    const close = (notice) => {
        animate.fadeOut(notice, {
            delay: 160,
            onComplete: ()=>{notice.remove()}
        })
    }
}