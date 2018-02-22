<script type="text/javascript">
window.UEDITOR_HOME_URL = '{{$asset_url}}/vendor/ueditor/';
</script>
<script src="{{$asset_url}}/vendor/ueditor/ueditor.model.config.js"></script>
<script src="{{$asset_url}}/vendor/ueditor/ueditor.all.min.js"></script>
<script src="{{$asset_url}}/vendor/ueditor/ueditor.model.js"></script>

<form method="post" id="myform" name="myform">

    <div class="panel">

    <div class="panel-heading b-b b-light">
        <h4>{{$model['name']}}[{{$model['id']}}]</h4>
    </div>

    <div class="panel-body">

    <div class="row">

            <div class="col-sm-2 m-b">

                <ul class="list-group">
                @foreach($rows as $row)
                    <a href="javascript:abc('{{$row->form_type}}','{{$row['field']}}','{{$row['name']}}');" class="list-group-item"><i class="icon icon-plus"></i> {{$row['name']}}</a>
                @endforeach
                </ul>

                <div class="form-inline text-center">
                    <a onclick="tool.close();" class="btn btn-default">引用</a>
                    <a onclick="tool.review({{$row['id']}})" class="btn btn-default">预览</a>
                    <a onclick="tool.checkForm()" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</a>
                </div>

            </div>

            <div class="col-sm-10">
                <script type="text/plain" id="editor" name="template">{{$row['template']}}</script>
            </div>

        </div>
    </div>

    </div>

    <input type="hidden" name="work_id" id="work_id" value="{{$row['id']}}">
    <input type="hidden" name="count_item" id="count_item" value="{{url('count')}}?work_id={{$row['id']}}">
</div>
</form>

<script type="text/javascript">
var tool = {
    checkForm: function(type) {

        // 显示loading
        if(editor.hasContents()) {

            // 同步内容
            editor.sync();

            if(typeof type !== 'undefined') {
                document.myform.type.value = type;
            }
            
            var myform = $('#myform').serialize();
            $.post('{{url("create")}}', myform, function(res) {
                if(res.status) {
                    $.toastr('success', '保存成功。');
                }
            },'json');

        } else {
            $.toastr('error', '表单内容不能为空。');
            return false;
        }
    },
    close: function() {
        $.messager.confirm('操作确认', '关闭表单前，您是否要保存？', function(res) {
            if(res) {
                this.checkForm('close');
            }
        });
    },
    control: function(method) {
        editor.execCommand(method);
    },
    review: function(id) {
        $('#work-review').__dialog({
            title:'表单预览',
            dialogClass:'modal-lg',
            url:app.url('workflow/form/view', {review:true,id:id}),
            buttons:[{
                text: '确定',
                'class': 'btn-primary',
                click: function() {
                    $(this).dialog('close');
                }
            },{
                text: '取消',
                'class': 'btn-default',
                click: function() {
                    $(this).dialog('close');
                }
            }]
        });
    },
    checkClose: function() {
        if(event.clientX > document.body.clientWidth-20 && event.clientY < 0 || event.altKey) {
            window.event.returnValue = '您确定退出表单设计器吗';
        }
    }
}
var editor = UE.getEditor('editor',{'minFrameHeight':480,'initialFrameWidth':'100%'});

function abc(type, field, title) {
    var table = '{{$model->table}}';
    var id    = table +'-'+ field;
    var field = table +'['+ field + ']';

    if(type == 'text') {
        editor.execCommand('inserthtml', '<input type="text" id="'+id+'" name="'+field+'" title="'+title+'" data-toggle="model-field" class="form-control input-sm">');
    }
    
}

</script>
<div onbeforeunload="tool.checkClose();">
