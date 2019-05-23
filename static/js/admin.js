import $ from 'jquery';

$(document).ready(() => {
    // Make ACF wysiwyg shorter
    if (typeof acf !== 'undefined') {
        acf.add_action('wysiwyg_tinymce_init', function(ed, id, mceInit, $field) {
            // set height of wysiwyg on frontend
            var minHeight = 200;
            var mceHeight =
                $(ed.iframeElement)
                    .contents()
                    .find('html')
                    .height() || minHeight;

            if (mceHeight < minHeight) {
                mceHeight = minHeight;
            }

            $(ed.iframeElement).css('height', mceHeight);
        });
    }
});
