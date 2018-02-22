<div class="panel">

  <div class="wrapper">
      @include('budget/query')
  </div>

  <form method="post" id="myform" name="myform">
    <div class="table-responsive">
    <table class="table b-t m-b-none table-hover">
    <thead>
    <tr>
    <th align="center">
        <input class="select-all" type="checkbox">
    </th>
    <th align="left">单据单号</th>
    <th align="center">日期</th>
    <th align="right">数量</th>
    <th>创建者</th>
    <th>创建时间</th>
    <th></th>
	</tr>
</thead>

  @foreach($rows as $v)
  <tr>
    <td align="center">
        <input class="select-row" type="checkbox" name="id[]" value="{{$v['id']}}">
    </td>
    <td align="left">{{$v['sn']}}</td>
    <td align="center">{{$v['date']}}</td>
    <td align="right">{{$v['quantity']}}</td>
    <td align="center">{{$v['created_by']}}</td>
    <td align="center">@datetime($v['created_at'])</td>
    <td align="center">
      <a class="option" href="javascript:;" onclick='viewBox("","明细 [ {{$v['sn']}} ]","{{url('show',['id'=>$v['id'],'type'=>$v['type']])}}");'>查看</a>
    </td>
  </tr>
  @endforeach 
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