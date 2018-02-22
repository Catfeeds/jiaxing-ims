<div class="panel">

    <div class="wrapper">
        @include('task/query')
    </div>

    <div class="table-responsive">
        <table class="table table-hover b-t">
            <thead>
                <tr>
                    <th width="60">编号</th>
                    <th align="left">标题</th>
                    <th width="100">创建者</th>
                    <th width="140">下达时间</th>
                    <th width="140">限制时间</th>
                    <th width="80">状态</th>
                    <th width="100">操作</th>
                </tr>
            </thead>

            @if($rows)
            @foreach($rows as $k => $v)

            <tr>
                <td align="center">{{$v['id']}}</td>
                <td align="left"><a href="{{url('comment', ['id' => $v['id']])}}">{{$v['title']}}</a></td>
                <td align="center">{{get_user($v['add_user_id'], 'nickname')}}</td>
                <td align="center">@datetime($v['add_time'])</td>
                <td align="center">@datetime($v['end_time'])</td>

                <td align="center">
                    @if($v->comments[0]['status'] == 1)
                        <span class="label label-success">完成</span>
                    @else
                        @if($v['end_time'] < time())
                            <a data-toggle='tooltip' class="label label-danger" title="{{remaining_time($v['end_time'], time())}}"><i class="fa fa-clock-o"></i> 过期</a>
                        @else
                            <a data-toggle='tooltip' class="label label-info" title="{{remaining_time($v['end_time'], time())}}"><i class="fa fa-clock-o"></i> 正常</a>
                        @endif

                    @endif
                </td>

                <td align="center">

                    @if($v['count']['task'] == null)
                        <a class="option" href="{{url('comment')}}?id={{$v['id']}}">提交任务</a>
                    @elseif($v['count']['task'] == 0)
                        已提交
                    @elseif($v['count']['task'] == 1)
                        已完成
                    @elseif($v['count']['task'] == 2)
                        已驳回
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
