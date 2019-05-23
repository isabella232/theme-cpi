import $ from 'jquery';
import wpUtil from './wp-util.js';

import ArticleScrolling from './components/article-scrolling';
import Comments from './components/comments';
import CopyToClipboard from './components/copy-to-clipboard';
import ArticleGallery from './components/article-gallery';
import GridOverlay from './components/grid-overlay';
import Menu from './components/menu';
import MemberForm from './components/member-form';
import Nav from './components/nav';
import HangingPunctuation from './components/hanging-punctuation';
import HomeFeatures from './components/home-features';
import ScrollableTables from './components/scrollable-tables';

$(document).ready(() => {
    new HangingPunctuation(); // must load before ArticleScrolling
    if ($('.js-article-gallery').length) {
        new ArticleGallery();
    }
    if ($('.article-content').length) {
        new ArticleScrolling();
    }
    if (['localhost', 'cpi.ups.dock'].includes(location.hostname)) {
        new GridOverlay();
    }
    if ($('.js-copy-to-clipboard').length) {
        new CopyToClipboard();
    }
    if ($('.article-comments').length) {
        new Comments();
    }
    new Nav();
    if ($('.member-form__radio').length) {
        new MemberForm();
    }
    if ($('.home-features__list').length) {
        new HomeFeatures();
    }
    if ($('.article-main table').length) {
        new ScrollableTables();
    }

    // Search
    if ($('#js-search-box').length > 0) {
        require.ensure(
            ['./components/search'],
            require => {
                const Search = require('./components/search').default;
                this.search = new Search();
            },
            'search'
        );
    }
});
