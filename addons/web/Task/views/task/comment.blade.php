<div class="panel">

    <div class="panel-heading text-base">
        <i class="fa fa-file-text"></i> 任务详情
    </div>

    <div class="table-responsive">
        <table class="table">
            <tr>
                <td align="right">任务主题</td>
                <td>{{$instruct['title']}}</td>

                <td align="right">创建人</td>
                <td>{{$instruct['nickname']}}</td>
            </tr>
            <tr>
                <td width="15%" align="right">开始时间</td>
                <td width="35%">@datetime($instruct['add_time'])</td>

                <td width="15%" align="right">结束时间</td>
                <td width="35%">
                    @datetime($instruct['end_time'])
                </td>
            </tr>
            <tr>
                <td width="15%" align="right">任务正文</td>
                <td colspan="3">
                    {{$instruct['content']}}
                    {{'';$attachList['view'] = $attachList['queue']}}
                    @include('attachment/view')
                </td>
            </tr>
        </table>
    </div>
</div>

@if(isset($reply['status']))

<div class="panel">

    <div class="panel-heading text-base">
        <i class="fa fa-check-square"></i> 任务审核
    </div>
    
    <div class="table-responsive">
    <table class="table">
        <tr>
            <td width="15%" align="right">审核时间</td>
            <td width="35%">@datetime($reply['audit_time'])</td>

            <td width="15%" align="right">任务评分</td>
            <td width="35%">
                @if($reply['vote']>0) <strong style="color:green;">{{$reply['vote']}}</strong> @else 未评分 @endif
            </td>
        </tr>
        <tr>
            <td width="15%" align="right">审核意见</td>
            <td width="35%">{{nl2br($reply['reject'])}}</td>

            <td width="15%" align="right">状态</td>
            <td width="35%">
                <span style="color:@if($reply['status'] == 2) red @else green @endif ;">{{option('task.status', $reply['status'])}}</span>
            </td>
        </tr>
    </table>
    </div>
</div>

@endif

<div class="panel">

    <div class="panel-heading text-base">
        <i class="fa fa-comments"></i> 回复任务
    </div>
    
    <form method="post" action="{{url()}}" id="myform" name="myform">

        <table class="table">

            @if(empty($reply) || $reply['status'] == 2)
            <tr>
                <td align="left">
                    {{'';$attachList['queue'] = $attachList['reply']}}
                    @include('attachment/add')
                </td>
            </tr>

            <tr>
                <td align="left">
                    <label class="checkbox-inline"><input name="sms" type="checkbox" value="true" checked="checked"> 短信提醒</label>
                </td>
            </tr>

            <td align="left">
                {{ueditor('content', $reply['content'])}}
            </td>

            @elseif($reply['status'] == 0 || $reply['status'] == 1)

            <td width="15%" align="right">回复时间</td>
            <td>@datetime($reply['add_time'])</td>

            <tr>
                <td width="15%" align="right">回复正文</td>
                <td align="left">
                    {{$reply['content']}}
                    {{'';$attachList['view'] = $attachList['reply']}}
                    @include('attachment/view')
                </td>
            </tr>

            @endif

        </table>

        <div class="panel-footer">
        @if(empty($reply) || $reply['status'] == 2)
            <input type="hidden" name="id" id="id" value="{{$reply['id']}}">
            <input type="hidden" name="instruct_id" id="instruct_id" value="{{$instruct['id']}}">
            <input type="hidden" name="status" id="status" value="0">
            <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
        @endif
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        </div>

    </form>
    
</div>