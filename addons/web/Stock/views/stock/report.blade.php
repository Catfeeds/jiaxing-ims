<div class="panel">

  <div class="wrapper">

    <div class="pull-right">
        <a href="{{url('product/yonyou/sync', ['ym' => date('Ym', strtotime($selects['sdate']))])}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步({{date("Y-m", strtotime($selects['sdate']))}})用友数据</a>
        <a href="{{url('product/yonyou/sync')}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步(2015-12-31)前用友数据</a> 
    </div>

		<form class="form-inline" id="search-form" name="mysearch" action="{{url()}}" method="get">
          <select class="form-control input-sm" id='warehouse_id' name='warehouse_id' data-toggle="redirect" rel="{{url('report',$selects)}}">
              <option value="">产品仓库</option>
                 @if(count($warehouse)) @foreach($warehouse as $k => $v)
                    <option value="{{$v['id']}}" @if($selects['warehouse_id'] == $v['id']) selected="true" @endif >{{$v['layer_space']}}{{$v['title']}}</option>
                 @endforeach @endif
            </select>

            <select class="form-control input-sm" id='type_id' name='type_id' data-toggle="redirect" rel="{{url('report',$selects)}}">
              <option value="">库存类型</option>
                 @if(count($types)) @foreach($types as $k => $v)
                    <option value="{{$v['id']}}" @if($selects['type_id']==$v['id']) selected="true" @endif >{{$v['title']}}</option>
                 @endforeach @endif
            </select>

            <select class="form-control input-sm" id='category_id' name='category_id' data-toggle="redirect" rel="{{url('report',$selects)}}">
              <option value="">产品类别</option>
                 @if(count($product_category)) @foreach($product_category as $k => $v)
                    <option value="{{$v['id']}}" @if($selects['category_id']==$v['id']) selected="true" @endif >{{$v['layer_space']}}{{$v['name']}}</option>
                 @endforeach @endif
            </select>

            <!--
            <select class="form-control input-sm" id='product_id' name='product_id' data-toggle="redirect" rel="{{url('report',$selects)}}">
              <option value="">全部产品</option>
                 @if(count($products)) @foreach($products as $k => $v)
                    <option value="{{$v['id']}}" @if($selects['product_id']==$v['id']) selected="true" @endif >{{$v['name']}} @if($v['spec'])  - {{$v['spec']}} @endif </option>
                 @endforeach @endif
            </select>
            -->

            &nbsp;
            日期
          <input type="text" name="sdate" class="form-control input-sm" data-toggle="date" size="13" id="sdate" value="{{$selects['sdate']}}" readonly />
           -
          <input type="text" name="edate" class="form-control input-sm" data-toggle="date" size="13" id="edate" value="{{$selects['edate']}}" readonly />
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
    <th>用友上期结存</th>
    <th>用友本期入库</th>
    <th>用友本期出库</th>
    <th>用友期末结存</th>
	</tr>
   @if(count($rows)) 
   @foreach($rows as $v)
  <tr>
      <td align="center">{{$v['stock_number']}}</td>
      <td align="center">{{$product_category[$v['category_id']]['name']}}</td>
	    <td align="left">{{$v['name']}} @if($v['spec']) - {{$v['spec']}} @endif</td>
	    <td align="right">{{$balance['a'][$v['id']]}}</td>
	    <td align="right">{{$balance['b'][$v['id']]}}</td>
	    <td align="right">{{$balance['c'][$v['id']]}}</td>
	    <td align="right">{{$balance['d'][$v['id']]}}</td>
      <td align="right">{{$yonyou_history[$v['stock_number']]}}</td>
	    <td align="right">{{$yonyou_data[$v['stock_number']]['quantity_a']}}</td>
	    <td align="right">{{$yonyou_data[$v['stock_number']]['quantity_b']}}</td>
	    <td align="right" title="{{$v['stock_amount']}}">
        <?php 
        $quantity_end  = $yonyou_data[$v['stock_number']]['quantity'] + $yonyou_history[$v['stock_number']];
        $yonyou_total_2 += $yonyou_data[$v['stock_number']]['quantity_a'];
        $yonyou_total_3 += $yonyou_data[$v['stock_number']]['quantity_b'];
        $yonyou_total_4 += $quantity_end;

        ?>
        @if($v['stock_amount'] > $quantity_end)
          <span class="red">{{$quantity_end}}</span>
        @else
          {{$quantity_end}}
        @endif
      </td>
  </tr>
   @endforeach @endif
   
    <tr>
    <th align="left">合计</th>
    <th align="right"></th>
    <th align="right"></th>
    <th align="right">{{array_sum($balance['a'])}}</th>
    <th align="right">{{array_sum($balance['b'])}}</th>
    <th align="right">{{array_sum($balance['c'])}}</th>
    <th align="right">{{array_sum($balance['d'])}}</th>
    <th align="right">{{array_sum((array)$yonyou_history)}}</th>
    <th align="right">{{$yonyou_total_2}}</th>
    <th align="right">{{$yonyou_total_3}}</th>
    <th align="right">{{$yonyou_total_4}}</th>
  </tr>
</table>

</div>
</div>