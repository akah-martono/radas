(function($) {
    class mediaInput{
        input;
        btnUpload;
        btnRemove;
        preview;
        constructor(node) {
            this.input = node.querySelector('.media-id');
            this.btnUpload = node.querySelector('.upload-media-button');
            this.btnRemove = node.querySelector('.remove-media-button');
            this.preview = node.querySelector('.media-preview');
        }
    }

    $(document).ready(function($){
        let mediaBox;
        document.querySelectorAll('.rds-media-input').forEach((element) => {
            mediaBox = new mediaInput(element);
            
            mediaBox.btnRemove.addEventListener('click', (e)=>{
                mediaBox.input.value = '';
                mediaBox.preview.innerHTML = '';
                mediaBox.btnRemove.classList.toggle('hide');
            });

            mediaBox.btnUpload.addEventListener('click', (e)=>{
                const isMultiple = mediaBox.input.getAttribute('data-is-multiple') ?? false;
                const frameTitle = mediaBox.input.getAttribute('data-frame-title') ?? 'Choose or Upload Media';
                const buttonText = mediaBox.input.getAttribute('data-frame-button-text') ?? 'Use this media';
                const mediaType = mediaBox.input.getAttribute('data-media-type') ?? 'image';
    
                const customMediaFrame = wp.media({
                    title: frameTitle,
                    button: {
                        text: buttonText
                    },
                    multiple: isMultiple,
                    library: { type: mediaType } 
                });
    
                customMediaFrame.on('select', function() {
                    const selection = customMediaFrame.state().get('selection');
                    // Loop through each selected attachment
                    selection.each(function (attachment) {
                        // Do something with the attachment information
                        console.log(attachment.toJSON());
                    });                
    
                    const attachment = selection.first().toJSON();
                    if(attachment.type != mediaType){
                        alert("The media you selected is not supported");
                        return;
                    }
                    mediaBox.input.value = attachment.id;    
                    mediaBox.preview.innerHTML = '<img width="129" height="129" src="' + attachment.sizes.thumbnail.url + '" />';
                    mediaBox.btnRemove.classList.toggle('hide', false);
                });
    
                customMediaFrame.open();
    
            });
        });

        // $('.upload-media-button').click(function() {
            
        //     input_field = $('#' + $(this).attr('input-id'));

        //     multiple = input_field.attr('data-multiple') ?? false;
        //     title = input_field.attr('data-title') ?? 'Choose or Upload Media';
        //     text = input_field.attr('data-text') ?? 'Use this media';
        //     type = input_field.attr('data-type') ?? 'image';

        //     var customMediaFrame = wp.media({
        //         title: title,
        //         button: {
        //             text: text
        //         },
        //         multiple: multiple,
        //         library: { type: type } 
        //     });

        //     customMediaFrame.on('select', function() {
        //         var selection = customMediaFrame.state().get('selection');
        //         // Loop through each selected attachment
        //         selection.each(function (attachment) {
        //             // Do something with the attachment information
        //             console.log(attachment.toJSON());
        //         });                

        //         var attachment = selection.first().toJSON();
        //         // input_field.val(attachment.url);
        //         input_field.val(attachment.id);

        //     });

        //     customMediaFrame.open();
        // });
    });
})(jQuery);
