import $ from 'jquery';
var Flickity = require('flickity');

class ArticleGallery {
    constructor() {
        this.$body = $('body');
        this.$galleryOverlay = $('.gallery-overlay');
        this.$gallerySlides = $('.gallery-overlay__slides');
        this.$openBtns = $('.js-open-gallery');
        this.$closeBtn = $('.js-gallery-close');
        this.bindCloseBtn();
        this.bindOpenBtns();
    }
    bindOpenBtns() {
        let that = this;
        that.$openBtns.click(function(e) {
            e.preventDefault();
            let $items = $(this)
                .closest('.js-article-gallery')
                .find('.js-gallery-source-item');

            that.cloneImages($items);
            that.updateAttributes();
            that.createSlider();
            that.bindKeyDown();
            that.$galleryOverlay.one('webkitTransitionEnd transitionend', () => {
                that.$closeBtn.focus();
            });
        });
    }
    cloneImages($items) {
        $items.clone().appendTo(this.$gallerySlides);
    }
    bindKeyDown() {
        this.$body.on('keydown.gallery', e => this.handleKeydown(e));
    }

    updateAttributes() {
        let $galleryItems = this.$gallerySlides.find('.js-gallery-source-item');

        $galleryItems.each(function() {
            let $this = $(this);
            let $img = $(this).find('img');

            $img.attr('src', $img.data('src')); // set the src from data-src
            $img.attr('srcset', $img.data('srcset')); // set the srcset from data-srcset
            $img.attr('sizes', $img.data('sizes')); // set the srcset from data-sizes
            $this.removeClass().addClass('js-gallery-item gallery-item');
        });
    }

    createSlider() {
        this.flkty = new Flickity(this.$gallerySlides[0], {
            adaptiveHeight: true,
            cellAlign: 'left',
            contain: true,
            friction: 0.5,
            imagesLoaded: true,
            pageDots: false,
            prevNextButtons: false,
            selectedAttraction: 0.03,
        });
        this.updateCounter();

        this.$body.addClass('gallery-overlay-is-shown');

        // bind clicks for custom prev/next buttons
        let that = this;
        $('.js-gallery-prev').on('click', function() {
            that.flkty.previous(true, true);
        });

        $('.js-gallery-next').on('click', function() {
            that.flkty.next(true, true);
        });

        // bind updateCounter to any change (prev/next could be from arrow keys)
        this.flkty.on('change', function() {
            that.updateCounter();
        });
    }
    updateCounter() {
        let slideIndex = this.flkty.selectedIndex + 1;
        let slideTotal = this.flkty.slides.length;
        $('.js-gallery-counter-index').text(slideIndex);
        $('.js-gallery-counter-total').text(slideTotal);
    }
    bindCloseBtn() {
        let that = this;
        $('.js-gallery-close').click(function(e) {
            e.preventDefault();
            that.closeGallery();
        });
    }
    closeGallery() {
        this.$body.off('keydown.gallery');
        this.$body.removeClass('gallery-overlay-is-shown');
        this.flkty.destroy();
        // $('.js-gallery-prev, .js-gallery-next').unbind();
        let that = this;
        setTimeout(function() {
            that.$gallerySlides.empty();
        }, 600);
    }
    handleKeydown(e) {
        // escape
        if (e.which === 27) {
            e.preventDefault();
            this.closeGallery();
            // left arrow
        } else if (e.which === 37) {
            e.preventDefault();
            this.flkty.previous(true, true);

            // right arrow
        } else if (e.which === 39) {
            e.preventDefault();
            this.flkty.next(true, true);
        }
    }
}

export default ArticleGallery;
