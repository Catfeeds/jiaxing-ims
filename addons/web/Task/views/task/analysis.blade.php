<div class="panel">

    <div class="wrapper">
        @include('task/query')
    </div>

    <div class="table-responsive">
        <table class="table b-t table-hover">
            <thead>
            <tr>
                <th align="left">账户姓名</th>
                <th align="right" width="160">任务数</th>
                <th align="right" width="120">任务平均分</th>
            </tr>
            </thead>
            <tbody>
            @if(count($rows))
            @foreach($rows as $k => $v)
            <tr>
                <td align="left">{{$v['nickname']}}</td>
                <td align="right">{{$v['count']}}</td>
                <td align="right">{{$v['avg']}}</td>
            </tr>
            @endforeach
            @endif
            </tbody>
        </table>

    </div>

    <footer class="panel-footer">
        <div class="text-right text-center-xs">
            {{$rows->render()}}
        </div>
    </footer>
</div>