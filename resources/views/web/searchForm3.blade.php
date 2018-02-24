<div class="wrapper-sm">

<?php $a = 0; ?>

@foreach($search['columns'] as $i => $column)

<?php
if ($column[0] == 'text') {
    continue;
}

$a++;

?>

<div class="form-group">
        @if($a > 1) &nbsp; @endif
        @if($column[0] == 'text2')
            <input type="hidden" name="label_{{$i}}" id="search-label-{{$i}}" value="{{$column[2]}}">
            关键词: </span>
        @else
            <span id="search-label-{{$i}}">{{$column[2]}}: </span>
        @endif

        <?php
        if (is_array($column[0])) {
            $__type  = $column[0]['type'];
        } else {
            $__type  = $column[0];
        }
        ?>
        <input type="hidden" name="field_{{$i}}" id="search-field-{{$i}}" data-type="{{$__type}}" value="{{$column[1]}}">
    </div>
        <div class="form-group" style="display:none;">
        <select name="condition_{{$i}}" id="search-condition-{{$i}}" class="form-control input-sm"></select>
    </div>
    <div class="form-group" id="search-value-{{$i}}"></div>
@endforeach

<button id="search-submit" type="submit" class="btn btn-sm btn-default"> <i class="fa fa-search"></i> 搜索</button>

</div>