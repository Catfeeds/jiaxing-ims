<div class="wrapper-sm">

<form method="post" class="form-horizontal" enctype="multipart/form-data" action="{{url()}}" id="purchase-repayment-form" name="purchase-repayment-form">

    <div class="form-group">
        <label for="sort" class="control-label col-sm-2"><span class="red"> * </span> 还款金额</label>
        <div class="col-sm-10">
            <input type="text" name="money" data-toggle="money" id="money" class="form-control input-sm">
        </div>
    </div>

    <div class="form-group">
        <label for="sort" class="control-label col-sm-2">备注</label>
        <div class="col-sm-10">
            <textarea class="form-control" type="text" name="remark" id="remark" placeholder="暂无备注"></textarea>
        </div>
    </div>

    <input type="hidden" name="stock_id" id="stock_id" value="{{$stock_id}}" />

</form>

</div>
    