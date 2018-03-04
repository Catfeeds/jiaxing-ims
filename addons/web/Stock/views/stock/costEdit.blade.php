<div class="wrapper-sm">
<form method="post" enctype="multipart/form-data" action="{{url()}}" id="stock-cost-form" name="stock-cost-form">
    <div class="form-group">
    <input type="text" name="stock_cost" id="stock_cost" value="{{$row->stock_cost}}" class="form-control input-sm">
    </div>
    <input type="hidden" name="id" id="id" value="{{$row->id}}" />
</form>
</div>