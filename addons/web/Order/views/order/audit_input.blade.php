<style type="text/css">
.step_box { display:none; }
.content-body {
    margin: 0;
}
</style>

<div class="panel">

<form id="myform" name="myform" onsubmit="return false;" method="post">
<table class="table table-form" width="100%">

@if(count($flow['forms']))
@foreach($flow['forms'] as $k => $v)
<tr>
    <td align="right" width="16%">{{$v['title']}} @if($v['required']==1)<span style="color:red;">*</span> @endif </td>
    <td>
        {{eval('?>'.$v['text'])}}
    </td>
</tr>
@endforeach
@endif

<tr>
    <td align="right" width="15%">审核意见<span style="color:red;">*</span></th>
    <td><textarea class="form-control input-sm" id="content" name="flow[content][text]"></textarea></td>
</tr>

 @if(Auth::user()->role->name == 'finance')
<tr>
    <td align="right">订单备注</td>
    <td><textarea class="form-control input-sm" id="description" name="order[description]">{{$order['description']}}</textarea></td>
</tr>
<tr>
    <td align="right">随货同行</td>
    <td><textarea class="form-control input-sm" id="goods" name="order[goods]">{{$order['goods']}}</textarea></td>
</tr>
 @endif
<tr>
    <td align="right">审核类型</td>
    <td align="left">
        @if($flow['flow_step_state']=='next')
            <label class="radio-inline" style="color:green;font-weight:bold;"><input type="radio" name="order[flow_step_state]" id="step_state_next" onclick="stepBox('next');" value="next" checked>正常</label>
        @endif
        <label class="radio-inline" style="color:orange;font-weight:bold;"><input type="radio" name="order[flow_step_state]" id="step_state_last" onclick="stepBox('last');" value="last">退回</label>
        <label class="radio-inline" style="color:red;font-weight:bold;"><input type="radio" name="order[flow_step_state]" id="step_state_deny" onclick="stepBox('deny');" value="deny">拒绝</label>

        @if($flow['flow_step_state']=='end')
            <label class="radio-inline" style="font-weight:bold;"><input type="radio" name="order[flow_step_state]" id="step_state_end" onclick="stepBox('end');" value="end" checked>结束</label>
        @endif
    </td>
</tr>

<tr>
    <td align="right">分支选项</td>
    <td align="left">

        <span id="step_box_{{$flow['flow_step_state']}}" class="step_box" style="display: @if($flow['flow_step_state'] == 'next' || $flow['flow_step_state'] == 'end') block @else none; @endif ">
            <select readonly="readonly" id="flow_step_id" name="order[{{$flow['flow_step_state']}}][flow_step_id]">
                @if(count($steps['next']))
                @foreach($steps['next'] as $k => $v)
                    <option value="{{$k}}" @if(($materielCount>0&&$k==14)||($materielCount<=0&&$k==4)) selected @endif >{{$v}}</option>
                @endforeach
                @endif
            </select>
        </span>

        <span id="step_box_last" class="step_box" style="display:none;">
            <select readonly="readonly" id="flow_step_id" name="order[last][flow_step_id]">
                @if(count($steps['last']))
                @foreach($steps['last'] as $k => $v)
                    <option value="{{$k}}">{{$v}}</option>
                @endforeach
                @endif
            </select>
        </span>

        <span id="step_box_deny" class="step_box" style="display:none;">
            无
        </span>

    </td>
</tr>

<tr>
    <td></td>
    <td>
        <div class="alert alert-warning" role="alert">
            审核必须输入审核信息，而且审核信息不能修改，请仔细检查后提交。
        </div>
        <label class="checkbox-inline">
            <input name="sms" id="sms" type="checkbox" checked="true" value="true"> 短信提醒
        </label>
        <input type="hidden" name="order[id]" id="order_id" value="{{$order['id']}}" />
    </td>
</tr>
</table>

</form>

</div>

<script type="text/javascript">
function stepBox(type)
{
    $('.step_box').css('display','none');
    $('#step_box_'+type).css('display','block');
}

// 弹窗回调保存事件
function iframeSave()
{
    var formData = $('#myform').serialize();

    $.post("{{url()}}",formData, function(data) {

        if(data == 1) {
            app.alert('审核意见', '恭喜你，订单审核成功。', 'info');
            window.top.frames['tab_iframe_' + window.top.tabActiveId].location.reload();
            //window.parent.location.reload();
            return true;
        } else {
            app.alert('审核意见',data,'info');
        }
    });
}

// 弹窗回调取消事件
function iframeCancel()
{
    // window.parent.frames["main"].win.dialog('close');
}
</script>
