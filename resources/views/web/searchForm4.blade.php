<div class="wrapper-xs">

<div class="row">

    @foreach($search['columns'] as $i => $column)

        <?php if($column[0] == 'text2') { continue; } ?>
    
        <div class="wrapper-xs">
            <div class="col-sm-2">
            <div class="form-group">
                <label class="control-label">{{$column[2]}}</label>
                <?php
                if (is_array($column[0])) {
                    $__type  = $column[0]['type'];
                } else {
                    $__type  = $column[0];
                }
                ?>
                <input type="hidden" name="field_{{$i}}" id="search-field-{{$i}}" data-type="{{$__type}}" value="{{$column[1]}}">
            </div>
            </div>
            <div class="col-sm-2">
            <div class="form-group" style="display:none;">
                    <select name="condition_{{$i}}" id="search-condition-{{$i}}" class="form-control input-sm"></select>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group" id="search-value-{{$i}}"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    @endforeach
</div>
</div>

@if($search['params'])
@foreach($search['params'] as $key => $param)
    <input name="{{$key}}" type="hidden" value="{{$param}}">
@endforeach
@endif