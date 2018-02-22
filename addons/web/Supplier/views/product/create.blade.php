<div class="panel">

<form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">
<div class="table-responsive">

<table class="table table-form">
<tr>
    <td align="right" width="10%">商品仓库</td>
    <td align="left" width="40%">
      <select class="form-control input-inline input-sm" name="warehouse_id">
        @if(count($warehouses)) 
        @foreach($warehouses as $warehouse)
         <option value="{{$warehouse->id}}" @if($warehouse->id == $product->warehouse_id) selected @endif>{{$warehouse->layer_space}}{{$warehouse->title}}</option>
        @endforeach
        @endif
      </select>
    </td>
    <td align="right" width="10%">商品类别</td>
    <td align="left" width="40%">
      <select class="form-control input-inline input-sm" name="category_id">
        <option value="0"> - </option>
        @if(count($categorys))
        @foreach($categorys as $category)
         <option value="{{$category->id}}" @if($category->id == $product->category_id) selected @endif>{{$category->layer_space}}{{$category->name}}</option>
        @endforeach 
        @endif
      </select>
    </td>
</tr>

<tr>
    <td align="right">商品名称</td>
    <td align="left">
    <input type="text" class="form-control input-sm" id="name" onblur="app.pinyin('name','code', 'all');" name="name" value="{{$product->name}}">
    </td>

    <td align="right">商品规格</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="spec" value="{{$product->spec}}">
    </td>
</tr>

<tr>
    <td align="right">商品条码</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="barcode" value="{{$product->barcode}}">
    </td>

    <td align="right">商品重量</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="weight" value="{{$product->weight}}">
    </td>
</tr>

<!--
<tr>
    <td align="right">价格1</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="price1" value="{{$product->price1}}">
    </td>
    <td align="right">价格2</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="price2" value="{{$product->price2}}">
    </td>
</tr>
-->

<tr>
    <td align="right">存货代码</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="stock_code" value="{{$product->stock_code}}">
    </td>

    <td align="right">存货编码</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="stock_number" value="{{$product->stock_number}}">
    </td>
</tr>

<tr>
    <td align="right">外箱尺寸</td>
    <td align="left">
        <input type="text" class="form-control input-sm" name="carton_size" value="{{$product->carton_size}}">
    </td>
    <td align="right">外箱条码</td>
    <td align="left">
        <input type="text" class="form-control input-sm" name="carton_barcode" value="{{$product->carton_barcode}}">
    </td>
</tr>

<tr>
    <td align="right">助记码</td>
    <td align="left">
        <input type="text" class="form-control input-sm" id="code" name="code" value="{{$product->code}}">
    </td>
    <td align="right">安全库存</td>
    <td align="left">
        <input type="text" class="form-control input-sm" name="stock_amount" value="{{$product->stock_amount}}">
    </td>
</tr>

<tr>
    <td align="right">供应商</td>
    <td align="left">
        {{Dialog::user('supplier','supplier_id', old('supplier_id', $suppliers), 1, 0)}}
    </td>
    <td align="right">商品图片</td>
    <td align="left">
        <input type="file" name="image" id="image">
    </td>
</tr>

<tr>
    <td align="right">商品状态</td>
    <td align="left">
        <select class="form-control input-inline input-sm" id='status' name='status'>
              <option value='1' @if($product->status == '1') selected @endif>启用</option>
              <option value='0' @if($product->status == '0') selected @endif>停用</option>
        </select>
    </td>
    <td align="right">商品单位</td>
    <td align="left">
    <select class="form-control input-inline input-sm" id='unit' name='unit'>
        @foreach(option('goods.unit') as $unit)
            <option value='{{$unit['id']}}' @if($product->unit == $unit['id']) selected @endif>{{$unit['name']}}</option>
        @endforeach
    </select>
    </td>
</tr>

<tr>
    <td align="right">商品排序</td>
    <td align="left">
    <input type="text" class="form-control input-sm" name="sort" value="{{$product->sort}}">
    </td>
    <td align="right">商品描述</td>
    <td align="left">
        <textarea class="form-control" rows="2" type="text" name="description" id="description">{{$product->description}}</textarea>
    </td>
</tr>

<tr>
    <td align="left" colspan="4">
        <input type="hidden" id="id" name="id" value="{{$product->id}}">
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
    </td>
</tr>

</table>

</div>

</form>

</div>