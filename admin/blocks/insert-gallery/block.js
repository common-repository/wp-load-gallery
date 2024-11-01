'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

(function (wpI18n, wpBlocks, wpElement, wpEditor, wpComponents) {
    var __ = wp.i18n.__;
    var Component = wpElement.Component,
        Fragment = wpElement.Fragment;
    var registerBlockType = wpBlocks.registerBlockType;
    var BlockControls = wpEditor.BlockControls;
    var TextControl = wpComponents.TextControl,
        Toolbar = wpComponents.Toolbar,
        IconButton = wpComponents.IconButton;

    var $ = jQuery;
    var el = wpElement.createElement;
    var iconblock = el('svg', { width: 24, height: 24, className: 'dashicon' }, el('path', { d: "M22 13h-8v-2h8v2zm0-6h-8v2h8V7zm-8 10h8v-2h-8v2zm-2-8v6c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V9c0-1.1.9-2 2-2h6c1.1 0 2 .9 2 2zm-1.5 6l-2.25-3-1.75 2.26-1.25-1.51L3.5 15h7z" }));

    var Wplg = function (_Component) {
        _inherits(Wplg, _Component);

        /**
         * Constructor
         */
        function Wplg() {
            _classCallCheck(this, Wplg);

            var _this = _possibleConstructorReturn(this, (Wplg.__proto__ || Object.getPrototypeOf(Wplg)).apply(this, arguments));

            _this.state = {
                isOpenList: false,
                searchValue: ''
            };

            _this.setWrapperRef = _this.setWrapperRef.bind(_this);
            _this.handleClickOutside = _this.handleClickOutside.bind(_this);
            return _this;
        }

        _createClass(Wplg, [{
            key: 'componentDidMount',
            value: function componentDidMount() {
                var attributes = this.props.attributes;
                var shortcode = attributes.shortcode;


                this.setState({
                    searchValue: shortcode
                });

                document.addEventListener('mousedown', this.handleClickOutside);
            }

            /**
             * Set the wrapper ref
             */

        }, {
            key: 'setWrapperRef',
            value: function setWrapperRef(node) {
                this.wrapperRef = node;
            }

            /**
             * Alert if clicked on outside of element
             */

        }, {
            key: 'handleClickOutside',
            value: function handleClickOutside(event) {
                if (this.wrapperRef && !this.wrapperRef.contains(event.target)) {
                    var _props = this.props,
                        attributes = _props.attributes,
                        setAttributes = _props.setAttributes;
                    var shortcode = attributes.shortcode;


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

        }, {
            key: 'selectGallery',
            value: function selectGallery(value) {
                var setAttributes = this.props.setAttributes;

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

        }, {
            key: 'search',
            value: function search(value) {
                var setAttributes = this.props.setAttributes;

                var galleriesSearchList = wplg_blocks.vars.galleries_select.filter(function (event) {
                    return event.label.toLowerCase().indexOf(value.toLowerCase()) > -1;
                });

                this.setState({ searchValue: value });

                setAttributes({
                    galleriesList: galleriesSearchList
                });
            }

            /**
             * Click to search input
             */

        }, {
            key: 'handleClick',
            value: function handleClick() {
                var setAttributes = this.props.setAttributes;

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

        }, {
            key: 'render',
            value: function render() {
                var _this2 = this;

                var _props2 = this.props,
                    attributes = _props2.attributes,
                    clientId = _props2.clientId;
                var galleriesList = attributes.galleriesList,
                    gallery_id = attributes.gallery_id,
                    cover = attributes.cover;


                return React.createElement(
                    Fragment,
                    null,
                    typeof cover !== "undefined" && React.createElement(
                        'div',
                        { className: 'wplg-cover' },
                        React.createElement('img', { src: cover })
                    ),
                    typeof cover === "undefined" && React.createElement(
                        'div',
                        { className: 'wp-block-shortcode', ref: this.setWrapperRef },
                        React.createElement(
                            'label',
                            null,
                            iconblock,
                            ' ',
                            wplg_blocks.l18n.block_title
                        ),
                        React.createElement(
                            'div',
                            { className: 'wp-load-gallery-block' },
                            React.createElement(TextControl, {
                                value: this.state.searchValue,
                                className: 'wplg_search_galleries',
                                placeholder: wplg_blocks.l18n.select_label,
                                autoComplete: 'off',
                                onChange: this.search.bind(this),
                                onClick: this.handleClick.bind(this)
                            }),
                            this.state.isOpenList && galleriesList.length && React.createElement(
                                'ul',
                                { className: 'wp-load-gallery-list' },
                                galleriesList.map(function (post) {
                                    return React.createElement(
                                        'li',
                                        { className: gallery_id.toString() === post.value.toString() ? 'wplg_item wplg_item_active' : 'wplg_item',
                                            'data-id': post.value,
                                            key: post.value,
                                            onClick: _this2.selectGallery.bind(_this2, post.value) },
                                        post.label
                                    );
                                })
                            ),
                            this.state.isOpenList && !galleriesList.length && React.createElement(
                                'ul',
                                { className: 'wp-load-gallery-list' },
                                React.createElement(
                                    'li',
                                    { key: '0' },
                                    wplg_blocks.l18n.no_post_found
                                )
                            )
                        )
                    )
                );
            }
        }]);

        return Wplg;
    }(Component);

    // register block


    registerBlockType('wplg/insert-gallery', {
        title: wplg_blocks.l18n.block_title,
        description: __('Load your gallery from list galleries and display them as a shortcode', 'wp-load-gallery'),
        icon: iconblock,
        category: 'common',
        keywords: [__('gallery', 'wp-load-gallery'), __('wplg', 'wp-load-gallery')],
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
                attribute: 'src'
            }
        },
        edit: Wplg,
        save: function save(_ref) {
            var attributes = _ref.attributes;
            var shortcode = attributes.shortcode;

            return shortcode;
        }
    });
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.components);
