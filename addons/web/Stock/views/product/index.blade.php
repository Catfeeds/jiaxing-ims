<div class="panel">

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
            <li class="@if($query['status'] == 1) active @endif">
                <a class="text-sm" href="{{url('',['status' => 1, 'advanced' => $query['advanced']])}}">正常</a>
            </li>
            <li class="@if($query['status'] == 0) active @endif">
                <a class="text-sm" href="{{url('',['status' => 0, 'advanced' => $query['advanced']])}}">禁用</a>
            </li>
        </ul>
    </div>

    <div class="wrapper">
        @include('product/query')
    </div>

<form method="post" id="myform" name="myform">
<div class="table-responsive">
<table class="table m-b-none table-hover">
    <tr>
    <th align="center">
        <input class="select-all" type="checkbox">
    </th>
    <th align="left">名称</th>
    <th align="left">规格</th>
    <th align="left">条码 </th>
    <th align="left">编码</th>
    <th>单位</th>
    <th align="right">销售价</th>
    <th align="center">排序</th>
    <th align="center">ID</th>
    <th align="center"></th>
	</tr>
    @if(count($rows)) @foreach($rows as $v)

  <tr>
    <td align="center">
        <input class="select-row" type="checkbox" name="id[]" value="{{$v['id']}}">
    </td>
    <!--
    <td align="center">
        {{goodsImage($v)}}
    </td>
    -->
    <td align="left">
        {{$v['name']}}
        <div>{{$v['spec']}}</div>
        <div>{{$v['barcode']}}</div>
    </td>
    <td align="left">
        {{$v['stock_code']}}
        <div>{{$v['stock_number']}}</div>
    </td>
    <td align="left">
        {{$v['stock_code']}}
        <div>{{$v['stock_number']}}</div>
    </td>
    <td align="center">{{option('goods.unit', $v['unit'])}}</td>
    <td align="right">{{$v['price1']}}</td>
    <td align="right">{{$v['price4']}}</td>
    <td align="right">{{$v['price2']}}</td>
    <td align="right">{{$v['price3']}}</td>
    <td align="right">{{$v['level_amount']}}</td>
    <td align="right">{{$v['weight']}}</td>
    <td align="center">
        <input type="text" class="form-control input-sort" name="id[{{$v['id']}}]" value="{{$v['sort']}}" />
    </td>
    <td align="center">{{$v['id']}}</td>
    <td align="center">
      <a class="option" href="{{url('add')}}?id={{$v['id']}}"> 编辑 </a>
    </td>
  </tr>
   @endforeach @endif
</table>
</div>

</form>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-1 hidden-xs">
            <button type="button" onclick="optionSort('#myform','{{URL::full()}}');" class="btn btn-default btn-sm"><i class="icon icon-sort-by-order"></i> 排序</button>
        </div>
        <div class="col-sm-11 text-right text-center-xs">
            {{$rows->render()}}
        </div>
      </div>
    </footer>
</div>