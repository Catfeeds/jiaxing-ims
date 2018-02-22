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
                    @include('attachment/view')
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">

    <div class="panel-heading text-base">
        <i class="fa fa-list-alt"></i> 任务执行
    </div>

    <div class="table-responsive">
        <table class="table">
            <tr>
                <th width="120">执行人</th>
                <th align="left">执行正文</th>
                <th width="200" align="left">审核意见</th>
                <th width="130">执行时间</th>
                <th width="130">审核时间</th>
                <th width="80">任务评分</th>
                <th width="60">状态</th>
                <th width="60"></th>
            </tr>

            @if(count($scope['user']['id']))
            @foreach($scope['user']['id'] as $userId)

            {{:$reply = $replys[$userId]}}
            <tr>
                <td align="center"><a href="#">{{$scope['user']['name'][$userId]}}</a></td>
                <td align="left">

                    {{$reply['content']}}

                    {{'';$attachList['view'] = $reply['attachment']}}
                    @include('attachment/view')

                </td>
                <td align="left">{{nl2br($reply['reject'])}}</td>
                <td align="left">@datetime($reply['add_time'])</td>
                <td align="center">@datetime($reply['audit_time'])</td>

                <td align="center">
                     @if($reply['id'] > 0)
                        @if($reply['vote'] > 0)
                            <strong style="color:green;">{{$reply['vote']}}</strong>
                        @else
                            未评分
                        @endif
                     @endif
                </td>

                <td align="center">{{option('task.status', $reply['status'])}}</td>
                <td align="center">
                    @if($reply['id'] > 0)
                        <a class="btn btn-xs btn-info" href="javascript:;" onclick='iframeBox("任务审核","{{url('audit')}}?id={{$reply['id']}}");'>审核</a>
                    @endif
                </td>
            </tr>
             @endforeach
             @endif
        </table>
    </div>
    
</div>

<!--

<div class="panel">

    <div class="panel-heading b-b b-light">
        <h3 class="m-xs m-l-none">
            {{$instruct['title']}}
        </h3>
        <small class="text-muted">
            创建人: {{$instruct['nickname']}}
            &nbsp;
            开始时间: @datetime($instruct['add_time'])
            &nbsp;
            结束时间: @datetime($instruct['end_time'])
        </small>
    </div>

    <div class="panel-body">
        {{$instruct['content']}}
        @include('attachment/view')
    </div>
    

    <div class="padder">
        <div class="table-responsive">
        <table class="table b-a">
            <tr>
                <th width="120">执行人</th>
                <th align="left">回复正文</th>
                <th width="200" align="left">审核意见</th>
                <th width="130">回复时间</th>
                <th width="130">审核时间</th>
                <th width="80">任务评分</th>
                <th width="60">状态</th>
                <th width="60"></th>
            </tr>

            @if(count($scope['user']['id']))
            @foreach($scope['user']['id'] as $userId)
            {{:$reply = $replys[$userId]}}
            <tr>
                <th align="center"><a href="#">{{$scope['user']['name'][$userId]}}</a></th>
                <td align="left">

                    {{$reply['content']}}

                    {{'';$attachList['view'] = $reply['attachment']}}
                    @include('attachment/view')

                </td>
                <td align="left">{{nl2br($reply['reject'])}}</td>
                <td align="left">@datetime($reply['add_time'])</td>
                <td align="center">@datetime($reply['audit_time'])</td>

                <td align="center">
                     @if($reply['id'] > 0)
                        @if($reply['vote'] > 0)
                            <strong style="color:green;">{{$reply['vote']}}</strong>
                        @else
                            未评分
                        @endif
                     @endif
                </td>

                <th align="center">{{option('task.status', $reply['status'])}}</th>
                <td align="center">
                    @if($reply['id'] > 0)
                        <a class="btn btn-xs btn-info" href="javascript:;" onclick='iframeBox("任务审核","{{url('audit')}}?id={{$reply['id']}}");'>审核</a>
                    @endif
                </td>
            </tr>
             @endforeach
             @endif
        </table>
        </div>
    </div>
-->

<div class="panel">
    <div class="panel-body">
    @if($instruct['add_user_id'] == Auth::id())
        <input type="hidden" name="id" id="id" value="{{$instruct['id']}}" />
        <input type="hidden" name="submit" id="submit" value="true" />
        <!--
        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
        -->
    @endif
    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
    </div>
</div>