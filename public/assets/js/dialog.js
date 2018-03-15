(function($) {

    var COUNT = 0

    $.fn.dialog = function(options) {

        var self = this,
        $this    = $(self),
        $body    = $(document.body),
        $element = $this.closest('.dialog'),
        parentId = 'dialog-parent';

        this.options = options;

        var create = function() {

            // fade
            var element = '<div class="dialog modal"><div class="modal-dialog"><div class="modal-content">';

            if(options.title) {
                element += '<div class="modal-header"><button type="button" data-dismiss="dialog" class="close">&times;</button><h4 class="modal-title"></h4></div>';
            }

            element += '<div class="modal-body"></div>';

            if(options.buttons.length > 0) {
                element += '<div class="modal-footer"></div>';
            }

            element += '</div></div></div>';

            // 窗口创建一个以上遮罩只显示一层
            //options.backdrop = COUNT > 0 ? '' : options.backdrop;

            $element = $(element);
            $body.append($element);
            $element.find(".modal-body").append($this);
            $element.modal({backdrop:options.backdrop});

            //if(options.draggable === true) {
                $('.modal-dialog').draggable({
                    handle: ".modal-header",
                    iframeFix: true,
                });
            //}
        }

        var createButton = function(_options) {

            var buttons = (_options || options || {}).buttons || {},
            $btnrow = $element.find(".modal-footer");

            // clear old buttons
            $btnrow.html('');

            for (var button in buttons) {

                var btn = buttons[button],
                    id      = '',
                    text    = '',
                    classed = 'btn-default',
                    click   = '';

                if (btn.constructor == Object) {
                    id      = btn.id;
                    text    = btn.text;
                    classed = btn['class'] || btn.classed || classed;
                    click   = btn.click;
                }

                if (btn.constructor == Function) {
                    text  = button;
                    click = btn;
                }

                $button = $('<button type="button" class="btn '+ classed +'">'+ text +'</button>');

                if(id) {
                    $button.attr("id", id);
                }

                if (click) {
                    (function(click) {
                        $button.on('click', function(e) {
                            click.call(self, e);
                        });
                    })(click);
                }
                $btnrow.append($button);
            }
            $btnrow.data('buttons', buttons);
        }

        var show = function() {
            $element.modal('show');
            var showHandler = options.onShow || function() {};
            showHandler.call(self);
        }

        var close = function() {
            $element.modal('hide');
        }

        var destroy = function() {
            //$this.data(parentId).append($this);
            $element.remove();
        }

        if (options.constructor == Object) {

            var defaults = {
                show: true,
                backdrop: true,
                destroy: false
            }

            options = $.extend(defaults, options);

           // if(!$this.data(parentId)) {
                //$this.data(parentId, $this.parent());
            //}

            if ($element.size() == 0) {
                create();
            }

            createButton();

            $element.find('.modal-title').html(options['title']);

            if(options['dialogClass']) {
                $element.find('.modal-dialog').addClass(options['dialogClass']);
            }

            $element.on('click',"[data-dismiss='dialog']", function() {
                var closeHandler = options.onClose || close;
                closeHandler.call(self);
            });

            $element.one('show.bs.modal', function() {
                COUNT++;
            })

            $element.one('hidden.bs.modal', function() {

                COUNT--

                if(COUNT > 0) {
                    $element.modal('checkScrollbar')
                    $body.addClass('modal-open')
                    $element.modal('setScrollbar')
                }
                
                // 取消绑定的事件
                $element.off('click',"[data-dismiss='dialog']");

                // 删除 $element
                if(options.destroy == true) {
                    destroy();
                }
            })

            if(options.modalClass) {
                $element.addClass(options.modalClass);
            }

            if(options.show) {
                show();
            }
        }

        if (options == "destroy") {
            options.destroy = true
            close();
        }
      
        if (options == "close") {
            close();
        }

        if (options == "show") {
            show();
        }
        return self;
    }
})(jQuery);

(function($) {

    var modal = {
        ok: {text: "确定", classed: 'btn-danger'},
        cancel: {text: "取消", classed: 'btn-default'}
    };

    $.messager = {};
    $.messager.alert = function(title, message, callback) {

        if (arguments.length < 2) {
            message = title || "";
            title   = "&nbsp;"
        }

        $("<div>" + message + "</div>").dialog({
            title:   title,
            destroy : true,
            dialogClass:'modal-sm',
            buttons: [{
                text: modal.ok.text, 
                classed: modal.ok.classed || "btn-success", 
                click: function() {

                    if(typeof callback === 'function') {
                        callback();
                    }
                    $(this).dialog("destroy");
                }
            }]
        });
    };

    $.messager.confirm = function(title, message, callback) {
        $("<div>" + message + "</div>").dialog({
            title: title,
            destroy : true,
            backdrop: 'static',
            dialogClass:'modal-sm',
            buttons: [{
                text: modal.ok.text,
                classed: modal.ok.classed || "btn-success", 
                click: function() {

                    $(this).dialog("destroy");

                    if(typeof callback === 'function') {
                        callback();
                    }
                }
            },{
                text: modal.cancel.text,
                classed : modal.cancel.classed || "btn-danger",
                click: function() {
                    $(this).dialog("destroy");
                }
            }]
        });
    };
})(jQuery);

(function($) {

    $.fn.__dialog = function(options) {

        var defaultOptions = {
            title:'Dialog',
            modalClass:'no-padder',
            dialogClass:'modal-md',
            destroy: true,
            onShow: function() {
                var me = this;
                if (options['url']) {
                    $.get(options['url'], function(data) {
                        me.html(data);
                    });
                }
                if (options['html']) {
                    this.html(options['html']);
                }

            },
            buttons:[{
                text: "确定",
                'class': "btn-primary",
                click: function() {
                    $(this).dialog("close");
                }
            },{
                text: "取消",
                click: function() {
                    $(this).dialog("close");
                }
            }]
        };

        options = $.extend(defaultOptions, options);

        var id = 'modal-';
        if(this.length) {
            id = 'modal-' + this.attr('id');
        }
        else if(this.selector) {
            id = this.selector.replace('#','modal-');
        }

        var $target = $('#'+id);
        if($target.length == 0) {
            $target = $('<div/>',{id:id});
        }
        $target.dialog(options);
        //$('.modal-content').hide();

        return $target;
    }
})(jQuery);

(function($) {

    $.toastr = function(type, title, content) {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            //"positionClass": "toast-top-center",
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr[type](title, content)
    }
})(jQuery);