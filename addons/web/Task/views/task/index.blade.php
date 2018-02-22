<div class="panel">

    @include('tabs')

    <div class="wrapper">
        @include('task/query')
    </div>

    <div class="table-responsive">
        <table class="table table-hover b-t">
        <thead>
            <tr>
                <th align="left">主题</th>
                <th width="140">开始时间</th>
                <th width="140">结束时间</th>
                <th width="160">状态</th>
                <th width="60">ID</th>
                <th width="140"></th>
            </tr>
        </thead>
    @if($rows)
    @foreach($rows as $k => $v)
    <tr>
        <td align="left">
         @if($search['query']['type'] == 'created')
            <a href="{{url('view',['id'=>$v['id']])}}">{{$v['title']}}</a>
        @else
            <a href="{{url('comment',['id'=>$v['id']])}}">{{$v['title']}}</a>
        @endif
        </td>

        <td align="center">@datetime($v['add_time'])</td>

        <td align="center">@datetime($v['end_time'])</td>

        <td align="center">

            @if($v['count']['all'] == $v['count'][1])
                <span class="label label-info">完毕</span>
            @else
                @if($v['end_time'] < time())
                    <a data-toggle='tooltip' class="label label-danger" title="{{remaining_time($v['end_time'], time())}}"><i class="fa fa-clock-o"></i> 过期</a>
                @else
                    <a data-toggle='tooltip' class="label label-info" title="{{remaining_time($v['end_time'], time())}}"><i class="fa fa-clock-o"></i> 正常</a>
                @endif
            @endif

            ({{$v['count']['all']}}/<strong class="red" title="退回任务">{{(int)$v['count'][2]}}</strong>/<strong class="green" title="完成任务">{{(int)$v['count'][1]}}</strong>)
        </td>

        <td align="center">{{$v['id']}}</td>
        
        <td align="center">

            @if($search['query']['type'] == 'created')

                <a class="option" href="{{url('view',['id'=>$v['id']])}}">查看</a>
                <a class="option" href="{{url('add',['id'=>$v['id']])}}">编辑</a>
                <a class="option" onclick="app.confirm('{{url('delete',['id'=>$v['id']])}}','确定要删除吗？');" href="javascript:;">删除</a>
            
            @else

                @if($v['count']['task'] == null)
                    <a class="option" href="{{url('comment')}}?id={{$v['id']}}">提交任务</a>
                @elseif($v['count']['task'] == 0)
                    已提交
                @elseif($v['count']['task'] == 1)
                    已完成
                @elseif($v['count']['task'] == 2)
                    已驳回
                @endif

            @endif
        </td>
    </tr>
    @endforeach
    @endif
    </table>
    
</div>

    <footer class="panel-footer">
        <div class="text-right text-center-xs">
            {{$rows->render()}}
        </div>
    </footer>
</div>
