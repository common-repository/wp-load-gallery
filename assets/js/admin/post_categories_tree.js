/**
 * Folder tree for WP Media Folder
 */
var wplgPostCategoriesTreeModule;
(function ($) {
    /**
     * Main folder tree function
     * @type {{options: {root: string, showroot: string, onclick: onclick, oncheck: oncheck, usecheckboxes: boolean, expandSpeed: number, collapseSpeed: number, expandEasing: null, collapseEasing: null, canselect: boolean}, init: init, closetree: closetree, glrsetevents: glrsetevents}}
     */
    wplgPostCategoriesTreeModule = {
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
        wp_current_category_id: 0,
        select_post_type: 'post',
        select_taxonomy: 'category',
        paged: 1,
        /**
         * Folder tree init
         */
        init: function () {
            $('.wplg_add_posts').magnificPopup({
                type: 'inline',
                closeBtnInside: false,
                midClick: true,
                callbacks: {
                    open: function(e) {
                        if (!$('.wplg-categories-tree ul').length && !$('.wplg_post_item').length) {
                            // render taxonomy list on first load
                            var option_taxonomies = '';
                            $.each(wplg_objects.vars.all_taxonomies.post.taxonomies, function () {
                                option_taxonomies += '<option value="'+ this.name +'">'+ this.title +'</option>';
                            });
                            $('.wplg-select-taxonomy').html(option_taxonomies).show();
                            wplgPostCategoriesTreeModule.loadCategoriesByPostType(wplgPostCategoriesTreeModule.select_post_type, wplgPostCategoriesTreeModule.select_taxonomy);

                            // render taxonomy list on change
                            $('.wplg-select-post-type').unbind('change').bind('change', function () {
                                if (typeof wplg_objects.vars.all_taxonomies[$(this).val()] !== "undefined") {
                                    var taxonomies = wplg_objects.vars.all_taxonomies[$(this).val()];
                                    var option_taxonomies = '';
                                    $.each(taxonomies.taxonomies, function () {
                                        option_taxonomies += '<option value="'+ this.name +'">'+ this.title +'</option>';
                                    });

                                    $('.wplg_filter_post_type').show();
                                    $('.wplg-select-taxonomy').html(option_taxonomies);
                                    wplgPostCategoriesTreeModule.select_taxonomy = $('.wplg-select-taxonomy').val();
                                    wplgPostCategoriesTreeModule.loadCategoriesByPostType($(this).val(), wplgPostCategoriesTreeModule.select_taxonomy);
                                    wplgPostCategoriesTreeModule.select_post_type = $(this).val();
                                } else {
                                    $('.wplg-select-taxonomy').closest('.wplg_filter_post_type').hide();
                                    wplgPostCategoriesTreeModule.loadCategoriesByPostType($(this).val(), '');
                                }
                                wplgPostCategoriesTreeModule.select_post_type = $(this).val();
                            });

                            $('.wplg-select-taxonomy').unbind('change').bind('change', function () {
                                wplgPostCategoriesTreeModule.loadCategoriesByPostType(wplgPostCategoriesTreeModule.select_post_type, $(this).val());
                                wplgPostCategoriesTreeModule.select_taxonomy = $(this).val();
                            });
                        }
                    }
                }
            });
        },

        loadCategoriesByPostType: function(post_type, taxonomy) {
            $.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wpgallery",
                    task: "load_categories_by_post_type",
                    post_type: post_type,
                    taxonomy: taxonomy,
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                beforeSend: function() {
                    $('.posts_results_overlay').show();
                    if (taxonomy === '') {
                        $('.wplg-posts-item-wrap').addClass('wplg_full_width');
                        $('.wplg-categories-tree').hide();
                    } else {
                        $('.wplg-posts-item-wrap').removeClass('wplg_full_width');
                        $('.wplg-categories-tree').show();
                    }
                    if (!$('.posts_results_loading').length) {
                        //$('.posts_results').html(loading);
                    }
                },
                success: function (res) {
                    $('.posts_results_overlay').hide();
                    if (res.status) {
                        wplgPostCategoriesTreeModule.categories_order = res.categories_order;
                        wplgPostCategoriesTreeModule.categories = res.categories;
                        wplgPostCategoriesTreeModule.importCategories();
                        wplgPostCategoriesTreeModule.getTreeElement().html(wplgPostCategoriesTreeModule.getRendering());
                        wplgPostCategoriesTreeModule.opentree(0);
                    }
                }
            });
        },

        loadPostList: function (category_id, paged = 1) {
            var post_type = $('.wplg-select-post-type').val();
            var taxonomy = $('.wplg-select-taxonomy').val();
            $.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: 'json',
                data: {
                    action: "wpgallery",
                    task: "load_post_list",
                    category_id: category_id,
                    post_type: post_type,
                    taxonomy: taxonomy,
                    paged: paged,
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                beforeSend: function() {
                    $('.posts_results_overlay').show();
                },
                success: function (res) {
                    $('.posts_results_overlay').hide();
                    if (res.status) {
                        if (parseInt(paged) === 1) {
                            $('.wplg_post_output').html(res.postListHtml);
                        } else {
                            $('.wplg_post_output').append(res.postListHtml);
                        }
                        if (parseInt(res.count) === 0) {
                            $('.wplg-pages-wrap').hide();
                        } else {
                            $('.wplg-pages-wrap').show();
                        }
                        wplgPostCategoriesTreeModule.actionEvent();
                    }
                }
            });
        },

        actionEvent: function() {
            $('.wplg_post_item').unbind('click').bind('click', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    $(this).addClass('selected');
                }
            });

            $('.wplg_add_this_category_btn').unbind('click').bind('click', function () {
                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                    return;
                }

                var $this = $(this);
                var post_type = $('.wplg-select-post-type').val();
                var category_id = $('.wplg-categories-tree .wplg_tree_item.selected').data('id');
                if (typeof category_id === "undefined") {
                    return;
                }
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "add_category_to_gallery",
                        gallery_id: wpGalleryModule.wp_current_gallery,
                        category_id: category_id,
                        post_type: post_type,
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        $this.closest('.wplg_dl_buttons').find('.spinner').css('visibility', 'visible').show();
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

            $('.wplg_add_posts_btn').unbind('click').bind('click', function () {
                if (parseInt(wpGalleryModule.wp_current_gallery) === 0) {
                    return;
                }

                var $this = $(this);
                var posts_ids = [];
                $('.wplg_post_item.selected').each(function () {
                    var post_ids = $(this).data('id');
                    if (post_ids !== '') {
                        posts_ids.push(post_ids);
                    }
                });

                if (!posts_ids.length) {
                    return;
                }

                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    dataType: 'json',
                    data: {
                        action: "wpgallery",
                        task: "add_posts_to_gallery",
                        gallery_id: wpGalleryModule.wp_current_gallery,
                        posts_ids: posts_ids.join(),
                        wplg_nonce: wplg_objects.vars.wplg_nonce
                    },
                    beforeSend: function () {
                        $this.closest('.wplg_dl_buttons').find('.spinner').css('visibility', 'visible').show();
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

            $('.wplg-loadmore-posts').unbind('click').bind('click', function () {
                wplgPostCategoriesTreeModule.paged++;
                var category_id = $('.wplg-categories-tree .wplg_tree_item.selected').data('id');
                wplgPostCategoriesTreeModule.loadPostList(category_id, wplgPostCategoriesTreeModule.paged)
            });
        },

        /**
         * import gallery category
         */
        importCategories: function () {
            var galleries_ordered = [];

            // Add each category
            $(wplgPostCategoriesTreeModule.categories_order).each(function (i, v) {
                galleries_ordered.push(wplgPostCategoriesTreeModule.categories[this]);
            });

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
            wplgPostCategoriesTreeModule.categories = galleries_ordered_deep;
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
                var lists = wplgPostCategoriesTreeModule.categories;
                while (ij < lists.length) {
                    var className = 'closed';
                    // Open li tag
                    content += '<li class="' + className + ' wplg_tree_item" data-id="' + lists[ij].id + '" data-parent_id="' + lists[ij].parent_id + '" >';
                    content += '<div class="wplg_row_tree">';
                    if (parseInt(lists[ij].id) === 0) {
                        content += '<a class="wplg_tree_title_folder top_level" onclick="wplgPostCategoriesTreeModule.opentree(0)" data-id="' + lists[ij].id + '"><i class="material-icons-outlined">folder</i>';
                    } else {
                        const a_tag = '<a class="wplg_tree_title_folder" onclick="wplgPostCategoriesTreeModule.opentree(' + lists[ij].id + ')" data-id="' + lists[ij].id + '">';

                        if (lists[ij + 1] && lists[ij + 1].depth > lists[ij].depth) { // The next element is a sub folder
                            content += '<a class="icon_toggle" onclick="wplgPostCategoriesTreeModule.toggle(' + lists[ij].id + ')"><i class="material-icons wp-arrow">keyboard_arrow_down</i></a>';
                            content += a_tag;
                            content += '<i class="material-icons-outlined">folder</i>';
                        } else {
                            content += a_tag;
                            content += '<i class="material-icons-outlined wp-no-arrow">folder</i>';
                        }
                    }

                    // Add current category name
                    content += '<span class="title-text" data-id="' + lists[ij].id + '" data-parent_id="' + lists[ij].parent_id + '">' + lists[ij].label + '</span>';
                    content += '</a>';
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
         * @param cat_id
         */
        toggle : function(cat_id) {
            // Check is gallery has closed class
            if (wplgPostCategoriesTreeModule.getTreeElement().find('li[data-id="' + cat_id + '"]').hasClass('closed')) {
                // Open the gallery
                wplgPostCategoriesTreeModule.getTreeElement().find('li[data-id="' + cat_id + '"]').removeClass('closed');
            } else {
                // Close the gallery
                wplgPostCategoriesTreeModule.closetree(cat_id);
                // close all sub gallery
                $('li[data-id="' + cat_id + '"]').find('li').addClass('closed');
            }
        },

        /**
         * open gallery tree by dir name
         */
        opentree : function(cat_id, reload = false) {
            if (parseInt(wplgPostCategoriesTreeModule.wp_current_category_id) !== 0 && parseInt(wplgPostCategoriesTreeModule.wp_current_category_id) === parseInt(cat_id) && !reload) {
                return;
            }

            $('.posts_results').animate({
                scrollTop: 0
            }, 100);
            wplgPostCategoriesTreeModule.getTreeElement().find('li').removeClass('selected');
            wplgPostCategoriesTreeModule.getTreeElement().find('li[data-id="' + cat_id + '"]').removeClass('closed').addClass('selected');
            wplgPostCategoriesTreeModule.paged = 1;
            wplgPostCategoriesTreeModule.loadPostList(cat_id, wplgPostCategoriesTreeModule.paged);
            wplgPostCategoriesTreeModule.wp_current_category_id = cat_id;
        },

        /**
         * Close a gallery and hide children
         * @param cat_id
         */
        closetree : function(cat_id) {
            wplgPostCategoriesTreeModule.getTreeElement().find('li[data-id="' + cat_id + '"]').addClass('closed');
        },

        /**
         * Retrieve the Jquery tree view element
         * of the current frame
         * @return jQuery
         */
        getTreeElement : function() {
            return $('.wplg-categories-tree');
        }
    };

    $(document).ready(function () {
        wplgPostCategoriesTreeModule.init();
    });
})(jQuery);