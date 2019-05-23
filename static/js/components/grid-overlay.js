import $ from 'jquery';

class GridOverlay {
    constructor(el) {
        this.$body = $('body');
        if (!this.$body) {
            throw new Error('Invalid element reference.');
        }
        this.init();
    }

    init() {
        $(document).keydown(evt => {
            if (evt.altKey && evt.shiftKey && 76 === evt.keyCode) {
                evt.preventDefault();
                this.$body.toggleClass('grid-is-shown');
            }
        });
    }
}

export default GridOverlay;
