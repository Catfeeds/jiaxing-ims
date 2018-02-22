<form id="search-form" class="form-inline" name="mysearch" action="{{url()}}" method="get">

        <div class="pull-right">

            @if(isset($access['create']))
                <a href="{{url('create')}}" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a>
            @endif

        </div>

        <div class="input-group">
            <button type="button" class="btn btn-sm btn-default" data-toggle="dropdown">
                批量操作
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu text-xs">
                @if(isset($access['delete']))
                <li><a href="javascript:optionDelete('#myform','{{url('delete')}}');"><i class="icon icon-trash"></i> 删除</a></li>
                @endif
            </ul>
        </div>

        @include('searchForm')

</form>

<script type="text/javascript">

$(function() {
    $('#search-form').searchForm({
        data: {{json_encode($search['forms'])}},
        init: function(e) {
            var self = this;
        }
    });
});
</script>