<div class="wrapper-sm">

    <form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{url()}}" id="create-form" name="create_form">

        <div class="form-group m-b-none">
            <label class="col-sm-2 control-label" for="file">模板文件</label>
            <div class="col-sm-10">
                <input type="file" id="file" name="file">
            </div>
        </div>

        <input type="hidden" name="node" value="{{$gets['node']}}">
        <input type="hidden" name="size" value="{{$gets['size']}}">
            
    </form>
</div>