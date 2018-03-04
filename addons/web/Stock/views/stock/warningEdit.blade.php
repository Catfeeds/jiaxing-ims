<div class="wrapper-sm">

    <form method="post" class="form-horizontal" action="{{url()}}" id="stock-warning-form" name="stock-warning-form">

        <div class="form-group">
            <label for="stock_min" class="col-sm-2 control-label">最低库存</label>
            <div class="col-sm-10">
                <input type="text" name="stock_min" value="{{$row->stock_min}}" id="stock_min" class="form-control input-sm">
            </div>
        </div>

        <div class="form-group">
            <label for="stock_max" class="col-sm-2 control-label">最高库存</label>
            <div class="col-sm-10">
                <input type="text" name="stock_max" value="{{$row->stock_max}}" id="stock_max" class="form-control input-sm">
            </div>
        </div>

        <input type="hidden" name="id" id="id" value="{{$row->id}}" />

    </form>

</div>
    