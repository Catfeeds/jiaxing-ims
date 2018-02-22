<div class="panel">

    <div class="wrapper">
        @include('receivable/query')
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
        <table class="table m-b-none">
        <tr>
            <th align="center">
                <label class="i-checks i-checks-sm m-b-none">
                    <input class="select-all" type="checkbox"><i></i>
                </label>
            </th>
            <th align="center" width="80">客户代码</th>
            <th align="left">客户名称</th>
            <th align="center">回款日期</th>
            <th align="right">回款金额</th>
            <th align="center" width="200"></th>
        </tr>

        @foreach($rows as $row)
        <tr>
            <td align="center">
                <label class="i-checks i-checks-sm m-b-none">
                    <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}"><i></i>
                </label>
            </td>
            <td align="center">{{$row->customer->user->username}}</td>
            <td align="left">{{$row->customer->user->nickname}}</td>
            <td align="center">@date($row->pay_date)</td>
            <td align="right">{{$row->pay_money}}</td>
            <td align="center">
                <a class="option" href="javascript:viewBox('show','查看','{{url('show', ['id'=>$row->id])}}');">查看</a>
                <a class="option" href="javascript:formBox('编辑','{{url('create', ['id'=>$row->id])}}','window-form');">编辑</a>
            </td>
        </tr>
        @endforeach
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
