<div class="panel">

    <div class="wrapper">
        @include('inventory/query')
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
                <th align="left">单号</th>
                <th align="center">供应商</th>
                <th align="right">现存数量</th>
                <th align="right">生产计划数量</th>
                <th align="center">创建者</th>
                <th align="center">创建时间</th>
                <th align="center"></th>
            </tr>
            @if(count($rows))
            @foreach($rows as $row)

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
                <td align="right">{{$row->datas->sum('quantity')}}</td>
                <td align="right">{{$row->datas->sum('plan')}}</td>
                <td align="center">{{get_user($row->created_by,'nickname')}}</td>
                <td align="center">@datetime($row->created_at)</td>
                <td align="center">
                    @if($edit)
                        <a class="option" href="{{url('edit',['id'=>$row['id']])}}"> 编辑 </a>
                    @else
                        <a class="option" href="{{url('show',['id'=>$row['id']])}}"> 查看 </a>
                    @endif
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