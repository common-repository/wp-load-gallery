/**
 * Main WP Media Gallery addon script
 */
var wpGalleryModule;
(function ($) {
    if (typeof ajaxurl === "undefined") {
        ajaxurl = wp.vars.ajaxurl;
    }

    wpGalleryModule = {
        wp_current_gallery: 0, // current gallery selected
        gallery_details: {},
        radio_field: [],
        events: [], // event handling
        init: function () {
            wpGalleryModule.radio_field = ['gallery_navigation', 'gallery_full_width', 'auto_animation', 'scale_image', 'enable_icons', 'enable_border', 'enable_overlay', 'enable_image_effect', 'enable_textpanel', 'textpanel_always_on', 'textpanel_enable_bg', 'enable_navigation', 'enable_pagination', 'enable_price', 'include_children', 'skin_on_hover', 'slider_vertical', 'slider_arrows', 'show_author', 'show_date', 'center_mode'];
            // show popup inline
            if ($().magnificPopup) {
                $('.add-gallery-box').magnificPopup({
                    type: 'inline',
                    closeBtnInside: true,
                    midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
                });
            }

            $('.wplg_box').on('click', function () {
                if ($(this).closest('.wplg-panel-wrap').hasClass('wplg_box_closed')) {
                    $(this).find('.wplg_box_icon').addClass('dashicons-arrow-up').removeClass('dashicons-arrow-down');

                    $(this).closest('.wplg-panel-wrap').find('.wplg_box_content').slideDown(200);
                    $(this).closest('.wplg-panel-wrap').removeClass('wplg_box_closed');
                } else {
                    $(this).find('.wplg_box_icon').addClass('dashicons-arrow-down').removeClass('dashicons-arrow-up');

                    $(this).closest('.wplg-panel-wrap').find('.wplg_box_content').slideUp(200);
                    $(this).closest('.wplg-panel-wrap').addClass('wplg_box_closed');
                }
            });

            $('.wplg-nav-tab').on('click', function () {
                var tab = $(this).data('tab');
                $('.wplg-nav-tab').removeClass('wplg-nav-tab-active');
                $(this).addClass('wplg-nav-tab-active');
                $('.wplg-content').addClass('hide').removeClass('show');
                $('#' + tab).addClass('show').removeClass('hide');
            });

            $('.edit-gallery-name').on('keyup', function () {
                $('.gallery_tree li[data-id="'+ wpGalleryModule.wp_current_gallery +'"] > .wplg_row_tree .wplg_tree_title_folder .title-text').html($(this).val());
            });

            $('.wplg-tree-toggle').unbind('click').click(function () {
                var leftPanel = $('.gallerylist');
                var wpLeftPanel = $('#adminmenuwrap');
                var rtl = $('body').hasClass('rtl');

                if (leftPanel.is(':visible')) {
                    if (wpLeftPanel.is(':visible')) {
                        if (!rtl) {
                            $(this).css('left', 35);
                        } else {
                            $(this).css('right', 35);
                        }
                    } else {
                        if (!rtl) {
                            $(this).css('left', 0);
                        } else {
                            $(this).css('right', 0);
                        }
                    }
                } else {
                    if (wpLeftPanel.is(':visible')) {
                        if (!rtl) {
                            $(this).css('left', 335);
                        } else {
                            $(this).css('right', 335);
                        }
                    } else {
                        if (!rtl) {
                            $(this).css('left', 270);
                        } else {
                            $(this).css('right', 270);
                        }
                    }
                }

                leftPanel.toggle()
            });

            wpGalleryModule.bindEvent();
            wpGalleryModule.eventImages();
        },

        /**
         * Change gallery function
         * @param id id of gallery
         */
        changeGallery: function (id) {
            if (typeof id === 'undefined') {
                return;
            }

            if (parseInt(id) === 0) {
                return;
            }

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "wpgallery",
                    task: "change_gallery",
                    id: id,
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                beforeSend: function () {
                    $('.wplg-nav-tab[data-tab="main-gallery"]').click();
                    $('.wplg-admin-overlay-tree').show();
                    $('.edit_wrap').addClass('wp-settings-disable').removeClass('wp-settings-enable wp-settings-disable-top');
                },
                success: function (res) {
                    $('.wplg-admin-overlay-tree').hide();
                    $('.edit_wrap').addClass('wp-settings-enable').removeClass('wp-settings-disable');
                    $('.wplg_gallery_id').val(id);
                    $('.edit_wrap .wplg-images-wrap').html(res.images_html);
                    $('.server_folders_gallery_output').html(res.folders_html);
                    // bind event
                    if (res.status) {
                        wpGalleryModule.eventImages();
                    }

                    /* Load image template */
                    $('.edit_wrap .gallery_name').val(res.name);
                    $('.edit-gallery-theme').val(res.theme);
                    $('.wplg-panel-theme').removeClass('theme-selected');
                    $('.wplg-panel-theme[data-theme="' + res.theme + '"]').addClass('theme-selected');

                    wpGalleryModule.loadOptions(res.options);
                    wpGalleryModule.renderShortCode(id);
                    wpGalleryModule.bindEvent();
                    wpGalleryModule.uploadImages();
                }
            });
        },

        renderShortCode: function(id) {
            var shortcode = '[wplg_gallery';
            shortcode += ' gallery_id="' + id + '"';
            shortcode += ']';

            var custom_shortcode = '[wplg_gallery';
            custom_shortcode += ' gallery_id="' + id + '"';
            var radio_field = wpGalleryModule.radio_field;
            var value;
            $('.wplg-form-edit select').each(function () {
                var name = $(this).attr('name');
                value = $(this).val();
                custom_shortcode += ' ' + name + '="' + value + '"';
            });

            $('.wplg-form-edit input').each(function () {
                var name = $(this).attr('name');
                if (typeof name !== "undefined") {
                    if (radio_field.indexOf(name) !== -1) {
                        if ($(this).is(':checked')) {
                            value = 1;
                        } else {
                            value = 0;
                        }
                    } else {
                        value = $(this).val();
                    }
                    custom_shortcode += ' ' + name + '="' + value + '"';
                }
            });
            custom_shortcode += ']';

            $('.wplg-shortcode-value').val(shortcode);
            $('.wplg-custom-shortcode').val(custom_shortcode);

            $('.wplg-shortcode-value').unbind('click').bind('click',function () {
                var shortcode_value = $('.wplg-shortcode-value').val();
                wpGalleryModule.setClipboardText(shortcode_value, wplg_objects.l18n.shortcode_copied, wplg_objects.l18n.shortcode_failed);
            });

            $('.wplg-custom-shortcode').unbind('click').bind('click',function () {
                var shortcode_value = $('.wplg-custom-shortcode').val();
                wpGalleryModule.setClipboardText(shortcode_value, wplg_objects.l18n.shortcode_copied, wplg_objects.l18n.shortcode_failed);
            });
        },

        loadOptions: function(options) {
            var radio_field = wpGalleryModule.radio_field;
            $.each(options, function (key, value) {
                if (radio_field.indexOf(key) !== -1) {
                    if (parseInt(value) === 1) {
                        $('.wplg-configs .' + key ).prop('checked', true);
                    } else {
                        $('.wplg-configs .' + key ).prop('checked', false);
                    }
                } else {
                    $('.wplg-configs .' + key ).val(value);
                }
            });

            $('.wplg-configs .wplg-color_check').remove();
            $('.wplg-configs .wplg-background-color[data-color="' + options.background + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            $('.wplg-configs .wplg-overlay-color[data-color="' + options.overlay_color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            $('.wplg-configs .wplg-border-color[data-color="' + options.border_color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            $('.wplg-configs .wplg-textpanel-bg-color[data-color="' + options.textpanel_bg_color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            $('.wplg-configs .wplg-textpanel-title-color[data-color="' + options.textpanel_title_color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            $('.wplg-configs .wplg-textpanel-desc-color[data-color="' + options.textpanel_desc_color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
        },

        /**
         * Copy value
         *
         * @param string text Value
         * @param string msg_success Message success
         * @param string msg_error Message error
         *
         * @return void
         */
        setClipboardText: function setClipboardText(text, msg_success, msg_error) {
            var id = "mycustom-clipboard-textarea-hidden-id";
            var existsTextarea = document.getElementById(id);

            if (!existsTextarea) {
                var textarea = document.createElement("textarea");
                textarea.id = id;
                // Place in top-left corner of screen regardless of scroll position.
                textarea.style.position = 'fixed';
                textarea.style.top = 0;
                textarea.style.left = 0;

                // Ensure it has a small width and height. Setting to 1px / 1em
                // doesn't work as this gives a negative w/h on some browsers.
                textarea.style.width = '1px';
                textarea.style.height = '1px';

                // We don't need padding, reducing the size if it does flash render.
                textarea.style.padding = 0;

                // Clean up any borders.
                textarea.style.border = 'none';
                textarea.style.outline = 'none';
                textarea.style.boxShadow = 'none';

                // Avoid flash of white box if rendered for any reason.
                textarea.style.background = 'transparent';
                document.querySelector("body").appendChild(textarea);
                existsTextarea = document.getElementById(id);
            }

            existsTextarea.value = text;
            existsTextarea.select();

            try {
                var status = document.execCommand('copy');
                if (!status) {
                    showDialog({
                        title: wplg_objects.l18n.error, // todo : use the response message instead of a predefined one
                        text: msg_error,
                        closeicon: false
                    });
                } else {
                    wpSnackbarModule.show({
                        id: 'copied',
                        content: msg_success,
                        auto_close_delay: 1000
                    });
                }
            } catch (err) {
                showDialog({
                    title: wplg_objects.l18n.error, // todo : use the response message instead of a predefined one
                    text: msg_error,
                    closeicon: false
                });
            }
        },

        /**
         * Escape string
         * @param s string
         */
        wpescapeScripts: function (s) {
            return s
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        },

        /**
         * Get thumbnail for local and cloud files
         * @param file
         * @returns {*}
         */
        getThumbnail: function (file) {
            if (typeof file.thumbnail === 'undefined' || file.thumbnail === null || file.thumbnail === '') {
                var icon = 'file_default';
                if (file.type.indexOf("image") >= 0) {
                    return URL.createObjectURL(file);
                }

                return wplg_objects.vars.plugin_url_image + icon + '.png';
            } else {
                return file.thumbnail;
            }
        },

        /**
         * Helper functions
         * @param bytes
         * @param si
         * @returns {string}
         */
        humanFileSize: function (bytes, si) {
            var thresh = si ? 1000 : 1024;
            if (Math.abs(bytes) < thresh) {
                return bytes + ' B';
            }
            var units = si
                ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
                : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
            var u = -1;
            do {
                bytes /= thresh;
                ++u;
            } while (Math.abs(bytes) >= thresh && u < units.length - 1);
            return bytes.toFixed(1) + ' ' + units[u];
        },

        /**
         * Render file in upload list
         * @param file
         */
        renderFileUploadRow: function (file) {
            var row = ($('.wplg-file-lists').find('.template-row').clone().removeClass('template-row'));

            row.find('.file-name').text(file.name);
            if (file.size !== 'undefined' && file.size > 0) {
                row.find('.file-size').text(wpGalleryModule.humanFileSize(file.size, true));
            }
            row.find('.upload-thumbnail img').attr('src', wpGalleryModule.getThumbnail(file));

            row.addClass('template-upload');
            $(".wplg-file-lists").append(row);
            return row;
        },

        /**
         * Upload function
         */
        uploadImages: function () {
            /* Upload image */
            $('.wplg_upload_img_button').unbind('click').bind('click', function () {
                $('#wplg_form_upload').submit();
            });

            $('.wplg_gallery_file').unbind('change').bind('change', function () {
                var numFiles = $(this)[0].files.length;
                $('.wplg-select-files-btn .count_files').html('(' + numFiles + ')');
                $('.wplg-file-lists .template-upload').remove();
                $.each($(this)[0].files, function (index, file) {
                    wpGalleryModule.renderFileUploadRow(file);
                });
            });

            $('.wplg-select-files-btn').unbind('click').bind('click', function () {
                $('.wplg_gallery_file').click();
            });

            var wplg_bar = jQuery('.wplg-upload-progress .ui-progressbar-value');
            jQuery('#wplg_form_upload').ajaxForm({
                beforeSend: function () {
                    wplg_bar.width(0);
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    jQuery('.wplg-upload-process-wrap').show();
                    var wplg_percentValue = percentComplete + '%';
                    wplg_bar.width(wplg_percentValue);
                },
                success: function () {
                    wplg_bar.width('100%');
                },
                complete: function (xhr) {
                    jQuery('.wplg-process-bar-full').hide();
                    var ob = JSON.parse(xhr.responseText);
                    if (typeof xhr.responseText !== "undefined") {
                        if (ob.status) {
                            $('.wplg_gallery_file').val('');
                            $.magnificPopup.close();
                            wpGalleryModule.changeGallery(wpGalleryModule.wp_current_gallery);
                        }
                    }
                }
            });
        },

        /**
         * action edit and remove image
         */
        eventImages: function () {
            /* Select images */
            var singleIndex;
            $('.wplg-images-wrap .gallery-image').unbind('click').bind('click', function (e) {
                var $this = $(this);
                if (!$(e.target).hasClass('material-icons') && !$(e.target).hasClass('material-icons-outlined')) {
                    var nodes = Array.prototype.slice.call(document.getElementById('wplg-images-wrap').children);
                    if (!$('.gallery-image.selected').length) {
                        singleIndex = nodes.indexOf(this);
                    }

                    // select multiple image use ctrl key or shift key
                    if (e.ctrlKey || e.shiftKey) {
                        if (!$('.gallery-image.selected').length) {
                            $this.addClass('selected');
                        } else {
                            var modelIndex = nodes.indexOf(this), i;
                            if (singleIndex < modelIndex) {
                                for (i = singleIndex; i <= (modelIndex + 1); i++) {
                                    $('.gallery-image:nth-child(' + i + ')').addClass('selected');
                                }
                            } else {
                                for (i = modelIndex; i <= (singleIndex + 1); i++) {
                                    $('.gallery-image:nth-child(' + (i + 1) + ')').addClass('selected');
                                }
                            }
                        }
                    } else {
                        if ($this.hasClass('selected')) {
                            $this.removeClass('selected');
                        } else {
                            $this.addClass('selected');
                        }
                    }

                    if ($('.gallery-image.selected').length) {
                        $('.wplg-remove-item-btn').show();
                    } else {
                        $('.wplg-remove-item-btn').hide();
                    }
                }
            });

            $('.edit_image_selection').unbind('click').bind('click', function () {
                var id = $(this).closest('.gallery-image').data('id');
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "item_details",
                        id: id,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        wpSnackbarModule.show({
                            id: 'loading_item_details',
                            content : wplg_objects.l18n.loading_item_details,
                            auto_close: false
                        });
                    },
                    success: function (res) {
                        if (res.status) {
                            wpSnackbarModule.close('loading_item_details');
                            showDialog({
                                text: res.html,
                                negative: {
                                    title: wplg_objects.l18n.cancel,
                                    id: 'wp-dl-cancel-edit-image'
                                },
                                positive: {
                                    title: wplg_objects.l18n.save,
                                    id: 'dl_save_image',
                                    onClick: function () {
                                        var title = wpGalleryModule.wpescapeScripts($('.form_image_details_popup .img_title').val());
                                        var excerpt = wpGalleryModule.wpescapeScripts($('.form_image_details_popup .img_excerpt').val());
                                        var link_to = wpGalleryModule.wpescapeScripts($('.form_image_details_popup .wplg_image_custom_link').val());
                                        var status;
                                        if ($('.wplg_image_status').is(':checked')) {
                                            status = 1;
                                        } else {
                                            status = 0;
                                        }
                                        /* Run ajax update image */
                                        $.ajax({
                                            url: ajaxurl,
                                            method: "POST",
                                            dataType: 'json',
                                            data: {
                                                action: "wpgallery",
                                                task: "update_item",
                                                id: id,
                                                title: title,
                                                excerpt: excerpt,
                                                link_to: link_to,
                                                status: status,
                                                wplg_nonce: wplg_objects.vars.wplg_nonce
                                            },
                                            success: function (res) {
                                                if (res.status) {
                                                    /* display notification */
                                                    wpSnackbarModule.show({
                                                        id: 'update_item',
                                                        content: wplg_objects.l18n.save_img,
                                                        icon: '<span class="material-icons-outlined wplg-snack-icon">check</span>',
                                                        auto_close_delay: 2000
                                                    });
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
            });
        },

        /**
         * render lists galleries tree
         * @param res
         * @param open_id
         * @param type
         */
        renderListstree: function (res, open_id, type) {
            wpGalleryTreeModule.categories = res.categories;
            wpGalleryTreeModule.categories_order = res.categories_order;
            wpGalleryTreeModule.importCategories();
            wpGalleryTreeModule.getTreeElement().html(wpGalleryTreeModule.getRendering());
            wpGalleryTreeModule.initDragDrop();
            if (type) {
                open_id = $('#gallerylist').find('.gallery_tree ul li:nth-child(2)').data('id');
            }
            wpGalleryTreeModule.titleopengallery(open_id, true);
        },

        loadGoogleDrive: function (folderID = 'root', loading = '') {
            $.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wpgallery",
                    task: "loadGoogleDrive",
                    folderID: folderID,
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                beforeSend: function() {
                    if (!$('.google_drive_results_loading').length) {
                        $('.google_drive_results').html(loading);
                    }
                },
                success: function (res) {
                    if (res.status) {
                        $('.google_drive_results').html(res.html);

                        $('.wplg_google_drive_file_item').unbind('click').bind('click', function () {
                            if ($(this).hasClass('selected')) {
                                $(this).removeClass('selected');
                            } else {
                                $(this).addClass('selected');
                            }
                        });

                        $('.wplg_add_google_drive_file').unbind('click').bind('click', function () {
                            if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                                return;
                            }

                            var $this = $(this);
                            var google_file_ids = [];
                            $('.wplg_google_drive_file_item.selected').each(function () {
                                var google_file_id = $(this).data('id');
                                if (google_file_id !== '') {
                                    google_file_ids.push(google_file_id);
                                }
                            });

                            if (!google_file_ids.length) {
                                return;
                            }

                            $.ajax({
                                url: ajaxurl,
                                method: "POST",
                                dataType: 'json',
                                data: {
                                    action: "wpgallery",
                                    task: "add_google_drive_file_to_gallery",
                                    gallery_id: wpGalleryModule.wp_current_gallery,
                                    google_file_ids: google_file_ids.join(),
                                    wplg_nonce: wplg_objects.vars.wplg_nonce
                                },
                                beforeSend: function () {
                                    $this.closest('.wplg_dl_buttons').find('.spinner').css('visibility', 'visible').show();
                                },
                                success: function (res) {
                                    if (res.status) {
                                        $this.closest('.wplg_dl_buttons').find('.spinner').hide();
                                        $.magnificPopup.close();
                                        wpGalleryModule.changeGallery(wpGalleryModule.wp_current_gallery);
                                    } else {
                                        showDialog({
                                            text: res.msg,
                                            closeicon: false
                                        });
                                    }
                                }
                            });
                        });

                        $('.wplg_google_drive_folder_item').unbind('click').bind('click', function () {
                            var id = $(this).data('id');
                            wpGalleryModule.loadGoogleDrive(id, loading)
                        });
                    }
                }
            });
        },

        /* action for gallery */
        galleryEvent: function () {
            // add images from folder
            if ($().magnificPopup) {
                $('.wplg_add_youtube, .wplg_add_vimeo').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true,
                    callbacks: {
                        close: function(e) {
                            $('.wplg-video-url').val('');
                        }
                    }
                });

                $('.add_image_from_folder').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true,
                    callbacks: {
                        close: function(e) {
                            $('.server_folder_checkbox').prop('checked', false);
                        }
                    }
                });

                $('.wplg_add_media_category').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true,
                    callbacks: {
                        open: function() {
                            $('.wplg-select-media-taxonomy').unbind('click').bind('click', function () {
                                var $this = $(this);
                                var taxnomy = $('.wplg-select-media-taxonomy').val();
                                $.ajax({
                                    type: "POST",
                                    url: ajaxurl,
                                    data: {
                                        action: "wpgallery",
                                        task: "load_media_categories_list",
                                        taxonomy: taxnomy,
                                        wplg_nonce: wplg_objects.vars.wplg_nonce
                                    },
                                    beforeSend: function () {
                                        $this.find('.spinner').css('visibility', 'visible').show();
                                        $('.wplg_media_categories_list').remove();
                                    },
                                    success: function (res) {
                                        if (res.status) {
                                            $this.find('.spinner').hide();
                                            $('.wplg-select-media-taxonomy').after(res.dropdown);
                                        }
                                    }
                                });
                            });

                            $('.wplg_add_media_category_btn').unbind('click').bind('click', function () {
                                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                                    return;
                                }

                                var $this = $(this);
                                var taxnomy = $('.wplg-select-media-taxonomy').val();
                                var category_id = $('.wplg_media_categories_list').val();
                                if (parseInt(category_id) === 0) {
                                    return;
                                }
                                $.ajax({
                                    type: "POST",
                                    url: ajaxurl,
                                    data: {
                                        action: "wpgallery",
                                        task: "add_media_categories",
                                        taxonomy: taxnomy,
                                        category_id: category_id,
                                        gallery_id: wpGalleryModule.wp_current_gallery,
                                        wplg_nonce: wplg_objects.vars.wplg_nonce
                                    },
                                    beforeSend: function () {
                                        $this.find('.spinner').css('visibility', 'visible').show();
                                    },
                                    success: function (res) {
                                        $this.closest('.wplg_dl_buttons').find('.spinner').hide();
                                        $.magnificPopup.close();
                                        if (res.status) {
                                            wpGalleryModule.changeGallery(wpGalleryModule.wp_current_gallery);
                                        }
                                    }
                                });
                            });
                        },
                        close: function(e) {

                        }
                    }
                });

                $('.server_folder_preview').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true
                });

                $('.upload_image_from_pc').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true,
                    callbacks: {
                        close: function(e) {
                            $('.template-upload').remove();
                            $('.count_files').html('');
                            $('.wplg-upload-process-wrap').hide();
                            $('.ui-progressbar-value').width(0);
                        }
                    }
                });

                $('.wplg_add_google_drive').magnificPopup({
                    type: 'inline',
                    closeBtnInside: false,
                    midClick: true,
                    callbacks: {
                        open: function(e) {
                            if (!$('.google_drive_results .wplg_folder_output').length && !$('.google_drive_results .wplg_file_output').length) {
                                var loading = $('.google_drive_results_loading').clone();
                                wpGalleryModule.loadGoogleDrive('root', loading);
                            }
                        }
                    }
                });
            }

            // add images from media library
            $('.add_image_from_media_library').unbind('click').bind('click', function () {
                if (typeof frame !== "undefined") {
                    frame.open();
                    return;
                }
                // Create the media frame.
                var frame = wp.media({
                    library: {
                        type: 'image'
                    },
                    title: wplg_objects.l18n.iframe_import_label,
                    button: {
                        text: wplg_objects.l18n.import
                    },
                    multiple: 'add'
                });

                // When an image is selected, run a callback.
                frame.on('select', function () {
                    // Grab the selected attachment.
                    var attachments = frame.state().get('selection').toJSON();
                    var files = [];
                    $.each(attachments, function (i, attachment) {
                        if (!$('.gallery-image[data-id="'+ attachment.id +'"]').length) {
                            var url = (typeof attachment.sizes.medium !== "undefined") ? attachment.sizes.medium.url : attachment.url;
                            var item_html = '<div data-id="'+ attachment.id +'" class="gallery-image">\n' +
                                '    <div class="wplg-image-preview">\n' +
                                '        <div class="square_thumbnail">\n' +
                                '            <div class="img_centered">\n' +
                                '                <img src="'+ url +'">\n' +
                                '            </div>\n' +
                                '        </div>\n' +
                                '\n' +
                                '        <div class="wplg-img-hover">\n' +
                                '            <div class="action_images">\n' +
                                '                <a class="edit_image_selection">\n' +
                                '                    <i class="material-icons-outlined"> info </i>\n' +
                                '                </a>\n' +
                                '            </div>\n' +
                                '        </div>\n' +
                                '                <div class="wplg-item-source">\n' +
                                '            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="24px" height="24px"><path fill="#fff" d="M24 4.050000000000001A19.95 19.95 0 1 0 24 43.95A19.95 19.95 0 1 0 24 4.050000000000001Z"></path><path fill="#01579b" d="M8.001,24c0,6.336,3.68,11.806,9.018,14.4L9.385,17.488C8.498,19.479,8.001,21.676,8.001,24z M34.804,23.194c0-1.977-1.063-3.35-1.67-4.412c-0.813-1.329-1.576-2.437-1.576-3.752c0-1.465,1.471-2.84,3.041-2.84 c0.071,0,0.135,0.006,0.206,0.008C31.961,9.584,28.168,8,24.001,8c-5.389,0-10.153,2.666-13.052,6.749 c0.228,0.074,0.307,0.039,0.611,0.039c1.669,0,4.264-0.2,4.264-0.2c0.86-0.057,0.965,1.212,0.099,1.316c0,0-0.864,0.105-1.828,0.152 l5.931,17.778l3.5-10.501l-2.603-7.248c-0.861-0.046-1.679-0.152-1.679-0.152c-0.862-0.056-0.762-1.375,0.098-1.316 c0,0,2.648,0.2,4.217,0.2c1.675,0,4.264-0.2,4.264-0.2c0.861-0.057,0.965,1.212,0.104,1.316c0,0-0.87,0.105-1.832,0.152l5.891,17.61 l1.599-5.326C34.399,26.289,34.804,24.569,34.804,23.194z M24.281,25.396l-4.8,13.952c1.436,0.426,2.95,0.652,4.52,0.652 c1.861,0,3.649-0.324,5.316-0.907c-0.04-0.071-0.085-0.143-0.118-0.22L24.281,25.396z M38.043,16.318 c0.071,0.51,0.108,1.059,0.108,1.645c0,1.628-0.306,3.451-1.219,5.737l-4.885,14.135C36.805,35.063,40,29.902,40,24 C40,21.219,39.289,18.604,38.043,16.318z"></path><path fill="#01579b" d="M4,24c0,11.024,8.97,20,19.999,20C35.03,44,44,35.024,44,24S35.03,4,24,4S4,12.976,4,24z M5.995,24 c0-9.924,8.074-17.999,18.004-17.999S42.005,14.076,42.005,24S33.929,42.001,24,42.001C14.072,42.001,5.995,33.924,5.995,24z"></path></svg>        </div>\n' +
                                '    </div>\n' +
                                '</div>';
                            files.push(attachment.id);
                            if ($('.gallery-image').length) {
                                $('.wplg-images-wrap').prepend(item_html);
                            } else {
                                $('.wplg-images-wrap').html(item_html);
                            }
                        }
                    });

                    wpGalleryModule.eventImages();
                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        dataType: 'json',
                        data: {
                            action: "wpgallery",
                            task: "import_media_library",
                            files: files.join(),
                            gallery_id: wpGalleryModule.wp_current_gallery,
                            wplg_nonce: wplg_objects.vars.wplg_nonce
                        }
                    });
                });

                // let's open up the frame.
                frame.open();
            });

            $('.wplg-load-default-options').unbind('click').bind('click', function () {
                var $this = $(this);
                var theme = $('.edit-gallery-theme').val();
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'wpgallery',
                        task: 'load_default_options',
                        theme: theme,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        $this.find('.spinner').css('visibility', 'visible').show();
                    },
                    success: function (res) {
                        if (res.status) {
                            $this.find('.spinner').hide();
                            wpGalleryModule.loadOptions(res.options);
                        }
                    }
                });
            });
        },

        /**
         * search key by value
         * @param arr
         * @param val
         * @returns {*}
         */
        arraySearch: function (arr, val) {
            for (var i = 0; i < arr.length; i++)
                if (arr[i] === val)
                    return i;
            return false;
        },

        serverFolderAction: function () {
            $('.wplg_server_folder_item').unbind('click').bind('click', function (e) {
                if ($(e.target).hasClass('wplg-delete-gallery-folder')) {
                    return;
                }
                var path = $(this).data('path');
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "load_server_folder_preview",
                        path: path,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        if (!$('.server_folder_preview_loader').length) {
                            $('.server_folder_preview_wrap').html('<img class="server_folder_preview_loader" src="' + wplg_objects.vars.plugin_url_image + 'loader_skype_trans.gif">');
                        }
                        $('.server_folder_preview').click();
                    },
                    success: function (res) {
                        if (res.status) {
                            $('.server_folder_preview_wrap').html(res.html);
                        }
                    }
                });
            });

            $('.wplg-delete-gallery-folder').unbind('click').bind('click', function () {
                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                    return;
                }
                var $this = $(this);
                var folder_path = $this.data('path');
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "server_folder_remove",
                        gallery_id: wpGalleryModule.wp_current_gallery,
                        folder_path: folder_path,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {

                    },
                    success: function (res) {
                        if (res.status) {
                            $this.closest('.wplg_server_folder_item').remove();
                        }
                    }
                });
            });

            $('.wplg_server_folder_button_add').unbind('click').bind('click', function () {
                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                    return;
                }

                var $this = $(this);
                var folders_path = [];
                $('.server_folder_checkbox:checked').each(function () {
                    var folder_path = $(this).data('file');
                    if (folder_path !== '') {
                        folders_path.push(folder_path);
                    }
                });

                if (!folders_path.length) {
                    return;
                }

                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "server_folder_add",
                        gallery_id: wpGalleryModule.wp_current_gallery,
                        folders_path: folders_path.join(),
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        $this.closest('.wplg_dl_buttons').find('.spinner').css('visibility', 'visible').show();
                    },
                    success: function (res) {
                        if (res.status) {
                            $this.closest('.wplg_dl_buttons').find('.spinner').hide();
                            $.magnificPopup.close();
                            $('.server_folders_gallery_output').html(res.folders_html);
                            wpGalleryModule.serverFolderAction();
                        }
                    }
                });
            });

            $('.wplg_video_button_add').unbind('click').bind('click', function () {
                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                    return;
                }

                var $this = $(this);
                var url = $('.wplg-video-url').val();
                var type = $(this).data('type');
                if (url === '') {
                    $('.wplg-video-url').focus();
                    return;
                }

                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "add_video",
                        gallery_id: wpGalleryModule.wp_current_gallery,
                        url: url,
                        type: type,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        $this.closest('.wplg_dl_buttons').find('.spinner').css('visibility', 'visible').show();
                    },
                    success: function (res) {
                        if (res.status) {
                            $this.closest('.wplg_dl_buttons').find('.spinner').hide();
                            $('.wplg-video-url').val('');
                            $.magnificPopup.close();
                            wpGalleryModule.changeGallery(wpGalleryModule.wp_current_gallery);
                        } else {
                            showDialog({
                                text: res.msg,
                                closeicon: false
                            });
                        }
                    }
                });
            });

            $('.wplg_dl_cancel').unbind('click').bind('click', function () {
                $.magnificPopup.close();
            });
        },

        /**
         * all event
         */
        bindEvent: function () {
            wpGalleryModule.serverFolderAction();
            $('.wplp-theme-demo').magnificPopup({
                type: 'image',
                showCloseBtn: false
            });

            $('.wplg-form-edit input, .wplg-form-edit select').unbind('change').bind('change', function () {
                wpGalleryModule.renderShortCode(wpGalleryModule.wp_current_gallery);
            });

            $('.wplg-clear-color').unbind('click').bind('click', function () {
                var field = $(this).data('field');
                $('.' + field + ' .wplg-color_check').remove();
                $('.wplg-input-color[data-type="'+ field +'"]').val('transparent');
            });

            $('.wplg-color').unbind('click').bind('click', function () {
                var color = $(this).data('color');
                $(this).closest('.wplg-color-main').find('.wplg-input-color').val(color).change();
                $(this).closest('.wplg-color-main').find('.wplg-color_check').remove();
                $(this).closest('.wplg-color-main').find('.wplg-color[data-color="' + color + '"]').append('<i class="material-icons wplg-color_check">done</i>');
            });

            $('.wplg-panel-theme').unbind('click').bind('click', function () {
                var theme = $(this).data('theme');
                $('.edit-gallery-theme').val(theme).change();
                $('.wplg-panel-theme').removeClass('theme-selected');
                $(this).addClass('theme-selected');
            });

            /* Create gallery */
            $('.btn_create_gallery').unbind('click').bind('click', function () {
                var $this = $(this);
                var title = $('.new-gallery-name').val();

                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "create_gallery",
                        parent: wpGalleryModule.wp_current_gallery,
                        title: title,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        wpSnackbarModule.show({
                            id: 'creating_gallery',
                            content : wplg_objects.l18n.creating_gallery,
                            icon: '<span class="material-icons-outlined wplg-snack-icon"> add </span>',
                            auto_close: false
                        });
                    },
                    success: function (res) {
                        if (res.status) {
                            $.magnificPopup.close();
                            /* display notification */
                            wpSnackbarModule.close('creating_gallery');
                            wpSnackbarModule.show({
                                id: 'create_gallery',
                                content: wplg_objects.l18n.add_gallery,
                                icon: '<span class="material-icons-outlined wplg-snack-icon"> add </span>',
                                auto_close_delay: 2000
                            });

                            // Update the categories variables
                            wpGalleryModule.renderListstree(res, res.items.term_id, false);
                            $('.wplg_tree_item[data-id="'+ res.items.term_id +'"]').parents('li').removeClass('closed');
                        }
                    }
                });
            });

            /* Delete selected images gallery */
            $('.wplg-remove-item-btn, .wplg-delete-category-item').unbind('click').bind('click', function () {
                var $this = $(this);
                var ids = [];
                if ($this.hasClass('wplg-delete-category-item')) {
                    ids.push($(this).data('id'));
                } else {
                    $('.wplg-images-wrap .gallery-image.selected').each(function (i, v) {
                        var id = $(v).data('id');
                        ids.push(id);
                    });
                }

                showDialog({
                    title: wplg_objects.l18n.delete_selected_image,
                    negative: {
                        title: wplg_objects.l18n.cancel,
                        id: 'dl_cancel_items_btn',
                    },
                    positive: {
                        title: wplg_objects.l18n.delete,
                        id: 'dl_delete_items_btn',
                        onClick: function () {
                            $.ajax({
                                url: ajaxurl,
                                method: "POST",
                                dataType: 'json',
                                data: {
                                    action: "wpgallery",
                                    task: "delete_imgs",
                                    ids: ids.join(),
                                    id_gallery: wpGalleryModule.wp_current_gallery,
                                    wplg_nonce: wplg_objects.vars.wplg_nonce
                                },
                                success: function (res) {
                                    if (res.status) {
                                        if ($this.hasClass('wplg-delete-category-item')) {
                                            $this.closest('.wplg_category_output_item').remove();
                                        } else {
                                            $.each(ids, function (i, id) {
                                                $('.gallery-image[data-id="' + id + '"]').remove();
                                            });

                                            /* display notification */
                                            wpSnackbarModule.show({
                                                id: 'delete_items',
                                                content: wplg_objects.l18n.delete_img,
                                                icon: '<span class="material-icons wplg-snack-icon">delete</span>',
                                                auto_close_delay: 2000
                                            });
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            });

            /* Create gallery */
            $('.wplg_save').unbind('click').bind('click', function () {
                var title = $('.edit-gallery-name').val();
                var theme = $('.edit-gallery-theme').val();
                /* Ajax edit gallery */
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wplg_edit_gallery",
                        id: wpGalleryModule.wp_current_gallery,
                        title: title,
                        theme: theme,
                        options: $('.wplg-form-edit').serialize(),
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        wpSnackbarModule.show({
                            id: 'wp-gallery-saving',
                            content: wplg_objects.l18n.gallery_saving,
                            is_progress: true,
                            auto_close: false
                        });
                    },
                    success: function (res) {
                        if (res.status) {
                            wpSnackbarModule.close('wp-gallery-saving');
                            wpSnackbarModule.show({
                                id: 'wp-gallery-saved',
                                content: wplg_objects.l18n.save_glr,
                                icon: '<span class="material-icons-outlined wplg-snack-icon">check</span>',
                                auto_close_delay: 1000
                            });
                            $('.wplg_tree_title_folder[data-id="' + wpGalleryModule.wp_current_gallery + '"] span').html(title);
                        }
                    }
                });

            });
        },
    };

    $(document).ready(function () {
        wpGalleryModule.init();
    });
})(jQuery);
String.prototype.hashCode = function () {
    var hash = 0, i, char;
    if (this.length === 0)
        return hash;
    for (i = 0, l = this.length; i < l; i++) {
        char = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash |= 0; // Convert to 32bit integer
    }
    return Math.abs(hash);
};