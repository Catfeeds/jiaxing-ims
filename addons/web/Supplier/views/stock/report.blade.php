<div class="panel">

  <div class="wrapper">

    <div class="pull-right">
        <a href="{{url('product/yonyou/sync', ['ym' => date('Ym', strtotime($query['sdate']))])}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步({{date("Y-m", strtotime($query['sdate']))}})用友数据</a>
        <a href="{{url('product/yonyou/sync')}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步(2015-12-31)前用友数据</a> 
    </div>

		<form class="form-inline" id="search-form" name="mysearch" action="{{url()}}" method="get">
          
           <select class="form-control input-sm" id='type_id' name='type_id' data-toggle="redirect" rel="{{url('report',$query)}}">
              <option value="">库存类型</option>
                 @if(count($types)) @foreach($types as $k => $v)
                    <option value="{{$v['id']}}" @if($query['type_id']==$v['id']) selected="true" @endif >{{$v['title']}}</option>
                 @endforeach @endif
            </select>
          
          <select class="form-control input-sm" id='warehouse_id' name='warehouse_id' data-toggle="redirect" rel="{{url('report',$query)}}">
              <option value="">产品仓库</option>
                 @if(count($warehouse)) @foreach($warehouse as $k => $v)
                    <option value="{{$v['id']}}" @if($query['warehouse_id'] == $v['id']) selected="true" @endif >{{$v['layer_space']}}{{$v['title']}}</option>
                 @endforeach @endif
            </select>

            <select class="form-control input-sm" id='category_id' name='category_id' data-toggle="redirect" rel="{{url('report',$query)}}">
              <option value="">产品类别</option>
                 @if(count($categorys)) @foreach($categorys as $k => $v)
                    <option value="{{$v['id']}}" @if($query['category_id'] == $v['id']) selected="true" @endif >{{$v['layer_space']}}{{$v['name']}}</option>
                 @endforeach @endif
            </select>

            &nbsp;
            日期
          <input type="text" name="sdate" class="form-control input-sm" data-toggle="date" size="13" id="sdate" value="{{$query['sdate']}}" readonly />
           -
          <input type="text" name="edate" class="form-control input-sm" data-toggle="date" size="13" id="edate" value="{{$query['edate']}}" readonly />
          <button type="submit" class="btn btn-default btn-sm">过滤</button>
      </form>
  </div>

<div class="table-responsive">
<table class="table m-b-none b-t table-hover table-bordered">
    <tr>
    <th align="center">存货编码</th>
    <th align="center">产品类别</th>
    <th align="left">产品名称</th>
    <th>上期结存</th>
    <th>本期入库</th>
    <th>本期出库</th>
    <th>期末结存</th>
	</tr>
   @if(count($rows)) 
   @foreach($rows as $v)
  <tr>
      <td align="center">{{$v['stock_number']}}</td>
      <td align="center">{{$v['category_name']}}</td>
	    <td align="left">@if($v['status']) <span> @else <span class="red"> @endif {{$v['name']}} @if($v['spec'])  - {{$v['spec']}} @endif </span></td>
	    <td align="right">{{$yonyou_history[$v['stock_number']]}}</td>
	    <td align="right">{{$yonyou_data[$v['stock_number']]['quantity_a']}}</td>
	    <td align="right">{{$yonyou_data[$v['stock_number']]['quantity_b']}}</td>
	    <td align="right">{{$yonyou_data[$v['stock_number']]['quantity'] + $yonyou_history[$v['stock_number']]}}</td>
  </tr>
  <?php 
    $yonyou_total_2 += $yonyou_data[$v['stock_number']]['quantity_a'];
    $yonyou_total_3 += $yonyou_data[$v['stock_number']]['quantity_b'];
    $yonyou_total_4 += $yonyou_data[$v['stock_number']]['quantity'] + $yonyou_history[$v['stock_number']];
  ?>
   @endforeach 
   @endif
    <tr>
    <th align="left">合计</th>
    <th align="right"></th>
    <th align="right"></th>
    <th align="right">{{array_sum((array)$yonyou_history)}}</th>
    <th align="right">{{$yonyou_total_2}}</th>
    <th align="right">{{$yonyou_total_3}}</th>
    <th align="right">{{$yonyou_total_4}}</th>
  </tr>
</table>

</div>
</div>