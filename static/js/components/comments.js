import $ from 'jquery';

class Comments {
    constructor(el) {
        this.$area = $('.article-comments__content');
        if (!this.$area) {
            throw new Error('Invalid element reference.');
        }
        this.$btn = $('.article-comments__toggle');
        this.$btnCount = $('.article-comments__toggle-count');
        this.$btnShowHide = $('.article-comments__toggle-show-hide');
        this.$btnComments = $('.article-comments__toggle-comments');
        this.init();
    }

    init() {
        this.addClickHandlers();
        this.updateCount();
        this.hideComments();
    }

    addClickHandlers() {
        let that = this;
        this.$btn.on('click', e => {
            e.preventDefault();
            that.toggleComments();
        });
    }
    toggleComments() {
        this.$area.slideToggle(400);
        let newBtnText = this.$btnShowHide.text() === 'Show' ? 'Hide' : 'Show';
        this.$btnShowHide.text(newBtnText);
    }
    hideComments() {
        this.$area.hide();
    }
    updateCount() {
        let count = this.getCount();
        if (count == 1) {
            this.$btnCount.text(count);
            this.$btnComments.text('Comment');
        } else if (count > 1) {
            this.$btnCount.text(count);
        }
    }
    getCount() {
        return $('.wpd-cc-value').text();
    }
}

export default Comments;
