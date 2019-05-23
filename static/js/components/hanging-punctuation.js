class HangingPunctuation {
    constructor() {
        this.$container = $('.js-hang-punc');
        this.punctuationMarks = {
            '\u201c': 'medium', // “ - ldquo - left smart double quote
            '\u2018': 'small', // ‘ - lsquo - left smart single quote
            '\u0022': 'medium', // " - ldquo - left dumb double quote
            '\u0027': 'small', // ' - lsquo - left dumb single quote
            '\u00AB': 'large', // « - laquo - left double angle quote
            '\u2039': 'medium', // ‹ - lsaquo - left single angle quote
            '\u201E': 'medium', // „ - bdquo - left smart double low quote
            '\u201A': 'small', // ‚ - sbquo - left smart single low quote
        };

        if (this.$container.length > 0) {
            this.hangPunc();
        }
    }

    hangPunc() {
        const containerChildren = this.$container.children();

        // Loop over all direct descendants of the $container
        // If it's a blockquote, loop over its direct descendants
        for (let i = 0; i < containerChildren.length; i += 1) {
            const el = containerChildren[i];

            // For blockquotes, which are marked up as <figure class="wp-block-pullquote"><blockquote><p>"Text"</p><cite>Citation</cite></blockquote></figure>,
            // target the paragraph only:
            if (el.tagName === 'FIGURE') {
                let $blockquote_paragraph = $(el).find('blockquote > p');
                if ($blockquote_paragraph.length) {
                    this.hangIfEligible($blockquote_paragraph[0]);
                }
            } else {
                this.hangIfEligible(el);
            }
        }
    }

    hangIfEligible(el) {
        const text = el.innerText || el.textContent;
        let htmlClass = 'hang-punc-';

        // for (const mark in this.punctuationMarks) {
        const marks = Object.keys(this.punctuationMarks);
        marks.forEach(mark => {
            if (text.indexOf(mark) === 0) {
                if (
                    el.tagName === 'H1' ||
                    el.tagName === 'H2' ||
                    el.tagName === 'H3' ||
                    el.tagName === 'H4' ||
                    el.tagName === 'H5'
                ) {
                    htmlClass += 'header-';
                }
                el.classList.add(htmlClass + this.punctuationMarks[mark]);
            }
        });
    }

    createTips() {
        $(this.toolTipTargets).each((i, el) => {
            const $el = $(el);
            const tipURL = new URL($el.attr('href'));
            const $toolTip = $('<span/>');
            $toolTip.text(tipURL.hostname);
            $toolTip.addClass('tool-tip');
            $el.append($toolTip);
        });
    }
}

export default HangingPunctuation;
