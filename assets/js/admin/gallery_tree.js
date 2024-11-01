/**
 * Folder tree for WP Media Folder
 */
var wpGalleryTreeModule;
(function ($) {
    /**
     * Main folder tree function
     */
    wpGalleryTreeModule = {
        options: {
            'root': '/',
            'showroot': '',
            'onclick': function (elem, type, file) {
            },
            'oncheck': function (elem, checked, type, file) {
            },
            'usecheckboxes': true, //can be true files dirs or false
            'expandSpeed': 500,
            'collapseSpeed': 500,
            'expandEasing': null,
            'collapseEasing': null,
            'canselect': true
        },
        categories : [], // categories
        /**
         * Folder tree init
         */
        init: function () {
            wpGalleryTreeModule.categories_order = wplg_objects.vars.categories_order;
            wpGalleryTreeModule.categories = wplg_objects.vars.categories;

            wpGalleryTreeModule.importCategories();
            $gallerylist = $('.gallerylist');
            if ($gallerylist.length === 0) {
                return;
            }

            wpGalleryTreeModule.getTreeElement().html(wpGalleryTreeModule.getRendering());
            wpGalleryTreeModule.initDragDrop();

            var first_id = $('#gallerylist').find('.gallery_tree ul li:nth-child(2)').data('id');
            if (first_id !== 0) {
                $('.wplg_tree_item[data-id="'+ first_id +'"]').parents('li').removeClass('closed');
                wpGalleryTreeModule.titleopengallery(first_id);
            }

            wpGalleryModule.galleryEvent();
        },

        initDragDrop: function () {
            if ($().draggable) {
                // Initialize dragping folder on tree view
                $('.wplg_tree_item[data-id!="0"]').draggable({
                    revert: true,
                    helper: function (ui) {
                        var title = $(ui.currentTarget).find('> .wplg_row_tree .title-text').text();
                        return '<div class="wplg-dragging-element">'+ title +'</div>';
                    },
                    appendTo: '.gallery_tree',
                    cursorAt: {top: 0, left: 0},
                    delay: 100, // Prevent dragging when only trying to click
                    drag: function () {
                    },
                    start: function (event, ui) {
                        // Add the original size of element
                        $(ui.helper).css('width', $(ui.helper.context).outerWidth() + 'px');
                        $(ui.helper).css('height', $(ui.helper.context).outerWidth() + 'px');

                        // Add some style to original elements
                        $(this).addClass('wplg-dragging');
                    },
                    stop: function (event, ui) {
                        // Revert style
                        $(this).removeClass('wplg-dragging');
                    }
                });
            }

            if ($().droppable) {
                // Initialize dropping folder on tree view
                $('.wplg_row_tree').droppable({
                    hoverClass: "wplg-hover-folder",
                    tolerance: 'pointer',
                    drop: function (event, ui) {
                        event.stopPropagation();
                        // move folder with folder tree
                        wpGalleryTreeModule.moveGallery($(ui.draggable).data('id'), $(this).data('id'));
                    }
                });
            }
        },

        moveGallery: function (gallery_id, target_gallery_id) {
            return $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "wpgallery",
                    task: "move_gallery",
                    gallery_id: gallery_id,
                    target_gallery_id: target_gallery_id,
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                beforeSend: function () {
                    // Show snackbar
                    if (!$('.wp-snackbar[data-id="moving_gallery"]').length) {
                        wpSnackbarModule.show({
                            id: 'moving_gallery',
                            content: wplg_objects.l18n.gallery_moving,
                            auto_close: false,
                            is_progress: true
                        });
                    }
                },
                success: function (response) {
                    if (response.status) {
                        // Update the categories variables
                        wpSnackbarModule.close('moving_gallery');
                        wpGalleryTreeModule.categories = response.categories;
                        wpGalleryTreeModule.categories_order = response.categories_order;
                        wpGalleryTreeModule.importCategories();
                        wpGalleryTreeModule.getTreeElement().html(wpGalleryTreeModule.getRendering());
                        $('.wplg_tree_item[data-id="'+ wpGalleryModule.wp_current_gallery +'"]').addClass('selected');
                        wpGalleryTreeModule.initDragDrop();
                    } else {
                        wpSnackbarModule.close('moving_gallery');
                        if (typeof response.msg !== "undefined") { //todo: change wrong variable name to something more understandable like message or error_message, and what should we do if wrong is set?
                            showDialog({
                                title: wplg_objects.l18n.error,
                                text: response.msg
                            });
                        }
                    }
                }
            });
        },

        /**
         * import gallery category
         */
        importCategories: function () {
            var galleries_ordered = [];

            // Add each category
            $(wpGalleryTreeModule.categories_order).each(function (i, v) {
                galleries_ordered.push(wpGalleryTreeModule.categories[this]);
            });
            galleries_ordered = galleries_ordered.sort(function(a, b){return a.order - b.order});
            // Reorder array based on children
            var galleries_ordered_deep = [];
            var processed_ids = [];
            const loadChildren = function (id) {
                if (processed_ids.indexOf(id) < 0) {
                    processed_ids.push(id);
                    for (var ij = 0; ij < galleries_ordered.length; ij++) {
                        if (galleries_ordered[ij].parent_id === id) {
                            galleries_ordered_deep.push(galleries_ordered[ij]);
                            loadChildren(galleries_ordered[ij].id);
                        }
                    }
                }
            };
            loadChildren(0);

            // Finally save it to the global var
            wpGalleryTreeModule.categories = galleries_ordered_deep;
            if (wpGalleryTreeModule.categories.length <= 1) {
                $('.edit_wrap').addClass('wp-settings-disable-top').removeClass('wp-settings-enable');
                $('.wplg-images-wrap').html('<img class="wplg-banner" src="'+ wplg_objects.vars.plugin_url_image + 'banner.jpg' +'"/>')
            }
        },

        /**
         * Get the html resulting tree view
         * @return {string}
         */
        getRendering: function () {
            var ij = 0;
            var content = ''; // Final tree view content
            /**
             * Recursively print list of folders
             * @return {boolean}
             */
            const generateList = function () {
                content += '<ul>';
                var lists = wpGalleryTreeModule.categories;
                while (ij < lists.length) {
                    var className = 'closed';
                    // Open li tag
                    var pad = lists[ij].depth * 20;
                    content += '<li class="' + className + ' wplg_tree_item" data-id="' + lists[ij].id + '" data-parent_id="' + lists[ij].parent_id + '" >';
                    content += '<div class="wplg_row_tree" data-id="' + lists[ij].id + '" style="padding-left: '+ pad +'px">';
                    if (parseInt(lists[ij].id) === 0) {
                        content += '<a class="wplg_tree_title_folder top_level" onclick="wpGalleryTreeModule.titleopengallery(0)" data-id="' + lists[ij].id + '"><i class="material-icons-outlined">aspect_ratio</i>';
                    } else {
                        const a_tag = '<a class="wplg_tree_title_folder" onclick="wpGalleryTreeModule.titleopengallery(' + lists[ij].id + ')" data-id="' + lists[ij].id + '">';

                        if (lists[ij + 1] && lists[ij + 1].depth > lists[ij].depth) { // The next element is a sub folder
                            content += '<a class="icon_toggle" onclick="wpGalleryTreeModule.toggle(' + lists[ij].id + ')"><i class="material-icons wp-arrow">keyboard_arrow_down</i></a>';
                            content += a_tag;
                            content += '<i class="material-icons">aspect_ratio</i>';
                        } else {
                            content += a_tag;
                            content += '<i class="material-icons wp-no-arrow">aspect_ratio</i>';
                        }
                    }

                    // Add current category name
                    content += '<span class="title-text" data-id="' + lists[ij].id + '" data-parent_id="' + lists[ij].parent_id + '">' + lists[ij].label + '</span>';
                    content += '</a>';
                    if (parseInt(lists[ij].id) !== 0) {
                        content += '<i class="material-icons wpicon-delete-gallery"  onclick="wpGalleryTreeModule.deleteGallery(' + lists[ij].id + ')" data-id="' + lists[ij].id + '" data-parent_id="' + lists[ij].parent_id + '" "> delete_outline </i>';
                    }
                    content += '</div>';

                    // This is the end of the array
                    if (lists[ij + 1] === undefined) {
                        // Let's close all opened tags
                        for (var ik = lists[ij].depth; ik >= 0; ik--) {
                            content += '</li>';
                            content += '</ul>';
                        }

                        // We are at the end don't continue to process array
                        return false;
                    }


                    if (lists[ij + 1].depth > lists[ij].depth) { // The next element is a sub folder
                        // Recursively list it
                        ij++;
                        if (generateList() === false) {
                            // We have reached the end, let's recursively end
                            return false;
                        }
                    } else if (lists[ij + 1].depth < lists[ij].depth) { // The next element don't have the same parent
                        // Let's close opened tags
                        for (var ik = lists[ij].depth; ik > lists[ij + 1].depth; ik--) {
                            content += '</li>';
                            content += '</ul>';
                        }

                        // We're not at the end of the array let's continue processing it
                        return true;
                    }

                    // Close the current element
                    content += '</li>';
                    ij++;
                }
            };

            // Start generation
            generateList();
            return content;
        },

        /**
         * Toggle the open / closed state of a gallery
         * @param gallery_id
         */
        toggle : function(gallery_id) {
            // Check is gallery has closed class
            if (wpGalleryTreeModule.getTreeElement().find('li[data-id="' + gallery_id + '"]').hasClass('closed')) {
                // Open the gallery
                wpGalleryTreeModule.opengallery(gallery_id);
            } else {
                // Close the gallery
                wpGalleryTreeModule.glrclosedir(gallery_id);
                // close all sub gallery
                $('li[data-id="' + gallery_id + '"]').find('li').addClass('closed');
            }
        },

        /**
         * open gallery tree by dir name
         * @param gallery_id
         */
        opengallery : function(gallery_id) {
            wpGalleryTreeModule.getTreeElement().find('li[data-id="' + gallery_id + '"]').removeClass('closed');
        },

        /**
         * open gallery tree by dir name
         */
        titleopengallery : function(gallery_id, reload = false) {
            if ((wpGalleryModule.wp_current_gallery === gallery_id && !reload)) {
                return;
            }
            wpGalleryModule.wp_current_gallery = gallery_id;
            wpGalleryTreeModule.getTreeElement().find('li').removeClass('selected');
            wpGalleryTreeModule.getTreeElement().find('li[data-id="' + gallery_id + '"]').addClass('selected');
            if (parseInt(gallery_id) !== 0) {
                wpGalleryModule.changeGallery(gallery_id);
                $('.select_gallery_id').val(gallery_id);
            } else {
                $('.edit_wrap').addClass('wp-settings-disable-top').removeClass('wp-settings-enable');
                $('.server_folders_gallery_output').html('');
                $('.wplg-images-wrap').html('<img class="wplg-banner" src="'+ wplg_objects.vars.plugin_url_image + 'banner.jpg' +'"/>')
            }
        },

        /**
         * Close a gallery and hide children
         * @param gallery_id
         */
        glrclosedir : function(gallery_id) {
            wpGalleryTreeModule.getTreeElement().find('li[data-id="' + gallery_id + '"]').addClass('closed');
        },

        /**
         * Retrieve the Jquery tree view element
         * of the current frame
         * @return jQuery
         */
        getTreeElement : function() {
            return $('.gallery_tree');
        },

        /**
         * init event click to open/close gallery tree
         */
        deleteGallery: function (id) {
            /* Delete gallery */
            showDialog({
                title: wplg_objects.l18n.delete_gallery,
                negative: {
                    title: wplg_objects.l18n.cancel
                },
                positive: {
                    title: wplg_objects.l18n.delete,
                    onClick: function () {
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            data: {
                                action: "wpgallery",
                                task: "delete_gallery",
                                id: id,
                                wplg_nonce: wplg_objects.vars.wplg_nonce
                            },
                            beforeSend: function () {
                                wpSnackbarModule.show({
                                    id: 'removing_gallery',
                                    content : wplg_objects.l18n.gallery_removing,
                                    icon: '<span class="material-icons-outlined wplg-snack-icon"> remove </span>',
                                    auto_close: false
                                });
                            },
                            success: function (res) {
                                /* remove gallery html */
                                if (res.status) {
                                    $('#gallerylist').find('[data-id="' + id + '"]').remove();
                                    $('.wp-gallery-categories option[value="' + id + '"]').remove();
                                    var first_id = $('#gallerylist').find('.gallery_tree ul li:nth-child(2)').data('id');
                                    wpGalleryTreeModule.titleopengallery(first_id);

                                    /* display notification */
                                    wpSnackbarModule.close('removing_gallery');
                                    wpSnackbarModule.show({
                                        id: 'delete_gallery',
                                        content : wplg_objects.l18n.delete_glr,
                                        icon: '<span class="material-icons-outlined wplg-snack-icon"> remove </span>',
                                        auto_close_delay: 2000
                                    });
                                }
                            }
                        });
                    }
                }
            });
        },
    };

    $(document).ready(function () {
        wpGalleryTreeModule.init();
    });
})(jQuery);