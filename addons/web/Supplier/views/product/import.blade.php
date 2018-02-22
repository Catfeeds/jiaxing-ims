<div class="panel">

    <div class="panel-body">

        <form enctype="multipart/form-data" class="form-horizontal" action="{{url()}}" method="post">
            
            <div class="form-group">
                <label class="col-sm-1 control-label">文件</label>
                <div class="col-sm-11">
                    <input class="form-control input-sm" type="file" id="myfile" name="myfile">
                    <div class="help-block">支持07-2003格式的xls文件
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-11 col-sm-offset-1">
                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i>上传</button>
                </div>
            </div>

        </form>
    </div>
</div>
