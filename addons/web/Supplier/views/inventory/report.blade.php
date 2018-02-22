<div class="panel">

<div class="wrapper">
    <form id="search-form" class="form-inline" name="search-form" action="{{url()}}" method="get">
        <select class="form-control input-sm" id='category_id' name='category_id' data-toggle="redirect" rel="{{url('report',$query)}}">
            <option value="">产品类别</option>
            @if(count($categorys))
            @foreach($categorys as $k => $v)
            <option value="{{$v['id']}}" @if($query['category_id'] == $v['id']) selected @endif>{{$v['layer_space']}}{{$v['name']}}</option>
            @endforeach
            @endif
        </select>
        <select class="form-control input-sm" id='product_id' name='product_id' data-toggle="redirect" rel="{{url('report',$query)}}">
            <option value="">全部产品</option>
            @if(count($products))
            @foreach($products as $k => $v)
            <option value="{{$v['id']}}" @if($query['product_id'] == $v['id']) selected @endif>{{$v['name']}} @if($v['spec'])- {{$v['spec']}} @endif</option>
            @endforeach
            @endif
        </select>
        &nbsp;
        日期
        <input type="text" name="sdate" class="form-control input-sm" data-toggle="date" id="sdate" value="{{$query['sdate']}}" readonly>
        -
        <input type="text" name="edate" class="form-control input-sm" data-toggle="date" id="edate" value="{{$query['edate']}}" readonly>
        <button type="submit" class="btn btn-default btn-sm">过滤</button>
    </form>
</div>

<div class="table-responsive">
<table class="table table-hover">
    <tr>
        <th align="left">产品名称</th>
        <th align="left">产品类别</th>
        <th align="right">上期结存</th>
        <th align="right">本期入库</th>
        <th align="right">本期出库</th>
        <th align="right">期末结存</th>
	</tr>
    @if(count($rows))
    @foreach($rows as $product_id => $v)
    <tr>
	    <td align="left">({{$product_id}}){{$v['name']}} @if($v['spec']) - {{$v['spec']}} @endif</span></td>
	    <td align="left">{{$categorys[$v['category_id']]['text']}}</td>
	    <td align="right">{{$balance['a'][$product_id]}}</td>
	    <td align="right">{{$balance['b'][$product_id]}}</td>
	    <td align="right">{{$balance['c'][$product_id]}}</td>
	    <td align="right">{{$balance['d'][$product_id]}}</td>
    </tr>
   @endforeach
   @endif
    <tr>
        <td align="left">合计</td>
        <td align="right"></td>
        <td align="right">{{array_sum($balance['a'])}}</td>
        <td align="right">{{array_sum($balance['b'])}}</td>
        <td align="right">{{array_sum($balance['c'])}}</td>
        <td align="right">{{array_sum($balance['d'])}}</td>
    </tr>
</table>
</div>

</div>