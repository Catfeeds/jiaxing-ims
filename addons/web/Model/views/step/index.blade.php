 <div class="panel">

    <form method="post" action="{{url()}}" id="myform" name="myform">

    <div class="wrapper">
        <a class="btn btn-default btn-sm" href="{{url('model/index')}}">模型管理</a>
        <a class="btn btn-info btn-sm" href="{{url('create', ['model_id'=>$model->id])}}">新建</a>
    </div>

    <table class="table m-b-none b-t">
        <tr>
            <th align="right" width="10%">流程名称</th>
            <td align="left">{{$model->name}}</td>
            <th align="right" width="10%">表</th>
            <td align="left">{{$model->table}}</td>
        </tr>
    </table>
</div>

<div class="panel">
    <table class="table table-hover">
        <tr>
            <th align="left">进程名称</th>
            <th align="left">转入进程</th>
            <th align="center">类型</th>
            <th align="center">颜色</th>
            <th align="center">编号</th>
            <th align="center"></th>
        </tr>
        @foreach($rows as $row)

        <tr>
            <td align="left"><span class="badge">{{$row->number}}</span> {{$row->name}}</td>
            <td align="left">
                @foreach($row->links as $number => $name)
                    <span class="badge">{{$number}}</span> {{$name}}
                @endforeach
            </td>
            <td align="center"><span class="label label-default">{{$row->type}}</span></td>
            <td align="center"><span class="label label-{{$row->color}}">{{$row->color}}</span></td>
            <td align="center">{{$row->id}}</td>
            <td align="center">
                <a class="option" href="{{url('condition',['model_id'=>$model->id,'id'=>$row->id])}}">条件</a>
                <a class="option" href="{{url('create',['model_id'=>$model->id,'id'=>$row->id])}}">编辑</a>
                @if($row->system == 0)
                <a class="option" onclick="app.confirm('{{url('delete',['id'=>$row->id])}}','确定要删除吗？');" href="javascript:;">删除</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    </form>
</div>