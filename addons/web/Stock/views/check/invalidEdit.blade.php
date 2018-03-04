<div class="wrapper-sm">
<form method="post" enctype="multipart/form-data" action="{{url()}}" id="return-invalid-form" name="return-invalid-form">
    <div class="form-group">
        <textarea class="form-control" type="text" name="remark" id="remark" placeholder="作废备注"></textarea>
    </div>
    <input type="hidden" name="id" id="id" value="{{$row->id}}" />
</form>
</div>
    