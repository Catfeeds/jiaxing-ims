<form id="search-form" role="form" class="form-inline" name="search-form" action="{{url()}}" method="get">

    @if(isset($access['add']))
        <a href="{{url('add')}}" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a>
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
