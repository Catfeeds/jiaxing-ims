<link href='{{$asset_url}}/vendor/fullcalendar/fullcalendar.min.css' rel='stylesheet' />
<link href='{{$asset_url}}/vendor/fullcalendar/theme.css' rel='stylesheet' />

<script src='{{$asset_url}}/libs/moment.min.js'></script>
<script src='{{$asset_url}}/vendor/fullcalendar/fullcalendar.min.js'></script>
<script src='{{$asset_url}}/vendor/fullcalendar/locale/zh-cn.js'></script>
<script type='text/javascript'>

var timelineInterval = 0;
var calendar = null;
var auth_id = '{{Auth::id()}}';
var user_id = '{{$user->id}}';

// var overlay = null;

$(function() {

    getCalendars(function(eventSources) {

        InitCalendar(eventSources);

        $('#calendars').on('click',"[data-calendar='active']", function() {
            activeCalendar(this);
        }).on('click',"[data-calendar='edit']", function() {
            var data = $(this).data();
            editCalendar(data.id);
        })

        $(document).on('click',"[data-calendar='help']", function() {
            help();
        });
    });

    // overlay = $('.fc-overlay');

});

function InitCalendar(sources)
{
	calendar = $('#calendar').fullCalendar({
        height: getPanelHeight(),
        header: {
            right: 'agendaDay,agendaWeek,month,listMonth',
            center: 'title',
            left: 'prev,next today'
        },
        lang: 'zh_cn',
        views: {
            month: {
                titleFormat: 'YYYY年M月'
            },
            week: {
                titleFormat: 'YYYY年M月D日'
            },
            day: {
                titleFormat: 'YYYY年M月D日'
            }
        },
        aspectRatio: 1.8,
        allDayText:'全天',
        slotLabelFormat: 'HH:mm',
        timeFormat: 'HH:mm',
        minTime:'07:00:00',
        maxTime:'23:00:00',
        titleRangeSeparator:' - ',
        monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
        monthNamesShort: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
        dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
        dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
        buttonText: {today: '今天',month: '月',week: '周',day: '日', listMonth: '列表'},
		editable: true,
		selectable: true,
		selectHelper: true,
        eventLimit: true,
		defaultView: 'agendaWeek',
        eventSources: sources,
        viewRender:function(view) {
            /*
        	window.clearInterval(timelineInterval);
        	timelineInterval = window.setInterval(function() {
				timeLine(view);
			}, 10000);
			timeLine(view);
            */
        },
		loading:function(status, view) {
			if (status) {
				$('#loading').show();
			} else {
				$('#loading').hide();
			}
		},

		// 改变大小事件 event, dayDelta, minuteDelta, revertFunc
		eventResize: function(event, delta, revertFunc) {

			if(auth_id == user_id) {
                resizeEvent(event, delta, revertFunc);
			} else {
                $.toastr('error', '不能给下属调整事件。');
				revertFunc();
			}
		},

		// 拖动事件
		eventDrop: function(event, delta, revertFunc) {

			if(auth_id == user_id) {
                moveEvent(event, delta, revertFunc);
			} else {
                $.toastr('error', '不能给下属移动事件。');
				revertFunc();
			}
		},

		// 点击事件
		eventClick: function(event, jsEvent, view) {

            if(event.source.id == 'shared') {

				viewEvent(event, jsEvent, view);

			} else if(auth_id == event.source.userid) {
				if(event.id > 0) {
					editEvent(event, jsEvent, view);
				} else {
					addEvent(event, jsEvent, view);
				}
			}
		},

		// 选择后弹出
		select: function(start, end, jsEvent, view) {

			if(auth_id == user_id) {
				addEvent({start: start, end: end}, jsEvent, view);
			} else {
                $.toastr('error', '不能给下属添加事件。');
			}
		},
        eventMouseover: function(calEvent, jsEvent, view) {
            /*
            if(view.type == 'listMonth') {
                return;
            }

            overlay.find('#overlay-title').text(calEvent.title);
            overlay.find('#overlay-start').text(calEvent.start.format());
            overlay.find('#overlay-end').text(calEvent.end.format());
            overlay.find('#overlay-location').text(calEvent.location);
            overlay.find('#overlay-description').text(calEvent.description);

            overlay.removeClass('left right top').find('.arrow').removeClass('left right top pull-up');
            var wrap = $(jsEvent.target).closest('.fc-event');
            var cal = wrap.closest('#calendar');
            var left = wrap.offset().left - cal.offset().left;
            var right = cal.width() - (wrap.offset().left - cal.offset().left + wrap.width());
            var top = cal.height() - (wrap.offset().top - cal.offset().top + wrap.height());
            if(right > overlay.width()) {
                overlay.addClass('left').find('.arrow').addClass('left pull-up')
            }else if (left > overlay.width()) {
                overlay.addClass('right').find('.arrow').addClass('right pull-up');
            }else{
                overlay.find('.arrow').addClass('top');
            }
            if(top < overlay.height()) {
                overlay.addClass('top').find('.arrow').removeClass('pull-up').addClass('pull-down')
            }
            (wrap.find('.fc-overlay').length == 0) && wrap.append(overlay);
            */
        }
	});
    
    calendar.find('.fc-right').append('<button data-calendar="help" class="fc-button fc-state-default fc-corner-left fc-corner-right"><i class="fa fa-info-circle"></i> 帮助</button>')
}

function help() {
    $('#calendar-help').__dialog({
        title: '帮助',
        onShow: function() {
            var self = this;
            $.get('{{url("help")}}',function(res) {
                self.html(res);
            });
        }
    });
}

function timeLine(curView) {

    var curTime = new Date();
    var parentDiv = $(".fc-slats:visible").parent();

    var cur = moment(curTime).format();

    var timeline = parentDiv.children(".timeline");
    if (timeline.length == 0) {
        //添加时间线标签
        timeline = $("<hr>").addClass("timeline");
        parentDiv.prepend(timeline);
    }

    if (curView.start.format() < cur && curView.end.format() > cur) {
        timeline.show();
    } else {
        timeline.hide();
    }
    var curSeconds = (curTime.getHours() * 60 * 60) + (curTime.getMinutes() * 60) + curTime.getSeconds();
    //24 * 60 * 60 = 86400, # of seconds in a day
    var percentOfDay = curSeconds / 86400;
    var topPos = Math.floor(parentDiv.height() * percentOfDay);

    timeline.css("top", topPos+"px");

    // 周视图时设置时间线的位置和宽度
    if (curView.name == "agendaWeek") {

        var dayCol = $(".fc-today:visible");

        if(dayCol.position()) {
            var left = dayCol.position().left;
            var width = dayCol.width();
            timeline.css({
                left:left + "px",
                width:width + "px"
            });
        }
    }
}

function viewEvent(event, jsEvent, view)
{
    var data = dateFormat(event);
    data.id = event.id;
    
	$(jsEvent).__dialog({
        title: '查看事件',
        onShow:function() {
        	var self = this;
        	$.get('{{url("event/view")}}?'+$.param(data), function(res) {
				self.html(res);
    		});
        }
    });
}

function addEvent(event, jsEvent, view)
{
    var data = dateFormat(event);

    $(jsEvent).__dialog({
        title:'事件管理',
        url:app.url('calendar/event/add', data),
        buttons:[{
            text: '提交',
            'class': 'btn-primary',
            click: function() {
                var $this = $(this);
                if ($('#title').val().length == '') {
                    $.toastr('error', '主题必须填写。');
                    return false;
                }
                var myform = $('#myform').serialize();
                $.post('{{url("event/add")}}',myform, function(res) {
                    if (res.status) {
                        calendar.fullCalendar('refetchEvents');
                        $.toastr('success', '事件已保存。');
                        $this.dialog('close');
                    } else {
                        $.toastr('error', '事件未保存。');
                    }
                },'json');
            }
        },{
            text: '取消',
            'class': 'btn-default',
            click: function() {
                $(this).dialog('close');
            }
        }]
    });
}

function resizeEvent(event, delta, revertFunc)
{
    var data = {
        id: event.id,
        delta:delta.asSeconds(),
        lastmodified: event.lastmodified
    };

    $.post('{{url("event/resize")}}', data, function(r) {
		if (r.status) {
			calendar.fullCalendar('refetchEvents');
            $.toastr('success', '事件已保存。');
		} else {
            $.toastr('error', '事件未保存。');
			revertFunc();
		}
	},'json');
}

function moveEvent(event, delta, revertFunc)
{
    var data = {
        id:event.id,
        allday:event.allDay,
        delta:delta.asSeconds(),
        lastmodified:event.lastmodified
    };
	$.post('{{url("event/move")}}', data, function(res) {
		if (res.status) {
			calendar.fullCalendar('refetchEvents');
            $.toastr('success', '事件已保存。');
		} else {
            $.toastr('error', '事件未保存。');
			revertFunc();
		}
	},'json');
}

function editEvent(event, jsEvent, view)
{
    var data = dateFormat(event);
    data.id     = event.id;
    data.title  = event.title;
    data.location = event.location;
    data.description = event.description;
    data.lastmodified = event.lastmodified;
    
    $(jsEvent).__dialog({
        title:'事件管理',
        url:app.url('calendar/event/edit', data),
        buttons:[{
            text: '提交',
            'class': 'btn-primary',
            click: function() {

                var $this = $(this);
                if ($('#title').val().length == '') {
                    $.toastr('error', '主题必须填写。');
                    return false;
                }

                var myform = $('#myform').serialize();
                $.post('{{url("event/edit")}}', myform, function(res) {
                    if (res.status) {
                        calendar.fullCalendar('refetchEvents');
                        $.toastr('success', '事件已保存。');
                        $this.dialog('close');
                    } else {
                        $.toastr('error', '事件未保存。');
                    }
                },'json');
            }
        },{
            text: '删除',
            'class': 'btn-danger',
            click: function() {
                var $btn = $(this);

                $.messager.confirm('操作确认', '确定要删除事件吗?', function() {
                    $.post('{{url("event/delete")}}', data, function(res) {
                        if (res.status) {
                            $.toastr('success', res.data);
                            calendar.fullCalendar('refetchEvents');
                            $btn.dialog('close');
                        }
                    },'json');
                });
            }
        },{
            text: '取消',
            'class': 'btn-default',
            click: function() {
                $(this).dialog('close');
            }
        }]
    });
}

function editCalendar(id)
{
	$('#calendar-edit').__dialog({
        title: '日历管理',
        modalClass:'',
        dialogClass:'modal-xs',
        onShow:function() {
        	var self = this;
        	$.get('{{url("add")}}?id='+id, function(res) {
				self.html(res);
    		});
        },
        buttons: [{
            text: '确定',
            'class': 'btn-primary',
            click: function() {
                var $btn = $(this);
                var data = $('#myform').serialize();
                $.post('{{url("add")}}', data, function(res) {
                    if (res.status) {
                        getCalendars();
                        $.toastr('success', '日历已保存。');
                        $btn.dialog('close');
                    } else {
                        $.toastr('error', '日历未保存。');
                    }
                },'json');
            }
        },{
            text: '删除',
            'class': 'btn-danger',
            click: function() {
                var $btn = $(this);
                $.post("{{url('delete')}}", {id:id}, function(res) {
                    if (res.status) {
                        getCalendars();
                        $.toastr('success', '日历删除完成。');
                        calendar.fullCalendar('refetchEvents');
                        $btn.dialog('close');
                    }
                },'json');
            }
        },{
            text: '取消',
            'class': 'btn-default',
            click: function() {
                $(this).dialog('close');
            }
        }]
    });
}

function getCalendars(callback)
{
    var rows = [];
    var home = user_id == auth_id ? '<a href="javascript:;" data-calendar="edit" id="0" title="新建日历"><i class="icon icon-plus"></i> 新建</a>' : '<a href="{{url("index")}}" title="返回我的日历">[返回]</a>';
    
    $.get("{{url('calendars',['user_id'=>$user->id])}}", function(res) {

        rows.push('<ul class="list-group"><li class="list-group-item"><span class="edit pull-right">'+home+'</span><strong>{{$user->nickname}}的日历</strong></li>');

        $.each(res.data.calendars, function() {

            var edit = '';
            if(this.userid == auth_id) {
                edit = '<span class="pull-right"><a href="javascript:;" data-calendar="edit" data-id="'+this.id+'"><i class="fa fa-pencil"></i> 编辑</a></span>';
            }
            if(this.active == 1) {
                var checkbox = 'checked';
            }
            if(this.id == 'shared') {
                var checkbox = 'checked disabled';
            }
            rows.push('<li class="list-group-item">'+ edit +'<label class="checkbox-inline"><input data-calendar="active" data-id="'+this.id+'" type="checkbox" '+checkbox+' /> <span class="text" style="color:'+this.calendarcolor+'">'+this.displayname+'</span></label></li>');
        });
        rows.push('</ul>');
        $('#calendars').html(rows.join(''));

        if(callback) {
            callback(res.data.sources);
        }
    });
}

function activeCalendar(self)
{
    var $self  = $(self);
	var id     = $self.data('id');
	var active = $self.prop('checked') ? 1 : 0;
	$.post('{{url("active")}}',{id:id,active:active}, function(res) {
		if (res.status) {
			if (res.data.active == 1) {
				calendar.fullCalendar('addEventSource',res.data.eventSource);
			} else {
				calendar.fullCalendar('removeEventSource',res.data.eventSource.url);
			}
		}
	},'json');
}

function refreshCalendar(id)
{
	$.post('{{url("refresh")}}',{id:id},function(data) {
		if (data.id) {
			calendar.fullCalendar('removeEventSource',data.url);
			calendar.fullCalendar('addEventSource',data);
		}
	},'json');
}

function dateFormat(event)
{
    var allDay = !event.start.hasTime();
    var start  = event.start.format();
    var end    = event.end == null ? start : event.end.format();
    return {
        start:start,
        end:end,
        allDay:allDay
    };
}

function getPanelHeight() {
	var position = $('#calendar-wrapper').position();
	var iframeHeight = $(window).height();
    return iframeHeight - position.top - 28;
}

$(function() {
    $(window).resize(function() {
        $('#calendar').fullCalendar('option', 'height', getPanelHeight());
    });
});

</script>

<style type='text/css'>

#calendar { position: relative; }

.fc-toolbar { margin-bottom: 5px; }
.fc-toolbar h2 { font-size: 24px; }

#loading { background:red; color:#fff; padding:3px; position:absolute; top:5px;right:5px;z-index:9999;}

.category .tree ul {padding:5px 0;}
.category .tree ul li.me {padding:5px; color:#666;}
.category .tree ul li ul {padding:5px 0 0;}
.category .tree ul li ul li {padding:5px 5px 0 5px; border:0;border-top:1px solid #eee;}
.category .tree ul li ul li a { }

</style>

<div class="panel">

    <div class="wrapper">

    <div class="row">

        <div class="col-sm-2 padder-r-n">

            <div id="calendars"></div>

            <div class="category m-t m-b">

                <div class="tree">

                <ul class="list-group">

                    <li class="list-group-item"><strong>下属日历</strong></li>
                    
                         @if(count($underling['role']))
                         @foreach($underling['role'] as $role_id => $role)
                            <li class="list-group-item">
                                {{$role['layer_html']}}{{$role['title']}}
                                <ul>
                                     @if(count($underling['user'][$role_id]))
                                     @foreach($underling['user'][$role_id] as $user)
                                        <li class="list-group-item"><a href="{{url('index',['user_id'=>$user['id']])}}">{{$user['nickname']}}</a></li>
                                     @endforeach
                                     @endif
                                </ul>
                            </li>
                         @endforeach
                         @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-sm-10" id="calendar-wrapper">
            <div id='loading' style='display:none;'>加载数据...</div>
            <!--
            <div class="fc-overlay">
                <div class="panel bg-white b-a pos-rlt">
                    <span class="arrow"></span>
                    <div class="h4 font-thin m-b-sm" id="overlay-title"></div>
                    <div class="line b-b b-light"></div>
                    <div><i class="fa fa-calendar text-muted m-r-xs"></i><span id="overlay-start"></span></div>
                    <div><i class="fa fa-clock-o text-muted m-r-xs"></i><span id="overlay-end"></span></div>
                    <div><i class="fa fa-map-marker text-muted m-r-xs"></i><span id="overlay-location"></span></div>
                    <div class="m-t-sm" id="overlay-description"></div>
                    <div class="m-t-sm" id="overlay-url"></div>
                </div>
            </div>
            -->
            <div id='calendar'></div>
        </div>
    </div>

    </div>

</div>
