<div class="panel">

    @include('tabs')

    <div class="wrapper">
        @include('supplier/query')
    </div>

    <form method="post" id="myform" name="myform">

    <div class="table-responsive">

        <table class="table table-hover m-b-none">
            <tr>
                <th align="center">
                    <input class="select-all" type="checkbox">
                </th>
                <th align="left">供应商名称</th>
                <th>公司性质</th>
                <th>创建时间</th>
                <th align="center"></th>
            </tr>
            @if(count($rows))
            @foreach($rows as $row)
            <tr>
                <td align="center">
                    <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}">
                </td>
                <td>{{$row->user->nickname}}</td>
                <td align="center">{{$row->nature}}</td>
                <td align="center">@datetime($row->user->created_at)</td>
                <td align="center">
                    <a class="option" href="{{url('show',['id'=>$row['id']])}}"> 查看 </a>
                    @if(isset($access['create']))
                    <a class="option" href="{{url('create',['id'=>$row['id']])}}"> 编辑 </a>
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