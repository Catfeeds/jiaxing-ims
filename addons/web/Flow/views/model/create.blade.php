<div class="panel">

    <form method="post" action="{{url()}}" id="myform" name="myform">
        <table class="table table-form m-b-none">
        <tr>
            <td align="right" width="10%">模型名称</th>
            <td><input type="text" id="name" name="name" value="{{$row->name}}" onblur="app.pinyin('name','table');" class="form-control input-sm"></td>
        </tr>

        <tr>
            <td align="right">父节模型</td>
            <td>
                <select class="form-control input-sm" name="parent_id" id="parent_id">
                    <option value="0"> - </option>
                    @foreach($models as $model)
                        <option value="{{$model->id}}" @if($model->id == $row->parent_id) selected @endif>{{$model->name}}</option>
                    @endforeach
                </select>
            </td>
        </tr>

        <tr>
            <td align="right">模型类型</td>
            <td>
                <select class="form-control input-sm" name="type" id="type">
                    <option value="0" @if($row->type == 0) selected @endif> - </option>
                    <option value="1" @if($row->type == 1) selected @endif>多行子表</option>
                </select>
            </td>
        </tr>

        <tr>
            <td align="right">关联外键</td>
            <td><input type="text" id="relation" name="relation" value="{{$row->relation}}" class="form-control input-sm"></td>
        </tr>

        <tr>
            <td align="right">数据表名</td>
            <td><input type="text" id="table" name="table" value="{{$row->table}}" class="form-control input-sm" @if($row->id > 0) readonly @endif></td>
        </tr>

        <tr>
            <td align="right">流程类型</td>
            <td>
                <label class="radio-inline"><input type="radio" @if($row['is_flow'] == 1) checked @endif value="1" name="is_flow"> 固定 </label>
                <label class="radio-inline"><input type="radio" @if($row['is_flow'] == 2) checked @endif value="2" name="is_flow"> 自由 </label>
                <label class="radio-inline"><input type="radio" @if($row['is_flow'] == 0) checked @endif value="0" name="is_flow"> 无 </label>
            </td>
        </tr>

        <tr>
            <td align="right">数据排序</td>
            <td>
                <label class="radio-inline"><input type="radio" @if($row['is_sort'] == 1) checked @endif value="1" name="is_sort"> 是 </label>
                <label class="radio-inline"><input type="radio" @if($row['is_sort'] == 0) checked @endif value="0" name="is_sort"> 否 </label>
            </td>
        </tr>

        <tr>
            <td align="left" colspan="2">
                <input type="hidden" name="id" value="{{$row->id}}">
                <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-check-circle"></i> 保存</button>
            </td>
        </tr>

        </table>

    </form>

</div>