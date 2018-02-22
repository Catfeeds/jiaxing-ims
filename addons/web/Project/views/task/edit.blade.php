<style>
.modal-body { overflow:hidden; }
</style>

<form method="post" class="project-form" action="{{url()}}" id="task-form-{{$task['id']}}" name="task-form-{{$task['id']}}">
<div class="panel m-b-none">

    <table class="table table-form m-b-none">

        <tr>
            <td align="right" width="15%">名称</td>
            <td align="left">
                
                @if($permission['name'])
                    <div class="input-group">
                        <div class="input-group-check">
                            <label class="i-checks i-checks-lg m-b-none hinted" title="点击完成任务">
                                <input class="select-row" name="progress" type="checkbox" @if($task['progress'] == 1)checked="checked" @endif value="{{$task['progress']}}"><i></i>
                            </label>
                        </div>
                        <input type="text" name="name" value="{{$task['name']}}" class="form-control input-sm">
                    </div>
                @else

                    @if($task['progress'] == 1)
                        <span class="label label-success">完成</span>
                    @else 
                        <span class="label label-info">执行中</span>
                    @endif

                    <input type="hidden" name="progress" value="{{$task['progress']}}">
                    <input type="hidden" name="name" value="{{$task['name']}}">

                    {{$task['name']}}

                @endif
            </td>
        </tr>

        @if($type == 'task')
        <tr>
            <td align="right">任务列表</td>
            <td align="left">
                @if($items)
                    @if($permission['parent_id'])
                        <select class="form-control input-sm" name="parent_id">
                            @foreach($items as $item)
                            <option value="{{$item['id']}}" @if($item['id'] == $task['parent_id']) selected="selected" @endif>{{$item['name']}}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="parent_id" value="{{$task['parent_id']}}">
                        @foreach($items as $item)
                            @if($item['id'] == $task['parent_id']) {{$item['name']}} @endif
                        @endforeach
                    @endif
                @endif
            </td>
        </tr>
        @endif

        <tr>
            <td align="right">执行者</td>
            <td align="left">
                @if($permission['user_id'])
                    {{Dialog::user('user', $type.'_user_id', $task['user_id'], 0, 0)}}
                @else
                    <input type="hidden" name="{{$type}}_user_id" value="{{$task['user_id']}}">
                    {{Dialog::text('user', $task['user_id'])}}
                @endif
            </td>
        </tr>

        <tr>
            <td align="right">参与者</td>
            <td align="left">
                @if($permission['users'])
                    {{Dialog::user('user', $type.'_users', $task['users'], 1, 0)}}
                @else
                    <input type="hidden" name="{{$type}}_users" value="{{$task['users']}}">
                    {{Dialog::text('user', $task['users'])}}
                @endif
            </td>
        </tr>
    
        <tr>
            <td align="right">时间</td>
            <td align="left">
                @if($permission['date'])
                    <input type="text" name="start_at" data-toggle="datetime" value="@datetime($task->start_at,time())" class="form-control input-sm input-inline"> 
                    - 
                    <input type="text" name="end_at" data-toggle="datetime" value="@datetime($task->end_at)" class="form-control input-sm input-inline">
                @else
                    <input type="hidden" name="start_at" value="@datetime($task->start_at)"> 
                    <input type="hidden" name="end_at" value="@datetime($task->end_at)">
                    @datetime($task->start_at)
                    - 
                    @datetime($task->end_at)
                @endif
            </td>
        </tr>

        <tr>
            <td align="right">备注</td>
            <td>
                @if($permission['remark'])
                    <textarea class="form-control" type="text" name="remark">{{$task->remark}}</textarea>
                @else
                    {{$task->remark}}
                @endif
            </td>
        </tr>

        <tr>
            <td align="right">附件</td>
            <td align="left">
                @if($permission['attachment'])
                    {{attachment_uploader('attachment', $task['attachment'])}}
                @else
                    {{attachment_show('attachment', $task['attachment'])}}
                @endif
            </td>
        </tr>

        <tr>
            <td align="right">子任务</td>
            <td>
                <div class="task-subtask" id="task-subtask-{{$task->id}}">

                    @if($tasks)
                    @foreach($tasks as $v)
                        <p>
                            <span class="time">
                                @datetime($v->created_at)
                                ({{get_user($v['user_id'], 'nickname', false)}})
                            </span>

                            @if($v->progress == 1)
                                <span class="label label-success">完成</span>
                            @else
                                @if(auth()->id() == $v->user_id)
                                    <span class="label label-danger">执行中</span>
                                @else
                                    <span class="label label-info">执行中</span>
                                @endif
                            @endif

                            <!--
                            <label class="i-checks i-checks-sm m-b-none">
                                <input class="select-row" type="checkbox" value="1"><i></i>
                            </label>
                            -->

                            <a href="javascript:editSubTask({{$v->id}});">{{$v->name}}</a>
                        </p>
                    @endforeach
                    @endif

                    @if($permission['add-subtask'] == 1)
                    <p class="m-b-none"><a href="javascript:addSubTask({{$task->id}});" class="option option-add"><i class="fa fa-fw fa-plus"></i>添加子任务</a></p>
                    @endif

                </div>
            </td>
        </tr>

        <tr>
            <td align="right"><span class="red">每日进展回复</span></td>
            <td>
                <div class="task-log" id="task-log-{{$task->id}}">

                    @if($logs)
                    @foreach($logs as $log)
                        @if($log->type == 'comment')
                            <p class="task-log-comment"><span class="time">@datetime($log->created_at)</span><div class="task-log-user">{{$log->user}}</div>{{$log->content}}</p>
                        @else 
                            <p class="task-log-content"><span class="time">@datetime($log->created_at)</span>{{$log->user}} {{$log->content}}</p>
                        @endif
                    @endforeach
                    @endif

                    @if($permission['add-comment'] == 1)
                    <p class="m-b-none"><a href="javascript:addComment({{$task->id}});" class="option option-add"><i class="fa fa-fw fa-plus"></i>添加回复</a></p>
                    @endif

                </div>
            </td>
        </tr>

        </table>
    </div>

</div>

<input type="hidden" name="type" value="{{$type}}">
<input type="hidden" name="project_id" value="{{$task->project_id}}">
<input type="hidden" name="id" value="{{$task->id}}">
<input type="hidden" name="is_item" value="0">

</form>