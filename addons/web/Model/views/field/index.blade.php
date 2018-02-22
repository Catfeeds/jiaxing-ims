<div class="panel">

    <form method="post" id="myform" name="myform">

        <div class="wrapper">
            <a class="btn btn-default btn-sm" href="{{url('model/index')}}">模型管理</a>
            <a class="btn btn-info btn-sm" href="{{url('create',['model_id'=>$model_id])}}">新建</a>
        </div>

        <table class="table m-b-none table-hover">
        <tr>
            <th align="left">字段别名</th>
        	<th align="left">字段名</th>
        	<th align="left">字段类型</th>
        	<th align="left">字段索引</th>
            <th align="left">表单类型</th>
            <th align="center">排序</th>
            <th align="center">编号</th>
        	<th align="center"></th>
        </tr>
        @foreach($rows->fields as $row)
        <tr>
        	<td align="left">{{$row['name']}}</td>
            <td align="left">{{$row['field']}}</td>
        	<td align="left">{{$row['type']}}({{$row['length']}})</td>
        	<td align="left">{{$row['index']}}</td>
            <td align="left">{{$row['form_type']}}</td>
            <td align="center"><input type="text" name="sort[{{$row['id']}}]" class="form-control input-sort" value="{{$row['sort']}}"></td>
        	<td align="center">{{$row['id']}}</td>
            <td align="center">
        	    <a class="option" href="{{url('create',['flow_id'=>$flow_id,'model_id'=>$model_id,'id'=>$row['id']])}}">编辑</a>
        	    @if($row['system'] == 0)
        			<a class="option" href="javascript:app.confirm('{{url('delete',['id'=>$row['id']])}}','确定要删除吗？');">删除</a>
        	    @endif
        	</td>
        </tr>
        @endforeach
        </table>

    </form>

    <footer class="panel-footer">
        <div class="row">
            <div class="col-sm-2 hidden-xs">
                <button type="button" onclick="optionSort('#myform','{{url('index',['model_id'=>$model_id])}}');" class="btn btn-sm btn-default">排序</button>
            </div>
            <div class="col-sm-10 text-right text-center-xs">
            </div>
        </div>
    </footer>

</div>