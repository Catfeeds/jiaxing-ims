<div class="panel">

<form method="post" action="{{url()}}" id="myform" name="myform">

    <div class="table-responsive">
        <table class="table table-form m-b-none">
            <tr>
                <td align="right">任务主题</td>
                <td align="left"><input class="form-control input-sm" type="text" name="title" id="title" value="{{$res['title']}}">
                </td>
            </tr>

            <tr>
            <td align="right">任务相关人</td>
            <td>
                {{Dialog::user('user', 'user_ids', $res['user_ids'], 1)}}
            </td>
            </tr>

            <tr>
                <td align="right" width="120">开始时间</td>
                <td align="left"><input class="form-control input-inline input-sm" data-toggle="datetime" class="date input-text" size="24" type="text" name="add_time" id="add_time" value="@datetime($res['add_time'], time())"></td>
            </tr>
            <tr>
                <td align="right">结束时间</td>
                <td align="left"><input class="form-control input-inline input-sm" data-toggle="datetime" class="date input-text" size="24" type="text" name="end_time" id="end_time" value="@datetime($res['end_time'])"></td>
            </tr>

            <tr>
                <td align="right">附件列表</td>
                <td align="left">
                    @include('attachment/add')
                </td>
            </tr>

            <tr>
                <td align="right">通知提醒</td>
                <td align="left">
                    <label class="checkbox-inline"><input name="message" type="checkbox" value="true" checked="checked"> 站内消息</label>
                    <label class="checkbox-inline"><input name="sms" type="checkbox" value="true" checked="checked"> 短信</label>
                </td>
            </tr>

            <tr>
                <td align="right">描述</td>
                <td align="left">{{ueditor('content', $res['content'])}}</td>
            </tr>

            <tr>
                <td align="right"></td>
                <td align="left"><input type="hidden" name="id" value="{{$res['id']}}">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                </td>
            </tr>
        </table>
    </div>

</form>

</div>
