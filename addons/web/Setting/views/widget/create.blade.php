<div class="panel">

    <form method="post" class="form-horizontal" action="{{url()}}" id="myform" name="myform">
        
        <table class="table table-form">
            <tr>
                <td align="right" width="10%">名称</td>
                <td align="left">
                    <input class="form-control input-inline input-sm" type="text" name="name" value="{{$row['name']}}">
                </td>
            </tr>

            <tr>
                <td align="right">链接</td>
                <td align="left">
                    <input class="form-control input-inline input-sm" type="text" name="path" value="{{$row['path']}}">
                </td>
            </tr>

            <tr>
                <td align="right">权限</td>
                <td align="left">
                    {{Dialog::search($row, 'id=receive_id&name=receive_name&multi=1')}}
                </td>
            </tr>

            <tr>
                <td align="right">图标</td>
                <td align="left">
                    <input class="form-control input-inline input-sm" type="text" name="icon" value="{{$row['icon']}}">
                </td>
            </tr>

            <tr>
                <td align="right">排序</td>
                <td align="left">
                    <input class="form-control input-inline input-sm" type="text" name="sort" value="{{$row['sort']}}">
                </td>
            </tr>

            <tr>
                <td align="right">可用</td>
                <td align="left">
                    <select class="form-control input-inline input-sm" name="status" id="status">
                        <option value="1" @if($row['status'] == 1) selected @endif>是</option>
                        <option value="0" @if($row['status'] == 0) selected @endif>否</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td align="right">默认</td>
                <td align="left">
                    <select class="form-control input-inline input-sm" name="default" id="default">
                        <option value="1" @if($row['default'] == 1) selected @endif>是</option>
                        <option value="0" @if($row['default'] == 0) selected @endif>否</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td align="right"></td>
                <td align="left">
                    <input type="hidden" name="id" value="{{$row['id']}}">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                </td>
            </tr>

        </table>
    </form>
</div>