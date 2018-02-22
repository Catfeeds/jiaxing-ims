<form id="search-form" class="form-inline" name="mysearch" action="{{url()}}" method="get">

    <div class="pull-right">
        @if(isset($access['delete']))
            <button type="button" onclick="optionDelete('#myform','{{url('delete')}}');" class="btn btn-sm btn-danger"><i class="icon icon-remove"></i> 删除</button>
        @endif
    </div>

    @if(isset($access['create']))
        <a href="javascript:formBox('新建','{{url('create', ['id'=>$row->id])}}','window-form');" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a>
    @endif

    @include('searchForm')

</form>
<script type="text/javascript">
$(function() {
    $('#search-form').searchForm({
        data:{{json_encode($search['forms'])}},
        init:function(e) {
            var self = this;
        }
    });
});
</script>