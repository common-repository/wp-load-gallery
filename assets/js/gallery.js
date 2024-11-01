var wpGallery;
(function ($) {
    wpGalleryPopup = {
        callPopup: function() {
            if ($().magnificPopup) {
                var index = 0;
                $('.wplg-wrap-action-file .wplg-textpanel, .wplg-wrap-action-file .wplg-icon-type, .wplg-border-gallery-box .wplg-box-hover.wplg-wrap-action-file .wplg-gallery-box').unbind('click').bind('click', function (e) {
                    e.preventDefault();
                    var $this = $(this).closest('.wplg-gallery');
                    var items = wpGalleryPopup.galleryGetItems($this);
                    var href = $(this).closest('.wplg-gallery-item').find('.wplg-thumb-overlay').attr('href');
                    index = items.findIndex(x => x.src === href);
                    wpGalleryPopup.magnificPopup($this, items, index);
                });

                $('.wplg-wrap-action-file .wplg-thumb-overlay').unbind('click').bind('click', function (e) {
                    e.preventDefault();
                    if ($(this).closest('.wplg-border-gallery-box').length && $(this).closest('.wplg-gallery-masonry.wplg-box-hover').length) {
                        return;
                    }
                    var $this = $(this).closest('.wplg-gallery');
                    var items = wpGalleryPopup.galleryGetItems($this);
                    var href = $(this).closest('.wplg-gallery-item').find('.wplg-thumb-overlay').attr('href');
                    index = items.findIndex(x => x.src === href);
                    wpGalleryPopup.magnificPopup($this, items, index);
                });

                $('.wplg-wrap-action-link .wplg-textpanel, .wplg-wrap-action-link .wplg-icon-type, .wplg-border-gallery-box .wplg-box-hover.wplg-wrap-action-link .wplg-gallery-box').unbind('click').bind('click', function (e) {
                    e.preventDefault();
                    var url = $(this).closest('.wplg-gallery-item').find('.wplg-thumb-overlay').attr('href');
                    window.open(url);
                });
            }
        },

        magnificPopup: function (gallery, items, index) {
            $.magnificPopup.open({
                items: items,
                gallery: {
                    enabled: true,
                    tCounter: '<span class="mfp-counter">%curr% / %total%</span>',
                    arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>' // markup of an arrow button
                },
                showCloseBtn: true,
                callbacks: {
                    open: function(e) {
                        $.magnificPopup.instance.goTo(index);
                    }
                }
            });
        },

        /**
         * Get all items in gallery
         * @param gallery
         * @returns {Array}
         */
        galleryGetItems: function (gallery) {
            var items = [];
            gallery.find('.wplg-thumb-overlay').each(function () {
                var src = $(this).attr('href');
                var type = 'image';
                if ($(this).hasClass('isvideo')) {
                    type = 'iframe';
                }

                var pos = items.map(function (e) {
                    return e.src;
                }).indexOf(src);
                if (pos === -1) {
                    items.push({src: src, type: type, title: $(this).data('title')});
                }
            });

            return items;
        }
    };

    wpGallery = {
        gallery_items: [],
        getOptions: function ($this) {
            var options = $this.data('params');
            if (options.theme === 'compact') {
                options.slider_textpanel_title_font_size = options.title_font_size;
                options.slider_textpanel_desc_font_size = options.desc_font_size;
            }
            if (options.theme === 'tiles' || options.theme === 'justified_grid') {
                options.tile_textpanel_title_font_size = options.title_font_size;
                options.tile_textpanel_desc_font_size = options.desc_font_size;
            }
            options.animationDuration = 200;
            options.tile_textpanel_align = 'bottom';
            switch (options.textpanel_position) {
                case "top":
                    options.tile_textpanel_align = "top";
                case "bottom":
                    options.tile_textpanel_always_on = true;
                    options.tile_textpanel_offset = 0;
                    break;
                case "inside_top":
                    options.tile_textpanel_align = "top";
                    break;
                case "middle":
                    options.tile_textpanel_align = "middle";
                    options.textpanel_appear_type = "fade";
                    options.tile_textpanel_appear_type = "fade";
                    options.enable_icons = 0;
                    break;
            }

            return options;
        },

        paginationByNumberPage: function ($this, paged) {
            var options = wpGallery.getOptions($this.closest('.wplg-pagnination-wrap'));
            var $wrap = $this.closest('.wplg-wrap').find('.wplg-gallery');
            var gallery_id = $this.data('gallery_id');
            var total_pages = $this.closest('.wplg-pagnination-wrap').data('total_pages');
            $.ajax({
                url: wpgallery.ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wplg_loadmore_gallery",
                    gallery_id: gallery_id,
                    paged: paged,
                    options: JSON.stringify(options)
                },
                beforeSend: function () {
                    $this.closest('.wplg-wrap').addClass('wplg-gallery-swiching');
                },
                success: function (res) {
                    if (res.status) {
                        $this.closest('.wplg-wrap').removeClass('wplg-gallery-swiching');
                        if (options.theme === 'masonry' || options.theme === 'portfolio') {
                            $wrap.masonry('destroy');
                        }
                        $wrap.html(res.items);
                        var pagination = '';
                        var start = parseInt(paged) - 3;
                        if (start < 1) start = 1;
                        var end = parseInt(paged) + 3;
                        if (end > total_pages) end = total_pages;
                        if (parseInt(paged) > 1) {
                            pagination += '<span class="material-icons wplg-first-page" data-paged="1"> first_page </span>';
                            pagination += '<span class="material-icons wplg-prev-page" data-paged="'+ (parseInt(paged) - 1) +'"> navigate_before </span>';
                        }
                        for (var i = start; i<=end; i++) {
                            if (parseInt(i) === parseInt(paged)) {
                                pagination += '<span class="wplg-number-page active-page" data-paged="'+ i +'">'+ i +'</span>';
                            } else {
                                pagination += '<span class="wplg-number-page" data-paged="'+ i +'">'+ i +'</span>';
                            }
                        }
                        if (parseInt(paged) < parseInt(total_pages)) {
                            pagination += '<span class="material-icons wplg-next-page" data-paged="'+ (parseInt(paged) + 1) +'"> navigate_next </span>';
                            pagination += '<span class="material-icons wplg-first-page" data-paged="'+ total_pages +'"> last_page </span>';
                        }
                        $this.closest('.wplg-wrap').find('.wplg-pagnination-wrap').html(pagination);
                        wpGallery.doGallery($wrap);
                        wpGallery.galleryAction();
                    }
                }
            });
        },

        paginationByLoadMore: function ($this, paged) {
            var options = wpGallery.getOptions($this.closest('.wplg-pagnination-wrap'));
            var $wrap = $this.closest('.wplg-wrap').find('.wplg-gallery');
            var gallery_id = $this.data('gallery_id');
            var total_pages = $this.closest('.wplg-pagnination-wrap').data('total_pages');
            $.ajax({
                url: wpgallery.ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wplg_loadmore_gallery",
                    gallery_id: gallery_id,
                    paged: paged,
                    options: JSON.stringify(options)
                },
                beforeSend: function () {
                    $this.addClass('loadmore-loader');
                },
                success: function (res) {
                    $this.removeClass('loadmore-loader');
                    if (res.status) {
                        $this.data('paged', parseInt(paged) + 1);
                        // disable button
                        if (parseInt(paged) >= parseInt(total_pages)) {
                            $this.addClass('wplg-disable-loadmore');
                        }
                        if (options.theme === 'masonry' || options.theme === 'portfolio') {
                            var elems = [];
                            var items = wpGallery.createElementFromHTML(res.items);
                            $(items).find('.wplg-gallery-item').each(function () {
                                elems.push($(this).get(0));
                                $($(this).get(0)).hide().appendTo($wrap);
                            });

                            imagesLoaded($wrap, function () {
                                var column_width = $wrap.find('.wplg-gallery-item:first-child').outerWidth();
                                $(elems).css({
                                    'width': column_width + 'px',
                                    'opacity': 0
                                }).show();
                                $wrap.masonry('appended', $(elems));
                                $(elems).css('opacity', 1);
                                $wrap.find('.wplg-gallery-box').on('mouseover', function () {
                                    //setThumbColorOverlayEffect
                                    if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                        wpGallery.setTextpanelEffect($(this), true, options);
                                    }
                                }).on('mouseout', function () {
                                    if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                        wpGallery.setTextpanelEffect($(this), false, options);
                                    }
                                });

                                $wrap.find('.wplg-gallery-box').each(function () {
                                    wpGallery.positionElements($(this), options);
                                });
                                wpGalleryPopup.callPopup();
                            });
                        } else {
                            $wrap.append(res.items);
                            wpGallery.doGallery($wrap);
                        }
                    } else {
                        $this.addClass('wplg-disable-loadmore');
                    }
                }
            });
        },

        createElementFromHTML: function(htmlString) {
            var div = document.createElement('div');
            div.innerHTML = htmlString.trim();

            // Change this to div.childNodes to support multiple top-level nodes
            return div;
        },

        ajaxLoadGalleryByNavigation: function ($this, navigation_type) {
            var gallery_id = $this.data('gallery_id');
            var instance = $this.closest('.wplg-menu-navigation-wrap').data('instance');
            var root_gallery_id = $this.closest('.wplg-menu-navigation-wrap').data('gallery_id');
            var root_params = $this.closest('.wplg-menu-navigation-wrap').data('params');
            $.ajax({
                url: wpgallery.ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wplg_navigation_gallery",
                    gallery_id: gallery_id,
                    instance: instance,
                    navigation_type: navigation_type,
                    root_gallery_id: root_gallery_id,
                    root_params: JSON.stringify(root_params)
                },
                beforeSend: function () {
                    $this.closest('.wplg-menu-navigation-wrap').next().addClass('wplg-gallery-swiching');
                },
                success: function (res) {
                    if (res.status) {
                        $this.closest('.wplg-menu-navigation-wrap').next().remove();
                        $this.closest('.wplg-menu-navigation-wrap').find('.wplg_gallery_name').remove();
                        $this.closest('.wplg-menu-navigation-wrap').append('<span class="wplg_gallery_name">'+ res.name +'</span>');
                        $this.closest('.wplg-menu-navigation-wrap').after(res.items_html);
                        wpGallery.doGallery($this.closest('.wplg-menu-navigation-wrap').next());
                        if (navigation_type === 'folder') {
                            $this.closest('.wplg-menu-navigation-folder').html(res.navigation_html);
                        }
                        wpGallery.galleryAction();
                    }
                }
            });
        },

        calculateGrid: function ($container, options) {
            var columns = parseInt(options.columns);
            var gutterWidth = options.gutterwidth;
            var containerWidth = $container.width();
            if (parseInt(columns) < 2 || containerWidth <= 450) {
                columns = 2;
            }

            gutterWidth = parseInt(gutterWidth);

            var columnWidth = Math.floor(containerWidth / columns);

            return {columnWidth: columnWidth, gutterWidth: gutterWidth, columns: columns};
        },

        runMasonry: function ($container, options) {
            var $postBox = $container.children('.wplg-gallery-item');
            var o = wpGallery.calculateGrid($container, options);
            $postBox.css({'width': o.columnWidth + 'px'});
            $container.masonry({
                itemSelector: '.wplg-gallery-item',
                columnWidth: o.columnWidth,
                gutter: 0,
                transitionDuration: 0,
            });
        },

        doGallery: function ($this, autobrowse = true) {
            if ($this.hasClass('wplg-wrap')) {
                $this = $this.find('.wplg-gallery');
            }
            var options = wpGallery.getOptions($this);
            var theme = $this.data('theme');
            if ($this.hasClass('wplg-gallery-default') || $this.hasClass('wplg-gallery-masonry') || $this.hasClass('wplg-gallery-slider')) {
                if(!$this.is(':visible')){
                    return;
                }
                wpGalleryPopup.callPopup();
                imagesLoaded($this, function () {
                    $this.addClass('wplg-loaded');
                    switch (theme) {
                        case 'flip_box':
                            var ratio = parseFloat($this.data('ratio'));
                            var width = $this.find('.wplg-gallery-item').width();
                            var height = 100;
                            if (parseInt(width/(ratio)) > 50) {
                                height = width/(ratio);
                            }
                            $this.find('.wplg-gallery-item').height(height);
                            break;
                    }

                    if (theme === 'masonry') {
                        wpGallery.runMasonry($this, options);
                    }

                    if (theme === 'portfolio') {
                        wpGallery.runMasonry($this, options);
                    }

                    if (theme === 'flex' || theme === 'square_grid' || theme === 'slider' || theme === 'masonry') {
                        $this.find('.wplg-gallery-box').on('mouseover', function () {
                            //setThumbColorOverlayEffect
                            if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                wpGallery.setTextpanelEffect($(this), true, options);
                            }
                        }).on('mouseout', function () {
                            if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                wpGallery.setTextpanelEffect($(this), false, options);
                            }
                        });
                    }

                    $this.find('.wplg-gallery-box').each(function () {
                        switch (theme) {
                            case 'flip_box':
                                var $text_panel = $(this).find('.wplg-textpanel');
                                var height_text_panel = $(this).find('.wplg-textpanel').outerHeight();
                                $text_panel.css('top', 'calc(50% - ' + height_text_panel/2 + 'px)');
                                break;
                            case 'square_grid':
                            case 'flex':
                            case 'masonry':
                            case 'portfolio':
                            case 'default':
                            case 'post_grid':
                                wpGallery.positionElements($(this), options);
                                break;
                        }
                    });

                    if (theme === 'slider') {
                        var slick_args = {
                            centerMode: (parseInt(options.center_mode) === 1),
                            dots: (parseInt(options.enable_navigation) === 1),
                            infinite: true,
                            arrows: (parseInt(options.slider_arrows) === 1),
                            vertical: (parseInt(options.slider_vertical) === 1),
                            slidesToShow: parseInt(options.columns),
                            slidesToScroll: parseInt(options.columns),
                            autoplay: (parseInt(options.auto_animation) === 1),
                            autoplaySpeed: parseInt(options.duration),
                            rows: (typeof options.grid_num_rows !== "undefined" && (parseInt(options.slider_vertical) === 0)) ? parseInt(options.grid_num_rows) : 1,
                            responsive: [
                                {
                                    breakpoint: 1024,
                                    settings: {
                                        slidesToShow: 3,
                                        slidesToScroll: 3,
                                        infinite: true,
                                        dots: true
                                    }
                                },
                                {
                                    breakpoint: 600,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 2
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        slidesToShow: 1,
                                        slidesToScroll: 1
                                    }
                                }
                            ]
                        };

                        if (parseInt(options.enable_pagination) === 1 && options.pagination_type === 'lazyload') {
                            slick_args.lazyLoad = 'ondemand';
                        }

                        if (!$this.hasClass('slick-initialized')) {
                            $this.slick(slick_args);
                        }

                        $this.find('.wplg-gallery-box').each(function () {
                            wpGallery.positionElements($(this), options);
                        });
                    }
                    $this.find('.wplg-gallery-item').css('opacity', 1);

                    // lazy load
                    if (theme !== 'slider' && autobrowse) {
                        wpGallery.autobrowse($this, options);
                    }
                });
            } else {
                if($this.hasClass('wplg-loaded')) {
                     return;
                }

                if($this.closest('.vc_tta-panel').length){
                    if($this.closest('.vc_tta-panel').height() === 0){
                        return;
                    }
                }

                if (theme === 'justified_grid') {
                    options.gallery_theme = 'tiles';
                } else {
                    options.gallery_theme = theme;
                }

                if (options.link === 'none') {
                    options.tile_enable_action = false;
                }

                if (options.link === 'link') {
                    options.tile_as_link = true;
                }

                options.tile_enable_icons = (parseInt(options.enable_icons) === 1);
                options.tile_enable_outline = false;
                if (parseInt(options.enable_overlay) === 1) {
                    options.tile_enable_overlay = true;
                    options.tile_overlay_opacity = options.overlay_opacity;
                    options.tile_overlay_color = options.overlay_color;
                } else {
                    if (parseInt(options.enable_image_effect) === 1) {
                        options.tile_enable_image_effect = true;
                        options.tile_image_effect_reverse = false;
                        options.tile_image_effect_type = options.image_effect_type;
                    }
                    options.tile_enable_overlay = false;
                }

                if (parseInt(options.enable_textpanel) === 1) {
                    options.tile_enable_textpanel = true;
                    options.tile_textpanel_title_text_align = "center";
                    if (parseInt(options.textpanel_always_on) === 1) {
                        options.tile_textpanel_always_on = true;
                    }

                    if (parseInt(options.textpanel_enable_bg) === 0) {
                        options.textpanel_enable_bg = false;
                    }
                    options.tile_textpanel_source = options.textpanel_source;
                    options.tile_textpanel_appear_type = options.textpanel_appear_type;
                    options.tile_textpanel_position = options.textpanel_position;
                    options.tile_textpanel_title_text_align = options.textpanel_align;
                    options.tile_textpanel_desc_text_align = options.textpanel_align;
                    options.slider_textpanel_title_text_align = options.textpanel_align;
                    options.slider_textpanel_desc_text_align = options.textpanel_align;
                }

                if (parseInt(options.enable_border) === 1) {
                    options.tile_enable_border = true;
                    options.tile_border_width = options.border_width;
                    options.tile_border_radius = options.border_radius;
                    options.tile_border_color = options.border_color;
                } else {
                    options.tile_enable_border = false;
                }

                options.tile_enable_shadow = false;
                if (parseInt(options.gutterwidth) === 0) {
                    options.tile_enable_border = false;
                }

                switch (theme) {
                    case 'compact':
                        var thumb_width;
                        var thumb_height;
                        if (options.theme_panel_position === 'right' || options.theme_panel_position === 'left') {
                            thumb_height = parseInt(options.compact_height - parseInt(options.number_thumbnails)*parseInt(options.gutterwidth))/parseInt(options.number_thumbnails);
                            thumb_width = thumb_height * 5/4;
                            options.thumb_width = thumb_width;
                            options.thumb_height = thumb_height;
                        } else {
                            thumb_width = parseInt(options.gallery_width - parseInt(options.number_thumbnails)*parseInt(options.gutterwidth))/parseInt(options.number_thumbnails);
                            thumb_height = thumb_width * 9/16;
                            options.thumb_width = thumb_width;
                            options.thumb_height = thumb_height;
                        }
                        options.gallery_height = parseInt(options.compact_height);
                        options.strip_space_between_thumbs = parseInt(options.gutterwidth);
                        options.slider_textpanel_bg_opacity = options.textpanel_bg_opacity;
                        if (parseInt(options.enable_border) === 1) {
                            options.thumb_border_color = options.border_color;
                            options.thumb_over_border_color = options.border_color;
                            options.thumb_selected_border_width = options.border_width;
                            options.thumb_selected_border_color = options.border_color;
                            options.thumb_round_corners_radius = options.border_radius;
                        } else {
                            options.thumb_selected_border_width = 0;
                            options.thumb_round_corners_radius = 0;
                        }

                        if (parseInt(options.enable_overlay) === 1) {
                            options.thumb_border_effect = false;
                            options.thumb_color_overlay_effect = true;
                            options.thumb_overlay_reverse = false;
                            options.thumb_overlay_color = options.overlay_color;
                            options.thumb_overlay_opacity = options.overlay_opacity;
                        } else {
                            if (parseInt(options.enable_image_effect) === 1) {
                                options.thumb_color_overlay_effect = false;
                                options.thumb_image_overlay_effect = true;
                                options.thumb_image_overlay_type = options.image_effect_type;
                            } else {
                                options.thumb_overlay_opacity = 0;
                            }
                        }

                        options.slider_enable_text_panel = (parseInt(options.enable_textpanel) === 1);
                        switch (options.textpanel_source) {
                            case 'title':
                                options.textpanel_enable_title = true;
                                options.textpanel_enable_description = false;
                                break;
                            case 'desc':
                                options.textpanel_enable_description = true;
                                options.textpanel_enable_title = false;
                                break;
                            case 'title_and_desc':
                                options.textpanel_enable_title = true;
                                options.textpanel_enable_description = true;
                                break;
                        }
                        if (options.textpanel_position === 'inside_bottom') options.textpanel_position = 'bottom';
                        if (options.textpanel_position === 'inside_top') options.textpanel_position = 'top';
                        options.textpanel_align = options.textpanel_position;
                        options.gallery_autoplay = (parseInt(options.auto_animation) === 1);
                        options.gallery_play_interval = options.duration;
                        options.slider_textpanel_title_color = options.textpanel_title_color;
                        options.slider_textpanel_desc_color = options.textpanel_desc_color;
                        break;

                    case 'tiles':
                        options.tiles_type = 'nested'; // nested
                        options.tiles_space_between_cols = parseInt(options.gutterwidth);
                        options.tile_textpanel_title_color = options.textpanel_title_color;
                        options.tile_textpanel_desc_color = options.textpanel_desc_color;
                        options.tile_textpanel_padding_right = 30;
                        options.tile_textpanel_padding_left = 30;
                        if (parseInt(options.skin_on_hover) === 1 && options.skin_type === 'border-transition') {
                            if (options.textpanel_position === 'inside_bottom') {
                                options.tile_textpanel_padding_bottom = 28;
                                options.tile_textpanel_padding_top = 8;
                            }

                            if (options.textpanel_position === 'inside_top') {
                                options.tile_textpanel_padding_bottom = 8;
                                options.tile_textpanel_padding_top = 28;
                            }
                        }
                        break;

                    case 'justified_grid':
                        options.tiles_type = 'justified';
                        options.tiles_justified_space_between = parseInt(options.gutterwidth);
                        options.tile_textpanel_title_color = options.textpanel_title_color;
                        options.tile_textpanel_desc_color = options.textpanel_desc_color;
                        options.tiles_justified_row_height = options.justified_row_height;
                        options.tile_textpanel_padding_right = 30;
                        options.tile_textpanel_padding_left = 30;
                        if (parseInt(options.skin_on_hover) === 1 && options.skin_type === 'border-transition') {
                            if (options.textpanel_position === 'inside_bottom') {
                                options.tile_textpanel_padding_bottom = 28;
                                options.tile_textpanel_padding_top = 8;
                            }

                            if (options.textpanel_position === 'inside_top') {
                                options.tile_textpanel_padding_bottom = 8;
                                options.tile_textpanel_padding_top = 28;
                            }
                        }
                        break;

                }

                $this.unitegallery(options);
                $this.addClass('wplg-loaded');
                $('.ug-tile-icon').on('click', function (e) {
                    $this.closest('.ug-tile').trigger('click');
                });
            }
        },

        autobrowse: function ($wrap, options) {
            if (parseInt(options.enable_pagination) === 0 || (parseInt(options.enable_pagination) === 1 && options.pagination_type !== 'lazyload')) {
                return;
            }

            var count = $wrap.closest('.wplg-wrap').data('total_items');
            var number = parseInt(options.items_per_page);
            var offset = parseInt(options.items_per_page);
            var current = 0;
            $wrap.autobrowse(
                {
                    url: function (offset) {
                        $wrap.closest('.wplg-wrap').addClass('wplg-gallery-lazyloading');
                        var paged = (parseInt(offset)/parseInt(options.items_per_page) + 1);
                        return wpgallery.ajaxurl + '?action=wplg_loadmore_gallery&paged=' + paged;
                    },
                    postData: {
                        gallery_id:   options.gallery_id,
                        theme: options.theme,
                        options: JSON.stringify(options)
                    },
                    timeout: 100,
                    template: function (res) {
                        $wrap.closest('.wplg-wrap').removeClass('wplg-gallery-lazyloading');
                        if (res.status) {
                            current += parseInt(options.items_per_page);
                            if (options.theme === 'masonry' || options.theme === 'portfolio') {
                                var elems = [];
                                var items = wpGallery.createElementFromHTML(res.items);
                                $(items).find('.wplg-gallery-item').each(function () {
                                    elems.push($(this).get(0));
                                    $($(this).get(0)).hide().appendTo($wrap);
                                });

                                imagesLoaded($wrap, function () {
                                    var column_width = $wrap.find('.wplg-gallery-item:first-child').outerWidth();
                                    $(elems).css({
                                        'width': column_width + 'px',
                                        'opacity': 0
                                    }).show();
                                    $wrap.masonry('appended', $(elems));
                                    $(elems).css('opacity', 1);
                                    $wrap.find('.wplg-gallery-box').on('mouseover', function () {
                                        //setThumbColorOverlayEffect
                                        if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                            wpGallery.setTextpanelEffect($(this), true, options);
                                        }
                                    }).on('mouseout', function () {
                                        if (parseInt(options.enable_textpanel) === 1 && parseInt(options.textpanel_always_on) === 0) {
                                            wpGallery.setTextpanelEffect($(this), false, options);
                                        }
                                    });

                                    $wrap.find('.wplg-gallery-box').each(function () {
                                        wpGallery.positionElements($(this), options);
                                    });
                                    wpGalleryPopup.callPopup();
                                });
                            } else {
                                $wrap.append(res.items);
                                wpGallery.doGallery($wrap, false);
                            }
                        } else {
                            current += parseInt(options.items_per_page);
                        }
                    },
                    itemsReturned: function (response) {
                        if (parseInt(current) >= parseInt(count)) {
                            return 0;
                        }
                        return number;
                    },
                    offset: offset
                }
            );
        },

        galleryAction: function () {
            $('.wplg_gallery_loadmore').unbind('click').bind('click', function () {
                var $this = $(this);
                if ($this.hasClass('wplg-disable-loadmore')) {
                    return;
                }
                var paged = $this.data('paged');
                wpGallery.paginationByLoadMore($this, paged);
            });

            $('.wplg-number-page, .wplg-first-page, .wplg-last-page, .wplg-next-page, .wplg-prev-page').unbind('click').bind('click', function () {
                var $this = $(this);
                if ($this.hasClass('active-page')) {
                    return;
                }
                var paged = $this.data('paged');
                wpGallery.paginationByNumberPage($this, paged);
            });

            $('.wplg-navigation-title, .wplg_navigation_folder').unbind('click').bind('click', function () {
                var $this = $(this);
                var navigation_type = $this.closest('.wplg-menu-navigation-wrap').data('navigation_type');
                wpGallery.ajaxLoadGalleryByNavigation($this, navigation_type);
            });

            $('.wplg-gallery-item .wplg-icon-link').unbind('click').bind('click', function () {
                var href = $(this).data('href');
                window.open(href);
                return false;
            });
        },

        /* Init gallery */
        initGallery: function () {
            wpGallery.galleryAction();
            // Win dow resize
            $(window).on('resize', function () {
                $('.wplg-gallery-default, .wplg-gallery-slider').each(function () {
                    var $this = $(this);
                    wpGallery.doGallery($this);
                });
            });

            $('.wplg-gallery').each(function () {
                var $this = $(this);
                wpGallery.doGallery($this);
            });
        },

        /**
         * get size and position of some object
         */
        getElementSize: function (element) {
            if (element === undefined) {
                throw new Error("Can't get size, empty element");
            }
            var obj = element.position();
            obj.height = element.outerHeight();
            obj.width = element.outerWidth();

            obj.left = Math.round(obj.left);
            obj.top = Math.round(obj.top);

            obj.right = obj.left + obj.width;
            obj.bottom = obj.top + obj.height;

            return (obj);
        },

        /**
         * get text panel element from the tile
         */
        getTextPanelElement: function (objTile) {
            var objTextPanel = objTile.children(".wplg-textpanel");

            return (objTextPanel);
        },

        /**
         * set textpanel effect
         */
        setTextpanelEffect: function (objTile, isActive, options) {
            var objTextPanel = wpGallery.getTextPanelElement(objTile);
            if (!objTextPanel)
                return true;
            if (options.textpanel_appear_type === "slide") {
                var panelSize = wpGallery.getElementSize(objTextPanel);
                if (parseInt(panelSize.width) === 0)
                    return false;

                var startPos = -panelSize.height;
                var endPos = 0;
                if (parseInt(options.skin_on_hover) === 1 && options.skin_type === 'border-transition') {
                    endPos += 20;
                }
                var startClass = {}, endClass = {};
                var posName = "bottom";
                if (options.textpanel_position === "inside_top")
                    posName = "top";

                startClass[posName] = startPos + "px";
                endClass[posName] = endPos + "px";
                if (isActive) {
                    objTextPanel.fadeTo(0, 1);
                    if (objTextPanel.is(":animated") == false)
                        objTextPanel.css(startClass);
                    endClass["opacity"] = 1;
                    objTextPanel.stop(true).animate(endClass, options.animationDuration);
                } else {
                    objTextPanel.stop(true).animate(startClass, options.animationDuration);

                }
            } else {		//fade effect
                if (isActive) {
                    objTextPanel.stop(true).fadeTo(options.animationDuration, 1);
                } else {
                    objTextPanel.stop(true).fadeTo(options.animationDuration, 0);
                }
            }
        },

        /**
         * position text panel
         * panelType - default or clone
         */
        positionElements_textpanel: function (objTile, panelType, tileWidth, tileHeight, options) {

            var isPosition = (parseInt(options.textpanel_always_on) === 1 || options.textpanel_appear_type === "fade");
            if (isPosition) {
                var g_objPanel = wpGallery.getTextPanelElement(objTile);
                wpGallery.positionPanel(g_objPanel,options);
            }
        },

        /**
         * position the panel
         */
        positionPanel: function (g_objPanel, options) {
            var objCss = {};
                switch (options.tile_textpanel_align) {
                    case "top":
                        objCss.top = "0px";
                        if (parseInt(options.skin_on_hover) === 1 && options.skin_type === 'border-transition') {
                            objCss.top = "20px";
                        }
                        break;
                    case "bottom":
                        objCss.top = "auto";
                        objCss.bottom = "0px";
                        if (parseInt(options.skin_on_hover) === 1 && options.skin_type === 'border-transition') {
                            objCss.bottom = "20px";
                        }
                        break;
                    case "middle":
                        objCss.top = wpGallery.getElementRelativePos(g_objPanel, "middle", 0);
                        break;
                }

            g_objPanel.css(objCss);
        },

        getElementRelativePos: function (element, pos, offset, objParent) {

            if (!objParent)
                var objParent = element.parent();

            if (typeof element == "number") {
                var elementSize = {
                    width: element,
                    height: element
                };
            } else
                var elementSize = wpGallery.getElementSize(element);

            var parentSize = wpGallery.getElementSize(objParent);


            switch (pos) {
                case "top":
                case "left":
                    pos = 0;
                    if (offset)
                        pos += offset;
                    break;
                case "center":
                    pos = Math.round((parentSize.width - elementSize.width) / 2);
                    if (offset)
                        pos += offset;

                    break;
                case "right":
                    pos = parentSize.width - elementSize.width;
                    if (offset)
                        pos -= offset;
                    break;
                case "middle":
                    pos = Math.round((parentSize.height - elementSize.height) / 2);
                    if (offset)
                        pos += offset;
                    break;
                case "bottom":
                    pos = parentSize.height - elementSize.height;
                    if (offset)
                        pos -= offset;
                    break;
            }

            return (pos);
        },

        getButtonLink: function(objTile) {
            var objButton = objTile.find(".wplg-icon-link");
            if (objButton.length == 0)
                return (null);

            return objButton;
        },

        getButtonZoom: function(objTile) {
            var objButton = objTile.find(".wplg-icon-type");
            if (objButton.length == 0)
                return (null);

            return objButton;
        },

        /**
         * position the elements
         */
        positionElements: function (objTile, options) {
            var panel_theme_bottom = false;
            if (options.theme === 'default' || options.theme === 'portfolio' || options.theme === 'post_grid') {
                panel_theme_bottom = true;
            }

            var objButtonZoom = wpGallery.getButtonZoom(objTile);
            var objButtonLink = wpGallery.getButtonLink(objTile);
            var sizeTile = wpGallery.getElementSize(objTile);
            //position text panel:
            if (parseInt(options.enable_textpanel) === 1 && !panel_theme_bottom) {
                wpGallery.positionElements_textpanel(objTile, "regular", sizeTile.width, sizeTile.height, options);
            }

            //set vertical gap for icons
             if (objButtonZoom || objButtonLink) {
                 var gapVert = 0;
                 if (parseInt(options.enable_textpanel) === 1 && !panel_theme_bottom) {
                     var objTextPanelElement = wpGallery.getTextPanelElement(objTile);
                     var texPanelSize = wpGallery.getElementSize(objTextPanelElement);
                     if (texPanelSize.height > 0)
                         gapVert = Math.floor((texPanelSize.height / 2) * -1);
                 }
             }

             if (objButtonZoom && objButtonLink) {
                 var sizeZoom = wpGallery.getElementSize(objButtonZoom);
                 var sizeLink = wpGallery.getElementSize(objButtonLink);
                 var spaceBetween = 26;

                 var buttonsWidth = sizeZoom.width + spaceBetween + sizeLink.width;
                 var buttonsX = Math.floor((sizeTile.width - buttonsWidth) / 2);

                 //if space more then padding, calc even space.
                 if (buttonsX < spaceBetween) {
                     spaceBetween = Math.floor((sizeTile.width - sizeZoom.width - sizeLink.width) / 3);
                     buttonsWidth = sizeZoom.width + spaceBetween + sizeLink.width;
                     buttonsX = Math.floor((sizeTile.width - buttonsWidth) / 2);
                 }

                 wpGallery.placeElement(objButtonZoom, buttonsX, "middle", 0, gapVert);
                 wpGallery.placeElement(objButtonLink, buttonsX + sizeZoom.width + spaceBetween, "middle", 0, gapVert);

             } else {
                 if (objButtonZoom)
                     wpGallery.placeElement(objButtonZoom, "center", "middle", 0, gapVert);

                 if (objButtonLink)
                     wpGallery.placeElement(objButtonLink, "center", "middle", 0, gapVert);
             }

             if (objButtonZoom)
                 objButtonZoom.show();

             if (objButtonLink)
                 objButtonLink.show();
        },

        placeElement: function (element, left, top, offsetLeft, offsetTop, objParent) {
            if (jQuery.isNumeric(left) == false || jQuery.isNumeric(top) == false) {

                if (!objParent)
                    var objParent = element.parent();

                var elementSize = wpGallery.getElementSize(element);
                var parentSize = wpGallery.getElementSize(objParent);
            }

            //select left position
            if (jQuery.isNumeric(left) == false) {

                switch (left) {
                    case "left":
                        left = 0;
                        if (offsetLeft)
                            left += offsetLeft;
                        break;
                    case "center":
                        left = Math.round((parentSize.width - elementSize.width) / 2);
                        if (offsetLeft)
                            left += offsetLeft;
                        break;
                    case "right":
                        left = parentSize.width - elementSize.width;
                        if (offsetLeft)
                            left -= offsetLeft;
                        break;
                }
            }

            //select top position
            if (jQuery.isNumeric(top) == false) {

                switch (top) {
                    case "top":
                        top = 0;
                        if (offsetTop)
                            top += offsetTop;
                        break;
                    case "middle":
                    case "center":
                        top = Math.round((parentSize.height - elementSize.height) / 2);
                        if (offsetTop)
                            top += offsetTop;
                        break;
                    case "bottom":
                        top = parentSize.height - elementSize.height;
                        if (offsetTop)
                            top -= offsetTop;
                        break;
                }

            }


            var objCss = {
                "position": "absolute",
                "margin": "0px"
            };

            if (left !== null)
                objCss.left = left;

            if (top !== null)
                objCss.top = top;

            element.css(objCss);
        },

        /**
         * set thumb border effect
         */
        setThumbColorOverlayEffect: function (objThumb, isActive, noAnimation, options) {
            var objOverlay = objThumb.children(".wplg-thumb-overlay");
            var animationDuration = options.animationDuration;
            if (noAnimation && noAnimation === true)
                animationDuration = 0;
            if (isActive) {
                objOverlay.stop(true).fadeTo(animationDuration, 1);
            } else {
                objOverlay.stop(true).fadeTo(animationDuration, 0);
            }
        }
    };

    $(document).ready(function () {
        wpGallery.initGallery();


        setTimeout(function () {
            $('.responsive-tabs__list__item').on('click', function () {
                var target = $(this).attr('aria-controls');
                var container = $('#' + target).find('.wplg-gallery');
                if (container.length) {
                    setTimeout(function () {
                        wpGallery.initGallery();
                    }, 200);
                }
            });

            $('.tabtitle.responsive-tabs__heading').on('click', function () {
                var container = $(this).next('.tabcontent.responsive-tabs__panel').find('.wplg-gallery');
                if (container.length) {
                    setTimeout(function () {
                        wpGallery.initGallery();
                    }, 200);
                }
            });

            $('.pp-tabs-labels .pp-tabs-label').on('click', function () {
                wpGallery.initGallery();
            });

            $('.elementor-tab-title, .vc_tta-tab').on('click', function () {
                setTimeout(function () {
                    wpGallery.initGallery();
                }, 200);

            });
        }, 1000);
    });

    $(document.body).on('post-load', function () {
        wpGallery.initGallery();
    });

    $(document.body).on('wps-toggled', function () {
        wpGallery.initGallery();
    });
})(jQuery);
