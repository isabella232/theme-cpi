//  Import CSS.
import './editor.scss';

class StationId {
    constructor(el) {
        this.registerMyBlock();
    }

    registerMyBlock() {
        const { __ } = wp.i18n;
        const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

        registerBlockType('cpi/station-id', {
            title: __('Station Identification'), // Block title.
            icon: {
                src: 'nametag',
                background: '#ecf6f6',
            },
            category: 'widgets',
            keywords: [__('station id'), __('id')],
            attributes: {
                content: {
                    source: 'html',
                    selector: 'p',
                },
            },

            /**
             * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
             */
            edit({ className }) {
                return (
                    <div className={className}>
                        <h3>Who We Are</h3>
                        <p>{whoWeAre}</p>
                    </div>
                );
            },

            save({ attributes, className }) {
                return null;
            },
        });
    }
}

export default StationId;
