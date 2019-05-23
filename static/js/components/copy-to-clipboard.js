import $ from 'jquery';
import Clipboard from 'clipboard';

class CopyToClipboard {
    constructor(el) {
        this.$copyBtn = $('.js-copy-to-clipboard');
        if (!this.$copyBtn) {
            throw new Error('Invalid element reference.');
        }

        this.$copyBtnFeedback = $('.js-copy-to-clipboard-feedback');
        this.init();
    }

    init() {
        let that = this;
        let clipboard = new Clipboard('.js-copy-to-clipboard');

        clipboard.on('success', function(e) {
            let successText = that.$copyBtn.data('success-text');
            let defaultText = that.$copyBtn.data('default-text');
            that.$copyBtnFeedback.text(successText);
            that.$copyBtn.addClass('copy-to-clipboard--success');
            setTimeout(function() {
                that.$copyBtn.blur();
                that.$copyBtnFeedback.text(defaultText);
                that.$copyBtn.removeClass('copy-to-clipboard--success');
            }, 2500);
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            that.$copyBtnFeedback.text('Not Supported');
            setTimeout(function() {
                that.$copyBtn.attr('disabled', 'disabled');
                that.$copyBtnFeedback.text('');
                that.$copyBtn.blur();
            }, 2500);
        });
    }
}

export default CopyToClipboard;
