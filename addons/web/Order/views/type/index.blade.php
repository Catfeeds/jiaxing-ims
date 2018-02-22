<div class="panel">

<div class="wrapper">

    <div class="pull-right">
        @if(isset($access['add']))
            <a href="{{url('add')}}" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a>
        @endif
    </div>

    <div class="input-group">
        <button type="button" class="btn btn-sm btn-default" data-toggle="dropdown">
            批量操作
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu text-xs">
            <li><a href="javascript:optionSort('#myform','{{url('index')}}');"><i class="icon icon-sort-by-order"></i> 排序</a></li>
        </ul>
    </div>

</div>

<form method="post" action="{{url()}}" id="myform" name="myform">
<table class="table m-b-none table-hover">
    <tr>
    <th align="left">名称</th>
    <th>备注</th>
    <th align="center">排序</th>
    <th align="center">编号</th>
    <th></th>
	</tr>
   @if(count($rows)) @foreach($rows as $v)
  <tr>
  <td align="left">
    {{str_repeat('<span class="level"></span>', $v['layer_level'])}}
    @if(sizeof($v['child']) == 1)
        <i class="fa fa-file-o"></i>
    @else
        <i class="fa fa-folder-o"></i>
    @endif
    {{$v['title']}}
  </td>
  <td align="left">{{$v['remark']}}</td>
  <td align="center">
    <input type="text" class="form-control input-sort" name="sort[{{$v['id']}}]" value="{{$v['sort']}}" />
  </td>
  <td align="center">{{$v['id']}}</td>
  <td align="center">
    <a class="option" href="{{url('add')}}?id={{$v['id']}}"> 编辑 </a>
    <a class="option" href="javascript:app.confirm('{{url('delete',['id'=>$v['id']])}}','确定要删除吗？');">删除</a>
  </td>
  </tr>
  @endforeach 
  @endif
</table>

<footer class="panel-footer">
      <div class="row">
        <div class="col-sm-1 hidden-xs">
        </div>
        <div class="col-sm-11 text-right text-center-xs">
        </div>
      </div>
    </footer>
</div>

</form>