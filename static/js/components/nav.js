import $ from 'jquery';

class Nav {
    constructor(el) {
        this.$body = $('body');
        if (!this.$body) {
            throw new Error('Invalid element reference.');
        }
        this.init();
    }

    init() {
        this.$openButton = $('.js-nav-open');
        this.$closeButton = $('.js-nav-close');
        this.$nav = $('.js-nav');
        this.$body = $('body');
        this.navOpen = false;
        this.addClickHandlers();
    }

    addClickHandlers() {
        this.$openButton.on('click', e => {
            e.preventDefault();
            this.toggleNav();
        });

        this.$closeButton.on('click', e => {
            e.preventDefault();
            this.toggleNav();
        });
    }
    toggleNav() {
        this.navOpen ? this.closeNav() : this.openNav();
    }
    openNav(e) {
        this.navOpen = true;
        this.$body.addClass('nav-is-open');
        this.$nav.addClass('nav--active');
        this.$nav.one('webkitTransitionEnd transitionend', () => {
            this.$closeButton.focus();
        });
        this.$body.on('keydown.nav', e => this.handleKeydown(e));
    }
    closeNav() {
        this.navOpen = false;
        this.$body.removeClass('nav-is-open');
        this.$nav.removeClass('nav--active');
        this.$openButton.focus();
        this.$body.off('keydown.nav');
    }

    handleKeydown(e) {
        const $focused = $(e.target);

        if (e.which === 27) {
            e.preventDefault();
            this.closeNav();
        } else if (e.which === 9 && e.shiftKey) {
            if (
                this.$nav
                    .find('a')
                    .first()
                    .is($focused)
            ) {
                e.preventDefault();
                this.$closeButton.focus();
            }
            if (this.$closeButton.is($focused)) {
                e.preventDefault();
                this.$nav
                    .find('a')
                    .last()
                    .focus();
            }
        } else if (e.which === 9) {
            if (
                this.$nav
                    .find('a')
                    .last()
                    .is($focused)
            ) {
                e.preventDefault();
                this.$closeButton.focus();
            }
            if (this.$closeButton.is($focused)) {
                e.preventDefault();
                this.$nav
                    .find('a')
                    .first()
                    .focus();
            }
        }
    }
}

export default Nav;
