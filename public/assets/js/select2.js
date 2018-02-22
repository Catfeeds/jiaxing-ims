(function($) {

    $.fn.select2Field = function(options) {

        $this = $(this);

        var defaults = {
            width: '240px',
            placeholder:'',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                type: 'POST',
                url: '',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        field_0: 'user.name',
                        condition_0: 'like',
                        search_0: params.term,
                        page: params.page
                    };
                },
                processResults: function (res, params) {
                    this.options.data = res.data;
                    params.page = params.page || 1;
                    return {
                        results: res.data,
                        pagination: {
                            more: (params.page * 30) < res.total
                        }
                    };
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            }, 
            formatResult: function(m) {
                return m.text;
            }, 
            // 函数用来渲染结果
            formatSelection: function(m) {
                return m.text;
            }
        };

        options = $.extend(true, {}, defaults, options);
        var abc = $this.select2(options);
        return this;
    }
})(jQuery);