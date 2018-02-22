<div class="panel">

    <div class="wrapper">
        <a class="btn btn-info btn-sm" href="{{url('create')}}"><i class="icon icon-plus"></i> 新建</a>
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
    <table class="table m-b-none table-hover b-t">
    <thead>
        <tr>
            <th align="left">名称</th>
            <th align="left">表名</th>
            <th align="left">关联字段</th>
            <th align="center">编号</th>
            <th align="center"></th>
        </tr>
    </thead>
    @foreach($rows as $row)
    <tr>
        <td align="left">
            {{str_repeat('<span class="level"></span>', $row['layer_level'])}}
            @if(sizeof($row['child']) == 1)
                <i class="fa fa-file-o"></i>
            @else
                <i class="fa fa-folder-o"></i>
            @endif
            {{$row['name']}}
        </td>
        <td align="left">{{$row->table}}</td>
        <td align="left">{{$row->relation}}</td>
        <td align="center">{{$row->id}}</td>
        <td align="center">
            <a class="option" href="{{url('template/index',['model_id'=>$row->id])}}"> 模板 </a>
            <a class="option" href="{{url('field/index',['model_id'=>$row->id])}}"> 字段 </a>
            @if($row->parent_id == 0)
                <a class="option" href="{{url('step/index',['model_id'=>$row->id])}}"> 流程 </a>
            @endif
            <a class="option" href="{{url('create',['id'=>$row->id])}}"> 编辑 </a>
            @if($row->system == 0)
                <a class="option" onclick="app.confirm('{{url('delete',['id'=>$row->id])}}','确定要删除吗？');" href="javascript:;">删除</a>
            @endif
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