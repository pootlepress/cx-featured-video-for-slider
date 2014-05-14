(function ($) {

    $(document).ready(function () {
        $('#set-post-video').click(function () {
            window.formfield = 'pp-fv-video-url';

            window.send_to_editor = function (html) {

                if (formfield) {

                    // itemurl = $(html).attr( 'href' ); // Use the URL to the main image.

                    if ( $(html).html(html).find( 'img').length > 0 ) {

                        itemurl = $(html).html(html).find( 'img').attr( 'src' ); // Use the URL to the size selected.

                    } else {

                        // It's not an image. Get the URL to the file instead.

                        var htmlBits = html.split( "'" ); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.

                        itemurl = htmlBits[1]; // Use the URL to the file.

                        var itemtitle = htmlBits[2];

                        itemtitle = itemtitle.replace( '>', '' );
                        itemtitle = itemtitle.replace( '</a>', '' );

                    } // End IF Statement

                    var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
                    var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
                    var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
                    var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;

                    if (itemurl.match(image)) {
                        //btnContent = '<img src="'+itemurl+'" alt="" /><a href="#" class="mlu_remove button">Remove Image</a>';
                    } else {

                        // No output preview if it's not an image.
                        // btnContent = '';

                        // Standard generic output if it's not an image.
//                        html = '<a href="'+itemurl+'" target="_blank" rel="external">View File</a>';
//
//                        btnContent = '<div class="no_image"><span class="file_link">'+html+'</span><a href="#" class="mlu_remove button">Remove</a></div>';
                    }

                    $( '#' + formfield).val(itemurl);
//                    $( '#' + formfield).siblings( '.screenshot').slideDown().html(btnContent);
                    tb_remove();

                    if (formfield == 'pp-fv-video-url') {
                        $('#pp-fv-container #pp-fv-set-video').hide();

                        $('#pp-fv-container .no_image .file_link a').attr('href', itemurl);
                        $('#pp-fv-video-add-from').val('file');

                        $('#pp-fv-container .no_image').show();

                        $('#pp-fv-container .additional-options').hide();
                    }

                } else {
                    window.original_send_to_editor(html);
                }

                // Clear the formfield value so the other media library popups can work as they are meant to. - 2010-11-11.
                formfield = '';

            }
        });

        $('#pp-fv-add-url-button').click(function () {

            if ($('#pp-fv-url-text-box').val() == '') {
                return;
            }

            $('#pp-fv-video-url').val($('#pp-fv-url-text-box').val());
            $('#pp-fv-video-add-from').val('url');

            $('#pp-fv-container #pp-fv-set-video').hide();

            var videoUrl = $('#pp-fv-video-url').val();
//
//            var html = '<a href="' + videoUrl + '" target="_blank" rel="external">View File</a>';
//
//            var btnContent = '<div class="no_image"><span class="file_link">'+html+'</span><a href="#" class="mlu_remove button">Remove</a></div>';

            $('#pp-fv-container .no_image .file_link a').attr('href', videoUrl);

            $('#pp-fv-container .no_image').show();

            if(videoUrl.indexOf('vimeo') >= 0) {
                $('#pp-fv-container .additional-options').show();
            } else {
                $('#pp-fv-container .additional-options').hide();
            }

        });

        $('#pp-fv-container .remove-button').click(function () {

            $('#pp-fv-container .no_image').hide();

            $('#pp-fv-container input').val('');

            $('#pp-fv-container #pp-fv-set-video').show();
        });

        if ($('#pp-fv-video-add-from').val() == 'url' && $('#pp-fv-video-url').val().indexOf('vimeo') >= 0) {
            $('#pp-fv-container .additional-options').show();
        } else {
            $('#pp-fv-container .additional-options').hide();
        }
    });

})(jQuery);
