<div class="wrapper-sm">

<form method="post" class="form-horizontal" action="{{url()}}" id="stock-warehouse-form" name="stock_warehouse_form">

<!--
<div class="form-group">
    <label for="parent_id" class="col-sm-2 control-label">上级类别</label>
    <div class="col-sm-10">
        <select class="form-control input-sm" name="parent_id" id="parent_id">
            <option value=""> - </option>
             @if(count($type)) @foreach($type as $k => $v)
                <option value="{{$v['id']}}" @if($row['parent_id'] == $v['id']) selected @endif>{{$v['layer_space']}}{{$v['name']}}</option>
             @endforeach @endif
        </select>
    </div>
</div>
-->

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">名称 <span class="red">*</span></label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="name" value="{{$row['name']}}" />
    </div>
</div>

<div class="form-group">
    <label for="user_id" class="col-sm-2 control-label">仓库管理员 <span class="red">*</span></label>
    <div class="col-sm-10">
        {{Dialog::user('user','user_id', $row['user_id'], 0, 0)}}
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
    <label for="sort" class="col-sm-2 control-label">排序</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="sort" value="{{$row['sort']}}" />
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