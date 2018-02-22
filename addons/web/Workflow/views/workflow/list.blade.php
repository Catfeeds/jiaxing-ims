<div class="row" style="margin-left:0;margin-right:0;">

    @if(count($categorys))
    @foreach($categorys as $key => $category)

    <div class="col-md-4 col-sm-6 m-b">

        @if(count($category))
        @foreach($category as $k => $cat)

            <div class="panel b-a">
                <div class="panel-heading text-base b-b">
                    <i class="fa fa-file-text-o"></i> {{$cat['title']}}
                </div>

                <table class="table table-hover m-b-none">
                    @if(count($rows[$cat['id']]))
                    @foreach($rows[$cat['id']] as $row)
                    <tr>
                        <td><a class="pull-right" href="{{url('form/view', ['id' => $row['id']])}}"><i class="fa fa-eye"></i> 预览</a> <a onclick="workStart({{$row['id']}});" href="javascript:;">{{$row['title']}}</a></td>
                    </tr>
                    @endforeach
                    @endif
                </table>
            </div>

        @endforeach
        @endif
    </div>

    @endforeach
    @endif

</div>

<script type="text/javascript">
function workStart(id)
{
    $('#work-box').__dialog({
        title: '新建工作',
        url:'{{url("add")}}?id='+id,
        dialogClass:'modal-md',
        buttons:[{
            text: '确定',
            'class': 'btn-primary',
            click: function() {
                var myform = $('#myform').serialize();
                $.post('{{url("add")}}', myform, function(res) {
                    if (res.status) {
                        window.location.href= '{{url("edit")}}?process_id='+res.data.process_id;
                    } else {
                        $.toastr('error', res.data, '工作办理');
                    }
                },'json');
            }
        },{
            text: '取消',
            'class': 'btn-default',
            click: function() {
                $(this).dialog('close');
            }
        }]
    });
}
</script>
