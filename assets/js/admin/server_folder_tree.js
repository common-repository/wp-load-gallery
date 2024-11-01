(function ($) {
    $(document).ready(function () {
        var options = {
            'root': '/',
            'showroot': wplg_objects.l18n.server_folders,
            'onclick': function (elem, type, file) {
            },
            'oncheck': function (elem, checked, type, file) {
            },
            'usecheckboxes': false, //can be true files dirs or false
            'expandSpeed': 500,
            'collapseSpeed': 500,
            'expandEasing': null,
            'collapseEasing': null,
            'canselect': true
        };

        var methods = {
            init_sync: function () {
                $server_folder_wrap = $('.wplg_server_folder');
                if ($server_folder_wrap.length === 0) {
                    return;
                }

                $server_folder_wrap.prepend('<ul class="wplg_server_tree"><li class="server_directory wplg_tree_close selected"><a class="server_folder_title" href="#" data-file="' + options.root + '"></a></li></ul>');
                openfolder_sync(options.root);
            },
            open_sync: function (dir) {
                openfolder_sync(dir);
            },
            close_sync: function (dir) {
                closedir_sync(dir);
            },
            getchecked: function () {
                $(".wplg_server_folder span.check").unbind('click').bind('click', function () {
                    $(this).toggleClass('checked');
                    $('.wplg_server_folder .server_folder_checkbox').prop('checked', false);
                    $('.wplg_server_folder .check').not(this).removeClass('checked');
                });
            }
        };

        var openfolder_sync = function (dir , callback) {
            if ($server_folder_wrap.find('a[data-file="' + dir + '"]').parent().hasClass('wplg_tree_open')) {
                return;
            }

            if ($server_folder_wrap.find('a[data-file="' + dir + '"]').parent().hasClass('wplg_tree_open') || $server_folder_wrap.find('a[data-file="' + dir + '"]').parent().hasClass('wait')) {
                if (typeof callback === 'function')
                    callback();
                return;
            }
            var ret;
            ret = $.ajax({
                url: ajaxurl,
                method:'POST',
                data: {
                    dir: dir,
                    action: 'wpgallery',
                    task: 'load_server_folder',
                    wplg_nonce: wplg_objects.vars.wplg_nonce
                },
                context: $server_folder_wrap,
                dataType: 'json',
                beforeSend: function () {
                    $('.wplg_server_folder').find('a[data-file="' + dir + '"]').parent().addClass('wait');
                }
            }).done(function (datas) {
                ret = '<ul class="wplg_server_tree" style="display: none">';
                for (var ij = 0; ij < datas.length; ij++) {
                    var classe = 'server_directory wplg_tree_close';
                    var isdir = '/';
                    ret += '<li class="' + classe + '">';
                    ret += '<input type="checkbox" class="server_folder_checkbox" data-file="' + dir + datas[ij].file + isdir + '" />';
                    ret += '<span class="check" data-file="' + dir + datas[ij].file + isdir + '"></span>';
                    ret += '<i class="material-icons-outlined server_folder_icon">folder</i>';
                    ret += '<a class="server_folder_title" href="#" data-file="' + dir + datas[ij].file + isdir + '">' + datas[ij].file + '</a>';
                    ret += '</li>';
                }
                ret += '</ul>';

                $('.wplg_server_folder').find('a[data-file="' + dir + '"]').parent().removeClass('wait').removeClass('wplg_tree_close').addClass('wplg_tree_open');
                $('.wplg_server_folder').find('a[data-file="' + dir + '"]').after(ret);
                $('.wplg_server_folder').find('a[data-file="' + dir + '"]').next().slideDown(options.expandSpeed, options.expandEasing,
                    function () {
                        $server_folder_wrap.trigger('afteropen');
                        $server_folder_wrap.trigger('afterupdate');
                        if (typeof callback === 'function')
                            callback();
                    });
                setevents_sync();
            }).done(function () {
                //Trigger custom event
                $server_folder_wrap.trigger('afteropen');
                $server_folder_wrap.trigger('afterupdate');
            });

        };

        var closedir_sync = function (dir) {
            $server_folder_wrap.find('a[data-file="' + dir + '"]').next().slideUp(options.collapseSpeed, options.collapseEasing, function () {
                $(this).remove();
            });
            $server_folder_wrap.find('a[data-file="' + dir + '"]').parent().removeClass('wplg_tree_open').addClass('wplg_tree_close');
            setevents_sync();

            //Trigger custom event
            $server_folder_wrap.trigger('afterclose');
            $server_folder_wrap.trigger('afterupdate');

        };

        var setevents_sync = function () {
            $server_folder_wrap = $('.wplg_server_folder');
            $server_folder_wrap.find('li a').unbind('click');
            //Bind for collapse or expand elements
            $server_folder_wrap.find('li.server_directory.wplg_tree_close a').bind('click', function () {
                methods.open_sync($(this).attr('data-file'));
                return false;
            });
            $server_folder_wrap.find('li.server_directory.wplg_tree_open a').bind('click', function () {
                methods.close_sync($(this).attr('data-file'));
                return false;
            });
        };

        /**
         * Folder tree function
         */
        methods.init_sync();
        jQuery('.wplg_server_folder').bind('afteropen', function () {
            methods.getchecked();
        });
    });
})(jQuery);