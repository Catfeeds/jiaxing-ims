<style type="text/css">
.content-body { margin: 0; }

.widget-row .table {
    border-radius: 0;
}
.widget-row .fixed-table-container {
    border: 0;
}
.widget-row .fixed-table-container tbody td,
.widget-row .fixed-table-container thead th {
    border-left: 0;
}
.widget-row .bootstrap-table .table > thead > tr > th {
    border-bottom: 0;
}
.row-sm { margin-left: 8px; margin-right: 8px; }
.row-sm > div { padding-left: 8px; padding-right: 8px; }
.row-sm > div > .panel {
    margin-bottom: 16px !important;
    text-align: center; 
}
.row-todo .panel { padding-bottom: 10px; position: relative; }
.todo-logo { color: #fff; padding-top:20px; position:absolute; top:0; bottom:0; left:0; width: 80px; }
.todo-text { margin-left: 0; }

.app-title {
    padding-top: 15px;
    padding-bottom: 15px;
}

@media (min-width: 768px) {
    .widget-row {
        overflow-y: auto;
        height: 200px;
    }
    .todo-text { margin-left: 60px; }
}

.pull-right a .fa {
    -moz-opacity:0.5;
    opacity: 0.5;
    color: #fff;
}
.pull-right a:hover .fa {
    -moz-opacity:1;
    opacity: 1;
}

.frame-primary .dashboard-title {
    color: #58666e;
}
.frame-primary .pull-right a .fa {
    color: #58666e;
}
.frame-primary .pull-right a:hover .fa {
    -moz-opacity:1;
    opacity: 1;
}
</style>

<div class="app-title wrapper-md">
    <!--
    <div class="pull-right">
        @if($panel == 'edit')
            <a href="{{url('',['panel' => 'add'])}}" title="配置组件">
                <i class="fa text-white fa-caret-down"></i>
            </a>
        @else
            <a href="{{url('',['panel' => 'edit'])}}" title="配置组件">
                <i class="fa text-white fa-caret-down"></i>
            </a>
        @endif
    </div>
    -->
    <div class="text-white text-md dashboard-title"><i class="fa fa-dashboard text-md"></i> 个人空间</div>
</div>

@include('layouts/errors')

<div class="dashboard-widget">

    <div class="row row-sm row-todo">
        @foreach($panels as $_panel)
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="panel">
                <div class="todo-logo hidden-xs {{$_panel[4]}}">
                    <i class="fa fa-3x {{$_panel[3]}}"></i>
                </div>
                <div class="todo-text">
                    <a href="{{$_panel[1]}}">
                        <div class="text-3x @if($_panel[0] > 0) text-danger @else text-info @endif">{{$_panel[0]}}</div>
                    </a>
                    <div class="text-muted">{{$_panel[2]}}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row row-sm">

        @foreach($widgets as $i => $widget)

            <div class="col-xs-12 col-sm-6 @if($i == 100) col-md-8 col-lg-8 @else col-md-6 col-lg-6 @endif">
            <div class="panel no-border">
                <div class="panel-heading text-base b-b">
                    <div class="pull-left">
                        <i class="fa {{$position['icon']}}"></i> {{$widget['name']}}
                    </div>
                    @if($panel == 'edit')
                    <div class="pull-right">
                        <!--
                        <a href="javascript:;"><i class="fa fa-pencil"></i></a>
                        &nbsp;
                        -->
                        <a href="javascript:;" onclick="widgetClose('{{$widget['id']}}');"></a><i class="fa fa-times"></i></a>
                    </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
                <div class="widget-row" url="{{$widget['path']}}" id="{{$widget['id']}}"></div>
            </div>
            </div>
            @endforeach
    </div>
</div>

<script>
function widgetSortable()
{
    var e = $(".panel").parent("[class*=col]");
    var t = ".panel-heading";
    var n = ".row > [class*=col]";
    $(e).sortable({
        handle: t,
        connectWith: n,
        stop: function(e, t) {
            // t.item.find(".panel-title").append('<i class="fa fa-refresh fa-spin m-l-5" data-id="title-spinner"></i>');
            widgetSavePosition();
        }
    });
}

function widgetSavePosition()
{
    var rows = $('.widget-row');
    var positions = [];
    if(rows.length) {
        $.map(rows, function(row) {
            positions.push({name:$(row).attr('title'),id:$(row).attr('id')});
        });
    }
}

function widgetClose(id)
{
    var panel = $('#'+id).closest('.panel');
    panel.remove();
}

function widgetInit()
{
    var rows = $('.widget-row');
    for (var i = 0; i < rows.length; i++) {
        var row = $(rows[i]);
        var url = row.attr('url');
        if(url.indexOf('/')) {
            $("#"+rows[i].id).load(app.url(row.attr('url')));
        }
    }
}

$(function() {
    widgetInit();
    @if($panel == 'edit')
        widgetSortable();
    @endif
});
</script>
