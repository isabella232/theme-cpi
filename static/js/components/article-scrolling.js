class ArticleChapters {
    constructor() {
        // For new articles, build chapters from h2s
        // For legacy articles, use h4s but add "Introduction" h2 built into template)
        this.$body = $('body');
        let isLegacy = this.$body.hasClass('tag-legacy');
        this.$headings = isLegacy
            ? $(
                  'body.tag-legacy .article-main .basic-text > h4, .article-main .basic-text h2.chapter-heading'
              )
            : $('.article-main .basic-text > h2 ');

        this.$sidebarTarget = $('.article-chapters');
        this.docOutline = [];
        this.mobileMax = 960;
        this.mobileMql =
            'matchMedia' in window ? window.matchMedia(`(min-width: ${this.mobileMax}px)`) : false;
        this.lastScrollPos = 0;

        if (this.$headings.length > 1) {
            this.buildDocOutline();
            this.buildChapters();
        }
        this.bindScrollToProgress();
    }

    buildDocOutline() {
        this.$headings.addClass('article-section-heading');
        let that = this;

        $('.article-section-heading').each(function(index) {
            let indexSuffix = index + 1;
            let headingId = 'part-' + indexSuffix;
            let headingText = $(this).text();
            let headingPos = parseInt($(this).position().top);
            let headingLinkID = 'chapter-link-to-' + headingId;

            // add IDs to headings
            $(this).attr('id', headingId);

            that.docOutline.push({
                $heading: $(this),
                text: headingText,
                id: headingId,
                linkId: headingLinkID,
            });

            // Group text and h2s into <section>s
            $(this)
                .nextUntil('.article-section-heading')
                .addBack()
                .wrapAll('<section class="article-section"></section>');
        });
    }

    buildChapters() {
        let that = this;
        // Add Container
        that.$sidebarTarget.append(`<ul class="article-chapters__list"></ul>`);

        that.docOutline.forEach(function(item, index) {
            $('.article-chapters__list').append(
                `<li class="article-chapters__item">
                    <a class="article-chapters__link" id="${item.linkId}" href="#${item.id}">
                        <span>${item.text}</span>
                    </a>
                    <span class="article-chapters__progress"></span>
                </li>`
            );
        });

        // Animate scrollTo when clicking chapter-link
        $('.article-chapters__link').click(function(e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: $($(this).attr('href')).offset().top }, 1500);
        });
    }

    bindScrollToProgress() {
        let that = this;
        $(window).scroll(
            this.throttle(function() {
                let scrollPos = $(window).scrollTop();
                that.updateProgress(scrollPos);
                that.updateStickyNav(scrollPos);
            }, 100)
        );
    }

    updateStickyNav(scrollPos) {
        // sticky nav
        if (scrollPos < this.lastScrollPos) {
            this.$body.addClass('js-scrolling-up');
        } else {
            this.$body.removeClass('js-scrolling-up');
        }

        if ($('div.article-content').position()) {
            if (scrollPos > $('.article-content').position().top) {
                this.$body.addClass('js-scrolled-to-article-content');
            } else {
                this.$body.removeClass('js-scrolled-to-article-content');
            }
        }
        this.lastScrollPos = scrollPos <= 0 ? 0 : scrollPos;
    }

    updateProgress(scrollPos) {
        let viewportHeight = $(window).height();
        let viewportMidScroll = scrollPos - 0.7 * viewportHeight;

        let that = this;

        this.docOutline.forEach(function(item, index) {
            let $linkItem = $('#' + item.linkId).closest('li');
            let $section = item.$heading.closest('.article-section');

            let sectionTop = $section.position().top;
            let sectionHeight = $section.height();
            let sectionBottom = sectionTop + sectionHeight;

            // Set booleans for clarity
            let sectionActive = viewportMidScroll > sectionTop && viewportMidScroll < sectionBottom;
            let sectionIsPast = viewportMidScroll > sectionBottom;

            // Flag and unflag active/complete sections
            // Also set progress as a decimal from 0 to 1
            let sectionProgressDecimal = 0;

            if (sectionActive) {
                $linkItem.addClass('article-chapters__item--active');
                sectionProgressDecimal = (viewportMidScroll - sectionTop) / sectionHeight;
            } else {
                $linkItem.removeClass('article-chapters__item--active');
            }

            if (sectionIsPast) {
                $linkItem.addClass('article-chapters__item--complete');
                sectionProgressDecimal = 1;
            } else {
                $linkItem.removeClass('article-chapters__item--complete');
            }

            // Update progress meter inside this item
            let barProgressPct = sectionProgressDecimal * 100 + '%';

            // Apply percentage to width if mobile, height if desktop

            let progressProperty = that.mobileMql.matches ? 'height' : 'width';
            let progressOtherProperty = progressProperty == 'height' ? 'width' : 'height';

            $linkItem
                .find('.article-chapters__progress')
                .css(progressProperty, barProgressPct)
                .css(progressOtherProperty, '1px');
        });
    }

    throttle(fn, threshhold, scope) {
        threshhold || (threshhold = 250);
        var last, deferTimer;
        return function() {
            var context = scope || this;

            var now = +new Date(),
                args = arguments;
            if (last && now < last + threshhold) {
                // hold on to it
                clearTimeout(deferTimer);
                deferTimer = setTimeout(function() {
                    last = now;
                    fn.apply(context, args);
                }, threshhold);
            } else {
                last = now;
                fn.apply(context, args);
            }
        };
    }
}

export default ArticleChapters;
