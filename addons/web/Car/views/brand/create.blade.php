<div class="wrapper-sm">

    <form method="post" class="form-horizontal" action="{{url()}}" id="user-group-form" name="user_group_form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">名称</label>
            <div class="col-sm-10">
                <input type="text" id="name" name="name" class="form-control input-sm" value="{{$row['name']}}">
            </div>
        </div>

        <input type="hidden" name="id" value="{{$row['id']}}">
            
    </form>
</div>