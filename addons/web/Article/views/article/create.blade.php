<form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">

<div class="panel">
<div class="table-responsive">
<table class="table table-form">
    <tr>
        <td width="10%" align="right">公告主题</td>
        <td width="90%" align="left">
            <input class="form-control input-sm" type="text" id="title" name="title" value="{{$row->title}}">
        </td>
    </tr>
    
    <tr>
        <td align="right">发布对象</td>
        <td align="left">
            {{Dialog::search($row, 'id=receive_id&name=receive_name&multi=1')}}
        </td>
    </tr>
 
    <tr>
        <td align="right">公告时间</td>
        <td align="left">
            <input data-toggle="datetime" class="form-control input-inline input-sm" id="created_at" type="text" value="@datetime($row->created_at, time())">
            &nbsp;至&nbsp;
            <input placeholder="留空表示一直有效。" data-toggle="datetime" class="form-control input-inline input-sm" name="expired_at" id="expired_at" type="text" value="@datetime($row->expired_at)">
        </td>
    </tr>

    <tr>
        <td align="right">公告类别</td>
        <td align="left">
            <select class="form-control input-inline input-sm" id='category_id' name='category_id'>
                @foreach(option('article.category') as $category)
                    <option value='{{$category['id']}}' @if($row->category_id == $category['id']) selected @endif >{{$category['name']}}</option>
                @endforeach
            </select>
        </td>
    </tr>

    <tr>
        <td align="right">附件列表</td>
        <td align="left">
            @include('attachment/create')
        </td>
    </tr>

    <tr>
        <td align="right">通知提醒</td>
        <td align="left">
            <label class="checkbox-inline i-checks i-checks-sm">
                <input name="notify[message]" type="checkbox" value="1" checked>
                <i></i>消息
            </label>
            <label class="checkbox-inline i-checks i-checks-sm">
                <input name="notify[mail]" type="checkbox" value="1">
                <i></i>邮件
            </label>
            <label class="checkbox-inline i-checks i-checks-sm">
                <input name="notify[sms]" type="checkbox" value="1">
                <i></i>短信
            </label>
        </td>
    </tr>
    <tr>
        <td align="right">描述</td>
        <td align="left">
            <textarea placeholder="100个字符以内。" cols="4" id="description" name="description" class="form-control input-sm">{{$row->description}}</textarea>
        </td>
    </tr>
    <tr>
        <td align="right">内容</td>
        <td align="left">
            {{ueditor('content', $row->content)}}
        </td>
    </tr>

    <tr>
        <td align="left" colspan="2">
            <input type="hidden" name="id" value="{{$row->id}}">
            <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
            <button type="submit" class="btn btn-success btn-large"><i class="fa fa-check-circle"></i> 保存</button>
        </td>
    </tr>

</table>

</div>
</div>
</form>

<script>
ajaxSubmit('#myform');
</script>