<div class="panel">

    @include('tabs')

    <div class="wrapper">
        @include('settlement/query')
    </div>

    <form method="post" id="myform" name="myform">

    <div class="table-responsive">

        <table class="table m-b-none">
            <tr>
                <th>
                    <label class="i-checks i-checks-sm m-b-none">
                        <input class="select-all" type="checkbox"><i></i>
                    </label>
                </th>
                <th align="left">单号</th>
                <th align="left">供应商</th>
                <th align="center">开始日期</th>
                <th align="center">结束日期</th>
                <th align="center">创建者</th>
                <th align="center">创建时间</th>
                <th>流程</th>
                <th align="center"></th>
            </tr>
            @if(count($rows))
            @foreach($rows as $row)

            <?php
                $step = get_step_status($row);
            ?>

            <tr>
                <td align="center">
                    <label class="i-checks i-checks-sm m-b-none">
                        <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}"><i></i>
                    </label>
                </td>
                <td align="left">
                    <a href="{{url('show', ['id' => $row->id])}}">{{$row->sn}}</a>
                </td>

                <td align="left">
                    {{$row->supplier->user->nickname}}
                </td>
                <td align="center">
                    {{$row->start_at}}
                </td>
                <td align="center">
                    {{$row->end_at}}
                </td>

                <td align="center">{{get_user($row->created_by,'nickname')}}</td>
                <td align="center">@datetime($row->created_at)</td>
                <td align="center">
                    <span class="@if($step['edit'])bg-danger @endif badge">{{$row->step->number}}</span> {{$row->step->name}}
                </td>
                <td align="center">
                    <a class="option" href="{{url('show',['id'=>$row['id']])}}"> 查看 </a>
                </td>
            </tr>
            @endforeach
            @endif

        </table>
    </div>
    </form>

    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-1 hidden-xs">
            </div>
            <div class="col-sm-11 text-right text-center-xs">
                {{$rows->render()}}
            </div>
        </div>
    </div>
</div>