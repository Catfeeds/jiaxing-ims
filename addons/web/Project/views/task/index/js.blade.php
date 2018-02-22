<script>

function formsBox(title, url, id, success, remove, error)
{
	var options = {
        title: title,
		url: url,
		buttons: []
	};

	if (typeof success === 'function') {
		options.buttons.push({
			text: '<i class="fa fa-check"></i> 保存',
			class: 'btn-info',
			click: function() {
				var me = this;
				var action = $('#'+id).attr('action');
				var formData = $('#'+id).serialize();
				$.post(action, formData, function(res) {
					success.call(me, res); 
				},'json');
			}
		});
	}

	if (typeof remove === 'function') {
		options.buttons.push({
			text: '<i class="fa fa-remove"></i> 删除',
			class: 'btn-danger',
			click: function() {
				var me = this;
				remove.call(me);
			}
		});
	}

	/*
	options.buttons.push({
		text: '取消',
		class: 'btn-default',
		click: function() {
			var me = this;
			if (typeof error === 'function') {
				error.call(me);
			} else {
				$(me).dialog("close");
			}
		}
	});
	*/
    $('#box-' + id).__dialog(options);
}

function saveResult(res) {
	if(res.status) {
		dataReload();
		$.toastr('success', res.data, '提醒');
		$(this).dialog("close");
	} else {
		$.toastr('error', res.data, '提醒');
	}
}

function addItem() {
	formsBox('添加任务列表', app.url('project/task/add', {type:'item',project_id:project_id}), 'item-form', function(res) {
		saveResult.call(this, res);
	});
}

function editItem(id) {

	var fun_edit = null, fun_delete = null;

	var task = getTask(id);

	if(task.option_edit == 1) {
		fun_edit = function(res) {
			saveResult.call(this, res);
		}
	}

	if(task.option_delete == 1) {
		fun_delete = function() {
			var me = this;
			$.messager.confirm('删除确认', '确定要删除任务列表吗？', function() {
				$.post(app.url('project/task/delete'), {id: id}, function(res) {
					saveResult.call(me, res);
				},'json');
			});
		}
	}
	formsBox('编辑任务列表', app.url('project/task/edit', {type:'item',id:id}), 'item-form-'+ id, fun_edit, fun_delete);
}

function addTask() {
	formsBox('添加任务', app.url('project/task/add', {type:'task',project_id:project_id}), 'task-form', function(res) {
		saveResult.call(this, res);
	});
}

function editTask(id) {

	var fun_edit = null, fun_delete = null;

	var task = getTask(id);

	if(task.option_edit == 1) {
		fun_edit = function(res) {
			saveResult.call(this, res);
		}
	}

	if(task.option_delete == 1) {
		fun_delete = function() {
			var me = this;
			$.messager.confirm('删除确认', '确定要删除任务吗？', function() {
				$.post(app.url('project/task/delete'), {id: id}, function(res) {
					saveResult.call(me, res);
				},'json');
			});
		}
	}
	formsBox('编辑任务', app.url('project/task/edit', {type:'task',id:id}), 'task-form-'+ id, fun_edit, fun_delete);
}

function addSubTask(id) {
	formsBox('添加子任务', app.url('project/task/add', {type:'subtask',project_id:project_id,parent_id:id}), 'task-form', function(res) {
		if(res.status) {
			dataReload();
			$('#task-subtask-' + id).prepend('<p><span class="time">'+ res.data.created_at + '(' + res.data.user_name + ')</span><label class="i-checks i-checks-sm m-b-none"><input class="select-row" type="checkbox" name="progress" value="'+ res.data.progress + '"><i></i></label><a href="javascript:editSubTask(' + res.data.id + ');">'+ res.data.name + '</a></p>');
			$.toastr('success', '恭喜您，添加子任务成功。', '提醒');
			$(this).dialog("close");
		} else {
			$.toastr('error', res.data, '提醒');
		}
	});
}

function editSubTask(id) {

	var fun_edit = null, fun_delete = null;

	var task = getTask(id);

	if(task.option_edit == 1) {
		fun_edit = function(res) {
			saveResult.call(this, res);
		}
	}

	if(task.option_delete == 1) {
		fun_delete = function() {
			var me = this;
			$.messager.confirm('删除确认', '确定要删除任务吗？', function() {
				$.post(app.url('project/task/delete'), {id: id}, function(res) {
					saveResult.call(me, res);
				},'json');
			});
		}
	}
	formsBox('编辑子任务', app.url('project/task/edit', {type:'subtask',id:id}), 'task-form-'+ id, fun_edit, fun_delete);
}

function addComment(task_id) {
	formsBox('添加评论', app.url('project/comment/add', {task_id:task_id}), 'comment-form', function(res) {
		if(res.status) {
			$('#task-log-' + task_id).prepend('<p class="task-log-comment"><span class="time">' + res.data.created_at + '</span><div class="task-log-user">' + res.data.user + '</div>' + res.data.content + '</p>');
			$.toastr('success', '恭喜您，添加评论成功。', '提醒');
			$(this).dialog("close");
		} else {
			$.toastr('error', res.data, '提醒');
		}
	});
}

function editComment(id) {
	formsBox('编辑评论', app.url('project/comment/edit', {id:id}), 'comment-form', function(res) {
		saveResult.call(this, res);
	}, function() {
		var me = this;
		$.messager.confirm('删除确认', '确定要删除评论吗？', function() {
			$.post(app.url('project/comment/delete'), {id:id}, function(res) {
				saveResult.call(me, res);
			},'json');
		});
	});
}

</script>