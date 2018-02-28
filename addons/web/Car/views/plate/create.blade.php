<div class="wrapper-sm">

    <form method="post" class="form-horizontal" action="{{url()}}" id="user-group-form" name="user_group_form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">车牌前缀名称</label>
            <div class="col-sm-10">
                <input type="text" id="name" name="name" class="form-control input-sm" value="{{$row['name']}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">省份</label>
            <div class="col-sm-10">
                <input type="text" id="region" name="region" class="form-control input-sm" value="{{$row['region']}}">
            </div>
        </div>

        <div class="form-group">
            <label for="remark" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <textarea class="form-control input-sm" rows="2" type="text" name="remark" id="remark">{{$row['remark']}}</textarea>
            </div>
        </div>

        <input type="hidden" name="id" value="{{$row['id']}}">
            
    </form>
</div>