$(function() {

    var $document = $(document);

    // 新提示 
    $document.tooltip({
        container: 'body',
        placement: 'auto',
        selector:'.hinted',
        delay: {show: 200, hide: 0}
    });

    // 批量操作
    $('.select-all').on('click', function() {
        var tr = $('.select-row').closest('tr');
        if($(this).prop('checked')) {
            tr.addClass('success');
        } else {
            tr.removeClass('success');
        }
        $(".select-row").prop('checked', $(this).prop('checked'));
    });

    // 阻止事件冒泡
    $('.table tbody td a').on('click', function(e) {
        e.stopPropagation();
    });

    // 点击td选择行
    $('.table tbody tr').on('click', function(e) {

        var tr = $(this);
        var checkbox = tr.find('.select-row');
        var checked  = checkbox.prop('checked');

        if(checkbox.length == 0) {
            return;
        }

        if(e.target.tagName == 'INPUT') {
            setCheckbox(checked);
        }

        if(e.target.tagName == 'DIV') {
            setCheckbox(!checked);
        }

        if(e.target.tagName == 'TD') {
            setCheckbox(!checked);
        }

        function setCheckbox(checked) {
            if(checked) {
                tr.addClass('success');
            } else {
                tr.removeClass('success');
            }
            checkbox.prop('checked', checked);
        }
    });

    // 弹出用户角色部门
    $document.on('click', '[data-toggle="dialog-search"]', function() {
        var params = $(this).data();
        delete params['toggle'];
        var url = app.url('index/api/dialog', params);
        $('#dialog-search').__dialog({
            url: url,
            title: '',
            buttons: [{
                text: "确定",
                'class': "btn-default",
                click: function() {
                    $(this).dialog("close");
                }
            }]
        });
    });

    // 清除用户角色部门选择
    $document.on('click', '[data-toggle="dialog-search-clear"]', function() {
        var params = $(this).data();
        document.getElementById(params.id).value = '';
        document.getElementById(params.name).value = '';
        document.getElementById(params.id+'_text').innerHTML = '';
    });

    // 日期选择
    $document.on('click', '[data-toggle="date"]', function() {
        var format = $(this).data('format') || 'yyyy-MM-dd';
        datePicker({dateFmt: format});
    });

    // 日期时间选项
    $document.on('click', '[data-toggle="datetime"]', function() {
        var format = $(this).data('format') || 'yyyy-MM-dd HH:mm';
        datePicker({dateFmt: format});
    });

    // 显示产品图片
    $("a.goods-image").hover(function(e) {
        var img = $('<p id="goods"><img src="'+ this.rel + '" alt="" /></p>');
		$("body").append(img);

        $(this).find('img').stop().fadeTo('slow', 0.5);

        var $window = $(window);
        var $image = $(document).find('#goods');

		var height = $image.height();
        var width  = $image.width();

        var left = ($window.scrollLeft() + ($window.width() - width) / 2) + 'px';
        var top = ($window.scrollTop() + ($window.height() - height) / 2) +'px';

        var offset = $(this).offset();
        $image.css({left: offset.left + 100, top: top});
        $image.fadeIn('fast');

	}, function() {
	    $(this).find('img').stop().fadeTo('slow', 1);
		$("#goods").remove();
    });

    // 表格拖动排序
    $('#table-sortable tbody').sortable({
        // opacity: 0.6,
        delay: 50,
        cursor: "move",
        axis:"y",
        items: "tr",
        handle: 'td.move',
        // containmentType:"parent",
        // placeholder: "ui-sortable-placeholder",
        helper: function(event, ui) {
            // 在拖动时，拖动行的cell（单元格）宽度会发生改变。
            ui.children().each(function() {
                $(this).width($(this).width());
            });  
            return ui;
        },
        stop: function (event, ui) {
        }, 
        start: function (event, ui) {
            ui.placeholder.outerHeight(ui.item.outerHeight());
        },
        update: function() {
            var url = $(this).parent().attr('url');
            var orders = $(this).sortable("toArray");
            $.post(url, {sort:orders}, function(res) {
                $.toastr('success', res.data, '排序');
            });
        }
    })//.disableSelection();

});

var app = {
    /**
     * 确认窗口
     */
    confirm: function(url, content, title)
    {
        title = title || '操作确认';
        $.messager.confirm(title, content, function() {
            location.href = url;
        });
    },
    /**
     * 警告窗口
     */
    alert: function(title, content) {
        $.messager.alert(title, content);
    },
    /**
     * 获取附带基本路径的URL
     */
    url: function(uri, params) {
        if(uri == '/') {
            return settings.public_url;
        }
        query = (params == '' || params === undefined) ? '' : '?' + $.param(params);
        return settings.public_url + '/' + uri + query;
    },
    redirect: function(uri, params) {
        return window.location.href = app.url(uri, params);
    },
    /**
     * 汉字转换为拼音
     */
    pinyin: function(read, write, type) {
        type = type || 'first';
        var field = $('#'+write).val();
        if (field == '') {
            $.get(app.url('index/api/pinyin?type='+ type +'&id='+Math.random()), {name:$('#'+read).val()}, function(data) {
                $('#'+write).val(data);
            });
        }
    },
    dialog: function(options) {
        return _dialog(options);
    }
}

var uploader = {
    file: function(fileId) {
        var id = $('#'+fileId).find(".id").val();
        location.href = app.url('index/attachment/download',{id:id});
    },
    cancel: function(fileId) {
        var id = $('#'+fileId).find(".id").val();
        if (id > 0) {
            var name = $('#'+fileId).find(".file-name a").text();
            $.messager.confirm('删除文件', '确定要删除 <strong>'+name+'</strong> 此文件吗', function() {
                $.get(app.url('index/attachment/delete'), {id:id}, function(res) {
                    if(res == 1) {
                        $('#'+fileId).remove();
                    }
                });
            });
        } else {
            $('#'+fileId).remove();
        }
    },
    insert: function(fileId) {
        var id = $('#'+fileId).find(".id").val();
        var name = $('#'+fileId).find(".file-name a").text();
        // 检查图片类型
        if(/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(name)) {
            var html = '<img src="' + app.url('index/attachment/show',{id: id}) + '" title="'+name+'">';
        } else {
            var html = '<a href="' + app.url('index/attachment/download',{id: id}) + '" title="'+name+'">'+name+'</a>';
        }
        UE.getEditor("content").execCommand('insertHtml', html);
    }
}

/**
 * 跳转地址，支持打开目标
 */
function redirect(href, target) {
    if(href) {
        if(target == '_blank') {
            window.open(href, "_blank");
        }  else  {
            self.location.href = href;
        }
    }
}

/** 
 * 框架盒子
 */
function iframeBox(title, src, ok, cancel) {
    ok     = ok || '确定';
    cancel = cancel || '取消';

    $('#iframe-box').__dialog({
        title: title,
        dialogClass: 'modal-lg',
        backdrop: 'static',
        html: '<iframe id="iframe-dialog" name="iframe-dialog" src="'+src+'" scrolling="auto" frameborder="0" width="100%" height="100%"></iframe>',
        buttons: [{
            text: ok,
            'class': "btn-info",
            click: function() {
                var dialog = window.frames['iframe-dialog'].iframeSave();
                if(dialog == true) {
                    $(this).find('#iframe-dialog').attr('src', 'about:blank');
                    $(this).dialog("close");
                }
            }
        },{
            text: cancel,
            'class': "btn-default",
            click: function() {
                window.frames['iframe-dialog'].iframeCancel();
                $(this).find('#iframe-dialog').attr('src', 'about:blank');
                $(this).dialog("close");
            }
        }]
    });
}

/**
 * 模式弹窗
 */
function dialogUser(title, url, id, multi)
{
    var url = app.url(url, {id:id, multi:multi});
    $('#dialog-user').__dialog({
        title: title,
        url: url,
        buttons: [{
            text: "确定",
            'class': "btn-default",
            click: function() {
                if(window.selectBox) {
                    window.selectBox.getSelecteds();
                } else {
                    $(this).dialog("close");
                }
            }
        }]
    });
}

/**
 * 清除已经选择的
 */
function dialogClear(id, name) {
    document.getElementById(id).value = '';
    document.getElementById(name).innerHTML = '';
}

/**
 * 表单窗口
 */
function formBox(title, url, id, success, error)
{
    id = id || 'myform';

    $('#box-' + id).__dialog({
        title: title,
        url: url,
        buttons: [{
            text: '保存',
            class: 'btn-info',
            click: function() {
                var me = this;
                var action = $('#'+id).attr('action');
                var formData = $('#'+id).serialize();
                $.post(action, formData, function(res) {

                    if (typeof success === 'function') {
                        success.call(me, res); 
                    } else {
                        if(res.status) {
                            if(res.data == 'reload') {
                                window.location.reload();
                            } else {
                                $.toastr('success', res.data, '提醒');
                                $(me).dialog("close");
                            }
                        } else {
                            $.toastr('error', res.data, '提醒');
                        }
                    }

                },'json');
            }
        },{
            text: '取消',
            class: 'btn-default',
            click: function() {
                var me = this;
                if (typeof error === 'function') {
                    error.call(me, res); 
                } else {
                    $(me).dialog("close");
                }
            }
        }]
    });
}

/**
 * 显示窗口
 */
function viewBox(name, title, url, size) {

    size = size || 'md';

    return $('#view-box').__dialog({
        title: title,
        url: url,
        dialogClass:'modal-' + size,
        buttons: [{
            text: "确定",
            'class': "btn-default",
            click: function() {
                $(this).dialog("close");
            }
        }]
    });
}

var viewDialogIndex = 0;
function viewDialog(name, url, options) {
    var defaults = {
        title: name,
        url: url,
        buttons: [{
            text: "确定",
            'class': "btn-default",
            click: function() {
                $(this).dialog("close");
            }
        }]
    };
    viewDialogIndex ++;
    var settings = $.extend({}, defaults, options);
    $('#view-dialog-' + viewDialogIndex).__dialog(settings);
}

/**
 * 快捷表单窗口
 */
function quickForm(table, title, url, size)
{
    size = size || 'md';

    $('#quick-form').__dialog({
        title: title,
        url: url,
        dialogClass: 'modal-' + size,
        destroy : true,

        buttons: [{
            text: "提交",
            'class': "btn-info",
            click: function(e) {

                var gets = $('#'+table+'_form').serialize();
                var rows = {};

                // 循环子表
                var tables = jqgridFormList[table];

                if(tables.length) {

                    for(var i=0; i < tables.length; i++) {
                        var t = tables[i];
                        var p = t[0].p;
                        var deleteds = p.deleteds;

                        // 有选择按钮
                        if(p.multiselect == true) {

                            var dataset = t.jqGrid('getSelections');

                            if(dataset.length == 0) {
                                $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                return;
                            } else {
                                rows[p.table] = {rows: dataset, deleteds: deleteds};
                            }

                        } else {

                            var dataset = t.jqGrid('getDatas');
                            if(dataset.v === true) {
                                if(dataset.data.length == 0) {
                                    $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                    return;
                                } else {
                                    rows[p.table] = {rows: dataset.data, deleteds: deleteds};
                                }
                            } else {
                                return;
                            }
                        }
                    }
                }

                gets = gets +'&'+ $.param(rows);

                var btn = $(e.target);
                btn.prop('disabled', true);
                btn.text('提交中');

                $.post(app.url('flow/form/store'), gets, function(res) {

                    btn.prop('disabled', false);
                    btn.text('提交');

                    if(res.status) {
                        location.reload();
                    } else {
                        $.toastr('error', res.data, '提醒');
                    }

                },'json');

            }
        },{
            text: "取消",
            'class': "btn-default",
            click: function(e) {
                $(this).dialog("close");
            }
        }]
    });
}

/**
 * 图片窗口
 */
function imageBox(name, title, url) {

    $('#image-box').__dialog({
        title: title,
        html: '<img style="text-align:center;max-width:100%;" src="'+url+'" />',
        buttons: [{
            text: "确定",
            'class': "btn-default",
            click: function() {
                $(this).dialog("close");
            }
        }]
    });
}

/**
 * 动态创建div
 */
function createDiv(id) {
    if(document.getElementById(id) == null) {
        $("<div>",{id:id}).appendTo("body");
    }
}

/**
 * 清除已经选择的
 */
function selectClear(id, name) {
    document.getElementById(id).value = '';
    document.getElementById(id+'_text').innerHTML = '';
}

function optionSort(id, url) {
    var e = $(id);
    e.attr('action', url);
    e.submit();
}

function optionDefault(id, url, target) {

    var count = $("input.select-row[type='checkbox']:checked").length;

    var e = $(id);
    e.attr('action', url);
    if(target == '_blank') {
        e.attr('target', '_blank');
    }

    if(count > 0) {
        e.submit();
    } else {
        $.toastr('error', '最少选择一行记录。', '错误');
    }
}

function optionDelete(id, url, content) {
    var count = $("input.select-row[type='checkbox']:checked").length;
    content = content || '确定要删除吗？';

    var e = $(id);
    e.attr('action', url);
    e.attr('target', '_self');

    if(count > 0) {

        top.$.messager.confirm('操作确认', content, function() {
            e.submit();
        });

    } else {
        $.toastr('error', '最少选择一行记录。', '错误');
    }
}

// 转换时间，计算差值
function niceTime(timestamp) {
    // 当前时间戳
    var nowtime = (new Date).getTime();

    // 计算时间戳差值
    var secondNum = parseInt((nowtime-timestamp*1000)/1000);

    if(secondNum >= 0 && secondNum < 60) {
        return secondNum+'秒前';
    } else if (secondNum >= 60 && secondNum < 3600) {
        var nTime = parseInt(secondNum/60);
        return nTime+'分钟前';
    } else if (secondNum >= 3600 && secondNum < 3600*24) {
        var nTime = parseInt(secondNum/3600);
        return nTime+'小时前';
    }  else {
        var nTime = parseInt(secondNum/86400);
        return nTime+'天前';
    }
}

function ucfirst(str) {
    if(str) {
        return str[0].toUpperCase() + str.substr(1);
    } else {
        return str;
    }
}

/** 
* 数字金额大写转换(可以处理整数,小数,负数)
*/
function digitUppercase(n) {
    var fraction = ['角','分'];
    var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
    var unit = [
        ['元', '万', '亿'],
        ['', '拾', '佰', '仟']
    ];
    var head = n < 0 ? '欠' : '';
    n = Math.abs(n);
    var s = '';
    for (var i = 0; i < fraction.length; i++) {  
        s += (digit[Math.floor(n * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');  
    }  
    s = s || '整';
    n = Math.floor(n);
    for (var i = 0; i < unit[0].length && n > 0; i++) {
        var p = '';
        for (var j = 0; j < unit[1].length && n > 0; j++) {
            p = digit[n % 10] + unit[1][j] + p;
            n = Math.floor(n / 10);
        }
        s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;
    }
    return head + s.replace(/(零.)*零元/, '元').replace(/(零.)+/g, '零').replace(/^整$/, '零元整');  
};

/**
 * 检查变量是否为空
 */
function isEmpty(value) {
    if(value == '' || value == undefined || value == null) {
        return true;
    }
    return false;
}

/**
 * 清除字符串两边的空格
 */
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, '');
}

// 数组过滤函数
if (!Array.prototype.filter) {
  Array.prototype.filter = function(fun /*, thisp*/)
    {
    var len = this.length;
    if (typeof fun != "function")
      throw new TypeError(); 
     var res = new Array();
    var thisp = arguments[1];
    for (var i = 0; i < len; i++)
    {
      if (i in this)
      {
        var val = this[i]; // in case fun mutates this
        if (fun.call(thisp, val, i, this))
          res.push(val);
      }
    } 
     return res;
  };
}

/**
 * 判断设备支持
 */
function isSmartDevice($window) {
    // Adapted from http://www.detectmobilebrowsers.com
    var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
    // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
    return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
}

function url(uri, params) {
    query = (params == '' || params === undefined) ? '' : '?' + $.param(params);
    return settings.public_url + '/' + uri + query;
}

/**
 *  时间戳格式化
 */
function format_datetime(value) 
{
    function add0(v) {
        return v < 10 ? '0' + v : v;
    }
    value = parseInt(value) * 1000;
    var time = new Date(value);
    var y = time.getFullYear();
    var m = time.getMonth()+1;
    var d = time.getDate();
    var h = time.getHours();
    var mm = time.getMinutes();
    var s = time.getSeconds();
    return y+'-'+add0(m)+'-'+add0(d)+' '+add0(h)+':'+add0(mm);
}

/** ajax 提交 */
function ajaxSubmit(id, callback) {

    $(id).submit(function() {

        var url  = $(this).attr('action');
        var data = $(this).serialize();

        $.post(url, data, function(res) {
            if(typeof callback === 'function') {
                callback(res);
            } else {
                if(res.status) {
                    $.toastr('success', res.data, '提醒');
                    if(res.url) {
                        self.location.href = res.url;
                    }
                } else {
                    $.toastr('error', res.data, '提醒');
                }
            }
        }, 'json');
        return false;
    });
}

/** flow分页模块 */
(function ($) {

   $(document).on('click', '.page_value', function() {

        var q    = $(this).data('q');
        var url  = $(this).data('url');
        var html = $(this).prop('outerHTML');

        var text = $(this).text();
        var $input = $('<input value="'+text+'" type="text" style="width:60px;" class="form-control input-inline input-xs text-right">');
        $('.page_limit').html($input);

        $input.focus();

        $input.on('keydown', function(e) {
            var key = e.which;
            if (key == 13) {
                e.preventDefault();
                $input.trigger('blur');
            }
        });

        $input.on('blur', function() {

            var value = $(this).val();

            if(value == text || value == '') {
                $('.page_limit').html(html);
                return;
            }

            value = value.split('-');

            var min = parseInt(value[0]);
            var max = parseInt(value[1]);
            if(max == NaN) {
                q['limit'] = 1;
            }
            if(min && max) {
                if(min == max || min > max) {
                    q['limit'] = 1;
                } else {
                    q['limit'] = max - min + 1;
                }
            }
            q['page'] = Math.ceil(min / q['limit']);
            self.location.href = app.url(url, q);
        });
    });
})(jQuery);