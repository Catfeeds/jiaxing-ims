<div class="panel">
    <div class="panel-body">
        <form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">

            <div class="form-group">
                <label class="col-sm-2 control-label">扣罚原因</label>
                <div class="col-sm-10">
                    <input class="form-control input-sm" type="text" id="name" name="name" value="{{$row->name}}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">扣罚分数</label>
                <div class="col-sm-10">
                    <input class="form-control input-sm" type="text" id="grade" name="grade" value="{{$row->grade}}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">扣罚日期</label>
                <div class="col-sm-10">
                    <input data-toggle="date" class="form-control input-sm" type="text" id="start_date" name="start_date" value="{{$row->start_date}}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">流程编号</label>
                <div class="col-sm-10">
                    <input class="form-control input-sm" type="text" id="process_id" name="process_id" value="{{$row->process_id}}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">详细描述</label>
                <div class="col-sm-10">
                    <textarea class="form-control input-sm" name="description" id="description">{{$row->description}}</textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <input type="hidden" name="id" value="{{$row->id}}">
                    <input type="hidden" name="hr_id" value="{{$row->hr_id}}">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>
