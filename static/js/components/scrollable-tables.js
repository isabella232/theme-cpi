import $ from 'jquery';

class ScrollableTables {
    constructor() {
        this.$articleTables = $('.article-main table');
        this.columnCountThreshold = 5;
        this.approxPixelsPerColumn = 130;
        this.wrapWideTables();
    }
    wrapWideTables() {
        let that = this;
        this.$articleTables.each(function() {
            let $this = $(this);
            let columnCount = $this.find('tbody > tr:first > td').length;

            // check this table's column count is above the threshold setting,
            // or the admins have added the wide class
            if ($this.hasClass('wide') || columnCount >= that.columnCountThreshold) {
                // create a wrapping div and copy the table's classes to it
                $this.wrap(function() {
                    let tableClasses = $this.attr('class');
                    return `<div class="scrollable-table-wrap ${tableClasses}"></div>`;
                });
                // remove the table's original classes
                $this.attr('class', '');
                let tableMinWidth = that.approxPixelsPerColumn * columnCount;
                // set a min width based on # of columns and approxPixelsPerColumn
                $(this).attr('style', `min-width: ${tableMinWidth}px`);
            }
        });
    }
}

export default ScrollableTables;
