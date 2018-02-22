<div class="panel">

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
            @foreach($options as $k => $v)
                <li class="@if($search['query']['option'] == $k) active @endif">
                    <a class="text-sm" href="{{url('index',['option'=>$k])}}">{{$v}}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="wrapper">

        <form id="search-form" class="form-inline" name="mysearch" action="{{url()}}" method="get">

            <div class="pull-right">

                @if(isset($access['delete']))
                    <a class="btn btn-sm btn-danger" href="javascript:optionDelete('#myform','{{url('delete',['status'=>'0'])}}','确定将流程放入回收站？');"><i class="icon icon-remove"></i> 删除</a>
                @endif

                @if(isset($access['trash']))
                    <a href="{{url('trash')}}" class="btn btn-sm btn-default"><i class="icon icon-trash"></i> 回收站</a>
                @endif

            </div>

            @if(isset($access['list']))
                <a href="{{url('list')}}" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a>
            @endif

            @include('workflow/select')
            
        </form>
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
        <table class="table m-b-none table-striped b-t table-hover">
            <thead>
            <tr>
                <th align="center">
                    <input class="select-all" type="checkbox">
                </th>
                <th align="left">主题 / 文号</th>
                <th>发起人</th>
                <th align="left">步骤</th>
                <th>状态</th>
                <th align="center">ID</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @if(count($rows))
            @foreach($rows as $row)
            <tr>
                <td align="center">
                    @if(isset($access['delete']) && ($row['start_user_id'] == Auth::id() || $access['delete'] == 4))
                        <input class="select-row" type="checkbox" name="id[]" value="{{$row['id']}}">
                    @else
                        <input type="checkbox" disabled>
                    @endif
                </td>
                <td>
                    @if($search['query']['option'] == 'todo')
                        <a href="{{url('edit')}}?process_id={{$row['id']}}">{{$row['title']}}</a>
                    @else
                        <a href="{{url('view')}}?process_id={{$row['id']}}">{{$row['title']}}</a>
                    @endif
                    <div class="text-muted">{{$row['name']}}</div>
                </td>
                <td align="center">{{get_user($row['start_user_id'], 'nickname')}}</td>
                <td>
                    <span class="badge">{{$row['step']['number']}}</span>
                    <a href="javascript:viewBox('process-log','流程记录','{{url('log', ['process_id' => $row['id']])}}');">{{$row['step']['name']}}</a>

                    <div class="">
                    <?php /*
                        if ($row['step']['timeout'])
                        {
                            $timeout = 6 * 60;
                            $start = Carbon\Carbon::createFromTimeStamp($row['step']['add_time']);
                            echo Carbon\Carbon::now()->diffInHours($start);
                        } */
                    ?>
                    </div>
                </td>
                <td align="center">
                     @if($row['end_time'])
                        <span class="label label-info">已结束</span>
                     @else
                        <span class="label label-success">执行中</span>
                     @endif
                </td>
                <td align="center">{{$row['id']}}</td>
                <td align="center">

                    @if($search['query']['option'] == 'todo')
                        <a class="option" href="{{url('edit',['process_id'=>$row['id']])}}">办理</a>
                    @else
                        <a class="option" href="{{url('view',['process_id'=>$row['id']])}}">查看</a>
                    @endif

                </td>
            </tr>
             @endforeach
             @endif
            </tbody>
        </table>
    </div>
    </form>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-1 hidden-xs">
        </div>
        <div class="col-sm-11 text-right text-center-xs">
            {{$rows->render()}}
        </div>
      </div>
    </footer>
</div>
