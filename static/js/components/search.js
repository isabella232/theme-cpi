import instantsearch from 'instantsearch.js';
import { searchBox, hits, stats, pagination } from 'instantsearch.js/es/widgets';
window.instantsearch = instantsearch;

class Search {
    constructor(el) {
        let indexName = this.getIndexPrefix() + 'searchable_posts';
        const search = instantsearch({
            appId: '80OAQO009N',
            apiKey: '160105423d71a82ca569c953d580714a',
            indexName: indexName,
            routing: true,
            searchFunction: function(helper) {
                if (helper.state.query === '') {
                    $('.search-results, .pagination--algolia, .search-results__stats').hide();
                    return;
                }
                helper.search();
                $('.search-results, .pagination--algolia, .search-results__stats').show();
            },
        });

        // initialize SearchBox
        search.addWidget(
            searchBox({
                container: '#js-search-box',
                placeholder: 'Search by keyword(s)',
                autofocus: false,
                cssClasses: {
                    root: 'search-input',
                },
            })
        );
        search.addWidget(
            stats({
                autoHideContainer: false,
                container: '#js-stats-container',
                templates: {
                    body: function(data) {
                        let pageNum = data.page + 1;
                        let output = '';
                        if (data.hasManyResults) {
                            output += `<span class="search-results__feedback">${
                                data.nbHits
                            } articles found</span>`;
                            output += `<span class="search-results__feedback search-results__feedback--page">Page ${pageNum} of ${
                                data.nbPages
                            }</span>`;
                        } else if (data.hasOneResult) {
                            output += `<span class="search-results__feedback">1 article found</span>`;
                        } else if (data.hasNoResults) {
                            output += `<span class="search-results__feedback search-results__feedback--none">No articles found for “${
                                data.query
                            }”</span>`;
                        }
                        return output;
                    },
                },
            })
        );

        search.addWidget(
            hits({
                container: '#js-search-results',
                templates: {
                    empty: '',
                    // empty: wp.template('instantsearch-no-results'),
                    item: wp.template('instantsearch-hit'),
                },
            })
        );

        /* Pagination widget */
        search.addWidget(
            pagination({
                container: '#js-search-pagination',
                padding: 0,
                showFirstLast: false,
                labels: {
                    previous:
                        '<span class="pagination__arrow pagination__arrow--prev" rel="prev"><span class="aria-hidden">Previous Page</span><svg><use xlink:href="#page-arrow-left"></use></svg></span>',
                    next:
                        '<span class="pagination__arrow pagination__arrow--next" rel="next"><span class="aria-hidden">Next Page</span><svg><use xlink:href="#page-arrow-right"></use></svg></span>',
                },
                cssClasses: {
                    root: 'pagination__controls',
                    previous: 'pagination__back',
                    next: 'paginatiom__forward',
                },
            })
        );

        search.start();
    }

    getIndexPrefix() {
        let prefix = 'dev_wp_';
        if (window.location.hostname.indexOf('publicintegrity.org') !== -1) {
            prefix = 'live_wp_';
        } else if (window.location.hostname === 'live-public-integrity.pantheonsite.io') {
            prefix = 'live_wp_';
        } else if (window.location.hostname === 'test-public-integrity.pantheonsite.io') {
            prefix = 'test_wp_';
        } else if (window.location.hostname === 'dev-public-integrity.pantheonsite.io') {
            prefix = 'test_wp_';
        } else {
            prefix = 'test_wp_';
        }
        return prefix;
    }
}

export default Search;
