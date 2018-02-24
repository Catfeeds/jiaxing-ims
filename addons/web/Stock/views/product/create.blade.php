<div class="wrapper-sm">

<form method="post" class="form-horizontal" action="{{url()}}" id="stock-warehouse-form" name="stock_warehouse_form">

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">名称 <span class="red">*</span></label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="name" value="{{$row['name']}}" />
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">规格</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="spec" value="{{$row['spec']}}" />
    </div>
</div>

<div class="form-group">
    <label for="parent_id" class="col-sm-2 control-label">类别</label>
    <div class="col-sm-10">
        <select class="form-control input-sm" name="category_id" id="category_id">
            <option value=""> - </option>
             @if(count($categorys))
             @foreach($categorys as $category)
                <option value="{{$category['id']}}" @if($row['category_id'] == $category['id']) selected @endif>{{$category['layer_space']}}{{$category['name']}}</option>
             @endforeach 
             @endif
        </select>
    </div>
</div>

<div class="form-group">
    <label for="parent_id" class="col-sm-2 control-label">默认仓库</label>
    <div class="col-sm-10">
        <select class="form-control input-sm" name="warehouse_id" id="warehouse_id">
            <option value=""> - </option>
             @if(count($warehouses))
             @foreach($warehouses as $warehouse)
                <option value="{{$warehouse['id']}}" @if($row['warehouse_id'] == $warehouse['id']) selected @endif>{{$warehouse['name']}}</option>
             @endforeach 
             @endif
        </select>
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">销售价格</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="price" value="{{$row['price']}}" />
    </div>
</div>

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">条码</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="barcode" value="{{$row['barcode']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">单位</label>
    <div class="col-sm-10">
        <select class="form-control input-sm" id='unit' name='unit'>
            <option value=''> - </option>
            @foreach(option('goods.unit') as $unit)
                <option value='{{$unit['id']}}' @if($row->unit == $unit['id']) selected @endif>{{$unit['name']}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label" for="is_share">共享商品 </label>
    <div class="col-sm-10">
        <label class="checkbox-inline"><input type="checkbox" id="is_share" name="is_share" value=""> 是否共享该商品</label>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">状态</label>
    <div class="col-sm-10">
        <label class="radio-inline"><input type="radio" name="status" value="1" @if($row['status'] == '1') checked @endif>启用</label>
        <label class="radio-inline"><input type="radio" name="status" value="0" @if($row['status'] == '0') checked @endif>停用</label>
    </div>
</div>

<div class="form-group">
    <label for="remark" class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <textarea class="form-control input-sm" rows="2" type="text" name="remark" id="remark">{{$row['remark']}}</textarea>
    </div>
</div>

<input type="hidden" name="id" value="{{$row['id']}}" />

</form>

</div>