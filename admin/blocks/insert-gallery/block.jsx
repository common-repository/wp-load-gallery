(function (wpI18n, wpBlocks, wpElement, wpEditor, wpComponents) {
    const {__} = wp.i18n;
    const {Component, Fragment} = wpElement;
    const {registerBlockType} = wpBlocks;
    const {BlockControls} = wpEditor;
    const {TextControl, Toolbar, IconButton} = wpComponents;
    const $ = jQuery;
    const el = wpElement.createElement;
    const iconblock = el('svg', {width: 24, height: 24, className: 'dashicon'},
        el('path', {d: "M22 13h-8v-2h8v2zm0-6h-8v2h8V7zm-8 10h8v-2h-8v2zm-2-8v6c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V9c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2zm-1.5 6l-2.25-3-1.75 2.26-1.25-1.51L3.5 15h7z"})
    );

    class Wplg extends Component {
        /**
         * Constructor
         */
        constructor() {
            super(...arguments);
            this.state = {
                isOpenList: false,
                searchValue: ''
            };

            this.setWrapperRef = this.setWrapperRef.bind(this);
            this.handleClickOutside = this.handleClickOutside.bind(this);
        }

        componentDidMount() {
            const {attributes} = this.props;
            const {
                shortcode
            } = attributes;

            this.setState({
                searchValue: shortcode
            });


            document.addEventListener('mousedown', this.handleClickOutside);
        }

        /**
         * Set the wrapper ref
         */
        setWrapperRef(node) {
            this.wrapperRef = node;
        }

        /**
         * Alert if clicked on outside of element
         */
        handleClickOutside(event) {
            if (this.wrapperRef && !this.wrapperRef.contains(event.target)) {
                const {attributes, setAttributes} = this.props;
                const {
                    shortcode
                } = attributes;

                this.setState({
                    isOpenList: false,
                    searchValue: shortcode
                });
                setAttributes({
                    shortcode: shortcode
                });
            }
        }

        /**
         * Select galleries post
         */
        selectGallery(value) {
            const {setAttributes} = this.props;
            this.setState({
                isOpenList: false,
                searchValue: '[wplg_gallery gallery_id="' + value + '"]'
            });

            setAttributes({
                gallery_id: value.toString(),
                shortcode: '[wplg_gallery gallery_id="' + value + '"]'
            });
        }

        /**
         * DO search galleries post
         */
        search(value) {
            const {setAttributes} = this.props;
            let galleriesSearchList = wplg_blocks.vars.galleries_select.filter(function (event) {
                return event.label.toLowerCase().indexOf(value.toLowerCase()) > -1
            });

            this.setState({searchValue: value});

            setAttributes({
                galleriesList: galleriesSearchList
            });
        }

        /**
         * Click to search input
         */
        handleClick() {
            const {setAttributes} = this.props;
            setAttributes({
                galleriesList: wplg_blocks.vars.galleries_select
            });

            this.setState({
                isOpenList: true,
                searchValue: ''
            });
        }

        /**
         * Render block
         */
        render() {
            const {attributes, clientId} = this.props;
            const {
                galleriesList,
                gallery_id,
                cover
            } = attributes;

            return (
                <Fragment>
                    {
                        typeof cover !== "undefined" && <div className="wplg-cover"><img src={cover} /></div>
                    }
                    {
                        typeof cover === "undefined" &&
                        <div className="wp-block-shortcode" ref={this.setWrapperRef}>
                            <label>{iconblock} {wplg_blocks.l18n.block_title}</label>

                            <div className="wp-load-gallery-block">
                                <TextControl
                                    value={this.state.searchValue}
                                    className="wplg_search_galleries"
                                    placeholder={wplg_blocks.l18n.select_label}
                                    autoComplete="off"
                                    onChange={this.search.bind(this)}
                                    onClick={this.handleClick.bind(this)}
                                />

                                {
                                    this.state.isOpenList && galleriesList.length &&
                                    <ul className="wp-load-gallery-list">
                                        {
                                            galleriesList.map((post) =>
                                                <li className={(gallery_id.toString() === post.value.toString()) ? 'wplg_item wplg_item_active' : 'wplg_item'}
                                                    data-id={post.value}
                                                    key={post.value}
                                                    onClick={this.selectGallery.bind(this, post.value)}>{post.label}</li>
                                            )
                                        }
                                    </ul>
                                }

                                {
                                    this.state.isOpenList && !galleriesList.length &&
                                    <ul className="wp-load-gallery-list">
                                        <li key="0">{wplg_blocks.l18n.no_post_found}</li>
                                    </ul>
                                }
                            </div>
                        </div>
                    }
                </Fragment>
            );
        }
    }

    // register block
    registerBlockType('wplg/insert-gallery', {
        title: wplg_blocks.l18n.block_title,
        description: __('Load your gallery from list galleries and display them as a shortcode', 'wp-load-gallery'),
        icon: iconblock,
        category: 'common',
        keywords: [
            __('gallery', 'wp-load-gallery'),
            __('wplg', 'wp-load-gallery')
        ],
        example: {
            attributes: {
                cover: wplg_blocks.vars.block_cover
            }
        },
        attributes: {
            galleriesList: {
                type: 'array',
                default: wplg_blocks.vars.galleries_select
            },
            gallery_id: {
                type: 'string',
                default: '0'
            },
            shortcode: {
                type: 'string',
                default: ''
            },
            cover: {
                type: 'string',
                source: 'attribute',
                selector: 'img',
                attribute: 'src',
            },
        },
        edit: Wplg,
        save: ({attributes}) => {
            const {
                shortcode
            } = attributes;
            return shortcode;
        }
    });
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.components);