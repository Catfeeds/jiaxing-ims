<li class="dropdown hidden-xs" id="notification-app">

    <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle">
        <i class="fa fa-bell-o notify-box">
            <span class="pulse" v-if="count.total > 0"></span>
        </i>
        <span class="visible-xs-inline">通知</span>
    </a>

    <div class="dropdown-menu w-xl">

        <div class="panel bg-white">

            <div class="list-group no-radius">

                <a href="javascript:;" v-if="count.article > 0" class="list-group-item">
                    <span class="pull-left thumb-sm">
                        <i class="fa fa-bullhorn fa-2x text-danger"></i>
                    </span>
                    <span class="block m-b-none">
                        <span><span class="text-danger pull-right-xs">0</span> 条未读公告</span>
                        <br>
                        <small class="text-muted text-xs">点击阅读</small>
                    </span>
                </a>

                <a href="javascript:;" v-if="count.mail > 0" class="list-group-item">
                    <span class="pull-left thumb-sm">
                        <i class="fa fa-envelope-o fa-2x text-success"></i>
                    </span>
                    <span class="block m-b-none">
                        <span><span class="text-danger pull-right-xs">2</span> 条未读邮件</span>
                        <br>
                        <small class="text-muted text-xs">点击阅读</small>
                    </span>
                </a>

                <a href="javascript:addTab('<?php echo url('index/notification/index'); ?>', '00', '通知提醒');" title="通知提醒" v-if="count.notification > 0" class="list-group-item">
                    <span class="pull-left thumb-sm">
                        <i class="fa fa-bell-o fa-2x text-info"></i>
                    </span>
                    <span class="block m-b-none">
                        <span><span class="text-danger pull-right-xs">{{count.notification}}</span> 条未读通知</span>
                        <br>
                        <span class="text-muted text-xs">点击阅读</span>
                    </span>
                </a>

            </div>

            <div class="panel-footer text-sm">
                <a href class="pull-right"><i class="fa fa-cog"></i></a>
                <a href="#">提醒设置</a>
            </div>

        </div>
    </div>
</li>

<script src="<?php echo $asset_url; ?>/vendor/vue.min.js"></script>
<script type="text/javascript">
new Vue({
    el: '#notification-app',
    data: {
        count: {
            total: 0,
            notification: 0
        }
    },
    methods: {
        nicetime: function(at) {
            return niceTime(at);
        },
        getItems: function() {
            var me = this;
            var notification = 0;
            $.get(app.url('index/notification/count'), function(count) {
                if(notification == count) {
                    return;
                }
                me.count.total  = count;
                me.count.notification = count;
            }, 'json');
        }
    },
    mounted: function() {

        this.$nextTick(function () {
            // 保证 this.$el 已经插入文档
        });

        var me = this;
        me.getItems();

        setInterval(function() {
            me.getItems();
        }, 10000);

    }
});
</script>