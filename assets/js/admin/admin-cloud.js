(function ($) {
    $(document).ready(function () {
        $('.wplg-cloud-tab-list li').on('click', function () {
            var tab = $(this).data('tab');
            $('.wplg-cloud-tab-list li').removeClass('active');
            $('.wplg-cloud-tab-content').hide();
            $(this).addClass('active');
            $('.wplg-cloud-tab-content[data-tab="'+ tab +'"]').show();
        });
    });
})(jQuery);