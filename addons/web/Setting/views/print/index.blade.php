@include('layouts/map')

<div class="panel no-border">

    @include('tabs', ['tabKey' => 'setting.print'])

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
            <li class="@if($search['query']['size'] == 'a4') active @endif">
                <a class="text-sm" href="{{url(null, $search['query'], ['size' => 'a4'])}}">A4格式</a>
            </li>
            <li class="@if($search['query']['size'] == 's') active @endif">
                <a class="text-sm" href="{{url(null, $search['query'], ['size' => 's'])}}">小票格式</a>
            </li>
        </ul>
    </div>

    <div class="wrapper-sm b-b m-b">
        @if(isset($access['create']))
        <a class="btn btn-sm btn-info" href="javascript:formTemplate();"><i class="fa fa-plus"></i> 更新模板</a>
        @endif
        @if(isset($access['export']))
        <a class="btn btn-sm btn-default" href="{{url('export', ['node' => $query['node'], 'size' => $query['size']])}}"><i class="fa fa-share-square"></i> 导出模板</a>
        @endif

        @if(isset($access['param']))
        <a class="btn btn-sm btn-default" href="javascript:viewBox('param', '参数列表', '{{url('param', ['node' => $query['node'], 'size' => $query['size']])}}', 'lg');"><i class="fa fa-file-text-o"></i> 支持参数</a>
        @endif

        @if(isset($access['demo']))
        <a class="btn btn-sm btn-default" href="{{url('demo', ['node' => $query['node'], 'size' => $query['size']])}}"><i class="fa fa-eye"></i> 演示模板</a>
        @endif
    </div></div>
    
    <div class="panel1" style="margin:0 auto;text-align:center;">
        <div id="template"></div>
    </div>

<script>
var params = {{json_encode($search['query'])}};
(function($) {
    $.get(app.url('setting/print/template'), params, function(res) {
        $('#template').html(res);
    });
})(jQuery);

function formTemplate()
{
    var url = "{{url('create', ['node' => $query['node'], 'size' => $query['size']])}}";
    formDialog({
        title: '更新模板',
        url: url,
        id: 'create-form',
        formId: 'create-form',
        dialogClass: 'modal-md',
        onBeforeSend: function(query) {
            var formData = new FormData();
            formData.append('file', $('#file')[0].files[0]);
            $.ajax({
                url: url,
                type: 'post',
                cache: false,
                data: formData,
                processData: false,
                contentType: false
            }).done(function(res) {
                if (res.status) {
                    window.location.reload();
                } else {
                    $.toastr('error', res.data, '错误');
                }
            }).fail(function(res) {
                $.toastr('error', res, '错误');
            });
            return false;
        }
    });
}

/*
function getPanelHeight() {
    var list = $('.list-jqgrid').position();
    return top.iframeHeight - list.top - 42;
}

$(window).on('resize', function() {
	$table.jqGrid('setGridHeight', getPanelHeight());
});
*/
</script>