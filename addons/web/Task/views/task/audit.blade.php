<div class="panel">
<form method="post" id="myform" name="myform">
<table class="table table-form">
    <tr>
        <td width="100" align="right">回复正文</td>
        <td align="left">{{$reply['content']}}</td>
    </tr>
    <tr>
        <td align="right">回复时间</td>
        <td align="left">@datetime($reply['add_time'])</td>
    </tr>
    <tr>
        <td align="right">任务评分</td>
        <td align="left">
            <select class="form-control input-inline input-sm" id='vote' name='vote'>
                <option value='0'>不评分</option>
                {{:$range = range(1,5)}}
                @if(count($range)) 
                @foreach($range as $k => $v)
                    <option value='{{$v}}' @if($reply['vote']==$v) selected @endif>{{$v}}</option>
                @endforeach @endif
            </select>
            <span class="help-inline">分</span>
        </td>
    </tr>
    <tr>
        <td align="right">任务状态</td>
        <td align="left">
            <select class="form-control input-inline input-sm" id='status' name='status'>
                @foreach(option('task.status') as $status)
                <option value='{{$status['id']}}' @if($reply['status'] == $status['id']) selected @endif>{{$status['name']}}</option>
                @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">审核说明</td>
        <td align="left"><textarea rows="3" class="form-control input-sm" id="reject" name="reject">{{$reply['reject']}}</textarea></td>
    </tr>

</table>

<input type="hidden" name="id" id="id" value="{{$reply['id']}}">
<input type="hidden" name="instruct_id" id="instruct_id" value="{{$reply['instruct_id']}}">

</div>

</form>

</div>

<script type="text/javascript">
// 弹窗回调保存事件
function iframeSave(dialog)
{
    var formData = $('#myform').serialize();
    $.post("{{url()}}?id={{$reply['id']}}",formData, function(data) {
        if(data == 1)
        {
            app.alert('审核任务', '恭喜你，任务审核完成。', 'info');
            //window.parent.frames.location.reload();
            window.parent.frames[top.frameId].reloadData();
            return true;
        } else  {
            app.alert('审核任务',data,'info');
        }
    });
}

//弹窗回调取消事件
function iframeCancel()
{
    //window.parent.frames["main"].win.dialog('close');
}
</script>
