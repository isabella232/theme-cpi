import $ from 'jquery';
var Flickity = require('flickity');

class HomeFeatures {
    constructor(el) {
        this.$container = $('.home-features__list');
        if (!this.$container) {
            throw new Error('Invalid element reference.');
        }
        this.flkty = null;
        this.init();
    }

    init() {
        // Set up dom element references
        var $carousel = this.$container;

        this.flkty = new Flickity($carousel[0], {
            adaptiveHeight: false,
            cellAlign: 'left',
            contain: true,
            friction: 0.3,
            imagesLoaded: true,
            pageDots: false,
            prevNextButtons: true,
            selectedAttraction: 0.03,
            setGallerySize: true,
        });
    }
}

export default HomeFeatures;
