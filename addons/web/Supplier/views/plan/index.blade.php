<div class="panel">

    @include('tabs')

    <div class="wrapper">
        @include('plan/query')
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
                <th align="left">订单号</th>
                <th align="center">供应商</th>
                <th align="center">周期计划阶段</th>
                <th align="right">周期订单汇总数量</th>
                <th align="center">创建者</th>
                <th align="center">创建时间</th>
                <th align="center">状态</th>
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
                    <a href="{{url('show', ['id' => $row->id])}}">{{$row->number}}</a>
                </td>
                
                <td align="center">{{$row->supplier->user->nickname}}</td>
                <td align="center">{{$row->datas[0]->cycle}}</td>
                <td align="right">{{$row->datas->sum('quantity')}}</td>
                <td align="center">{{get_user($row->created_by,'nickname')}}</td>
                <td align="center">@datetime($row->created_at)</td>
                <td align="center"><span class="label label-{{$status[$row->status]['color']}}">{{$status[$row->status]['name']}}</span></td>
                <td align="center">
                    <span class="@if($step['edit'])bg-danger @endif badge">{{$row->step->number}}</span> {{$row->step->name}}
                </td>
                <td align="center">
                    <a class="option" href="{{url('show',['id'=>$row['id']])}}"> 查看 </a>
                    <a class="option" onclick="formBox('短信提醒', app.url('index/api/sms',{user_id:{{$row->supplier->user->id}}}),'user-sms');">短信提醒</a>
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