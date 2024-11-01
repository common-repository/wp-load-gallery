'use strict';

/**
 * Snackbar main module
 */
var wpSnackbarModule = void 0;
(function ($) {
    wpSnackbarModule = {
        snackbar_ids: [],
        $snackbar_wrapper: null, // Snackbar jQuery wrapper
        snackbar_defaults: {
            onClose: function onClose() {}, // Callback function when snackbar is closed
            is_undoable: false, // Show or not the undo button
            onUndo: function onUndo() {}, // Callback function when snackbar is undoed
            icon: '<span class="material-icons-outlined wplg-snack-icon"> notifications_active </span>',
            is_closable: true, // Can this snackbar be closed by user
            auto_close: true, // Do the snackbar close automatically
            auto_close_delay: 6000, // Time to wait before closing automatically
            is_progress: false, // Do we show the progress bar
            percentage: null // Percentage of the progress bar
        },

        /**
         * Initialize snackbar module
         */
        initModule: function initModule() {
            wpSnackbarModule.$snackbar_wrapper = $('<div class="wplg-snackbar-wrapper"></div>').appendTo('body');
        },

        /**
         * Display a new snackbar
         * @param options
         * @return HTMLElement the snackbar generated
         */
        show: function show(options) {
            if (options === undefined) {
                options = {};
            }

            // Set default values
            options = $.extend({}, wpSnackbarModule.snackbar_defaults, options);

            // If an id is set save it
            if (typeof options.id === "undefined") {
                options.id = options.content;
            }
            if (options.id !== undefined) {
                wpSnackbarModule.snackbar_ids[options.id] = options;
            }

            return wpSnackbarModule.renderSnack();
        },

        renderSnack: function renderSnack() {
            var snack = '<div class="wplg-snackbar-wrap">';
            var snack_count = 0;
            Object.keys(wpSnackbarModule.snackbar_ids).map(function (snack_id, index) {
                snack_count++;
                var options = wpSnackbarModule.snackbar_ids[snack_id];
                // Generate undo html if needed
                var undo = '';
                if (options.is_undoable) {
                    undo = '<a href="#" class="wplg-snackbar-undo">' + wplg.l18n.wplg_undo + '</a>';
                }

                var id = '';
                if (options.id) {
                    id = 'data-id="' + options.id + '"';
                }

                snack += '<div ' + id + ' class="wplg-snackbar">\n                        ' + options.icon + '\n                        <div class="wplg-snackbar-content">' + options.content + '</div>\n                        ' + undo + '                        \n                    </div>';
            });

            snack += '<a class="wplg-snackbar-close" href="#"><i class="material-icons">close</i></a>';
            snack += '</div>';

            // Add element to the DOM
            $('.wplg-snackbar-wrap').remove();
            if (snack_count > 0) {
                var $snack = $(snack).prependTo(wpSnackbarModule.$snackbar_wrapper);

                // Initialize undo function
                $snack.find('.wplg-snackbar-undo').click(function (e) {
                    var snack_id = $(this).closest('.wplg-snackbar').data('id');
                    e.preventDefault();
                    wpSnackbarModule.snackbar_ids[snack_id].onUndo();
                    // Reset the close function as we've done an undo
                    wpSnackbarModule.snackbar_ids[snack_id].onClose = function () {};
                    // Finally close the snackbar
                    wpSnackbarModule.snackbar_ids[snack_id].close(snack_id);
                });

                Object.keys(wpSnackbarModule.snackbar_ids).map(function (snack_id, index) {
                    // Initialize autoclose feature
                    var options = wpSnackbarModule.snackbar_ids[snack_id];
                    if (options.auto_close) {
                        setTimeout(function () {
                            wpSnackbarModule.close(options.id);
                        }, options.auto_close_delay);
                    }
                });

                // Initialize close button
                $snack.find('.wplg-snackbar-close').click(function (e) {
                    $(this).closest('.wplg-snackbar-wrap').remove();
                    wpSnackbarModule.snackbar_ids = [];
                });
            }
        },

        /**
         * Remove a snackbar and call onClose callback if needed
         * @param snack_id snackbar element
         */
        close: function close(snack_id) {
            // Remove the id if exists
            if (snack_id !== undefined) {
                delete wpSnackbarModule.snackbar_ids[snack_id];
            }

            wpSnackbarModule.renderSnack();
        },

        /**
         * Retrieve an existing snackbar from its id
         * @param id
         * @return {null|object}
         */
        getFromId: function getFromId(id) {
            if (wpSnackbarModule.snackbar_ids[id] === undefined) {
                return null;
            }

            return id;
        },

        /**
         * Set the snackbar progress bar width
         * @param $snack jQuery element representing a snackbar
         * @param percentage int
         */
        setProgress: function setProgress($snack, percentage) {
            if ($snack === null) {
                return;
            }

            var $progress = $snack.find('.wplgliner_progress > div');
            if (percentage !== undefined) {
                $progress.addClass('determinate').removeClass('indeterminate');
                $progress.css('width', percentage + '%');
            } else {
                $progress.addClass('indeterminate').removeClass('determinate');
            }
        }
    };

    // Let's initialize WPLG features
    $(document).ready(function () {
        wpSnackbarModule.initModule();
    });
})(jQuery);
