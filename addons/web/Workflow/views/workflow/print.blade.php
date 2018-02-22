<style type="text/css">
body { 
	background-color: #fff;
}
table.workflow,
table.workflow td, 
table.workflow th {
	border-color: #222 !important;
}
</style>

<script type="text/javascript">
$(function() {
	$('tbody').on('change',function(i) {
		listView.rowUpdate(i);
	});
	{{$jsonload}}
});

// 工作流全局对象
var workFlow = {{$work['js']}};

// 工作流js定义区域
{{$js}}
</script>
{{$template}}
