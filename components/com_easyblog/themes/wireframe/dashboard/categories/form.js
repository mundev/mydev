
EasyBlog.ready(function($) {

    $('#private').on('change', function() {
        var val = $(this).val(),
            el = $('[data-category-access]');

        if (val == 2) {
            $(el).removeClass('hide');
        } else {
            $(el).addClass('hide');
        }
    });

});
