<div class="wrapper b-t">
    <style>
        .o_cp_searchview {
            position: relative;
            display: flex;
        }

        .o_cp_searchview>.btn-group>.btn-sm {
            padding: 5px 7px 5px 3px;
            border-right-width: 0;
        }

        .o_searchview {
            display: -ms-flexbox;
            display: -moz-box;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -ms-flex-flow: row wrap;
            -moz-flex-flow: row wrap;
            -webkit-flex-flow: row wrap;
            flex-flow: row wrap;
            -webkit-align-items: flex-end;
            align-items: flex-end;
            position: relative;
            padding: 2px 0 3px 0;
            flex: 1 1 auto;
        }

        .o_searchview .o_searchview_facet {
            -ms-flex: 0 0 auto;
            -moz-flex: 0 0 auto;
            -webkit-box-flex: 0;
            -webkit-flex: 0 0 auto;
            flex: 0 0 auto;
            max-width: 100%;
            display: -ms-flexbox;
            display: -moz-box;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            position: relative;
            margin: 1px 3px 0 0;
        }

        .o_searchview .o_searchview_facet .o_searchview_facet_label {
            -ms-flex: 0 0 auto;
            -moz-flex: 0 0 auto;
            -webkit-box-flex: 0;
            -webkit-flex: 0 0 auto;
            flex: 0 0 auto;
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: top;
            padding: 0 3px;
            color: white;
            display: -ms-flexbox;
            display: -moz-box;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-align-items: center;
            align-items: center;
        }

        .o_searchview .o_searchview_facet .o_facet_values {
            padding: 0 18px 0 5px;
        }

        .o_searchview .o_searchview_facet .o_facet_values .o_facet_values_sep {
            font-style: italic;
        }

        .o_searchview .o_searchview_facet .o_facet_remove {
            -ms-flex: 0 0 auto;
            -moz-flex: 0 0 auto;
            -webkit-box-flex: 0;
            -webkit-flex: 0 0 auto;
            flex: 0 0 auto;
            display: -ms-flexbox;
            display: -moz-box;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -moz-justify-content: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-align-items: center;
            align-items: center;
            position: absolute;
            top: -1px;
            left: auto;
            bottom: 0;
            right: 0;
            width: 18px;
            cursor: pointer;
        }

        .o_searchview .o_searchview_input {
            -ms-flex: 1 0 auto;
            -moz-flex: 1 0 auto;
            -webkit-box-flex: 1;
            -webkit-flex: 1 0 auto;
            flex: 1 0 auto;
            padding-bottom: 3px;
        }

        .o_searchview .o_searchview_more {
            font-size: 16px;
            cursor: pointer;
        }

        .o_searchview .o_searchview_autocomplete {
            position: absolute;
            top: 100%;
            left: -1px;
            bottom: auto;
            right: -1px;
            /*
            width: 100%;
            */
        }

        .o_searchview .o_searchview_autocomplete li {
            padding-left: 25px;
            position: relative;
        }

        .o_searchview .o_searchview_autocomplete li.o-indent {
            padding-left: 50px;
        }

        .o_searchview .o_searchview_autocomplete li a {
            display: inline-block;
            padding-left: 0px;
            padding-right: 0px;
        }

        .o_searchview .o_searchview_autocomplete li a:hover {
            background-color: inherit;
        }

        .o_searchview .o_searchview_autocomplete li a.o-expand,
        .o_searchview .o_searchview_autocomplete li a.o-expanded {
            position: absolute;
            top: auto;
            left: 6px;
            bottom: auto;
            right: auto;
            padding: 3px;
        }

        .o_searchview .o_searchview_autocomplete li a.o-expand:before {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
            border-bottom: 4px solid transparent;
            border-left: 4px solid;
            border-right: 0;
            border-top: 4px solid transparent;
            -moz-transform: scale(0.9999);
        }

        .o_searchview .o_searchview_autocomplete li a.o-expanded:before {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
            border-bottom: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid;
            -moz-transform: scale(0.9999);
        }

        .o_dropdown {
            white-space: nowrap;
            display: inline-block;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .o_dropdown .dropdown-toggle:first-child {
            padding-right: 10px;
        }

        .o_dropdown>a {
            color: #4c4c4c;
        }

        .o_dropdown>a:hover {
            text-decoration: none;
            font-weight: bold;
            color: #333333;
        }

        .o_dropdown.open>a {
            font-weight: bold;
            color: #333333;
        }

        .o_search_options .o_closed_menu {
            position: relative;
        }

        .o_search_options .o_closed_menu a:before {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
            border-bottom: 4px solid transparent;
            border-left: 4px solid;
            border-right: 0;
            border-top: 4px solid transparent;
            -moz-transform: scale(0.9999);
            position: absolute;
            top: 50%;
            left: 10px;
            bottom: auto;
            right: auto;
            margin-top: -4px;
        }

        .o_search_options .o_open_menu {
            position: relative;
        }

        .o_search_options .o_open_menu a:before {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
            border-bottom: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid;
            -moz-transform: scale(0.9999);
            position: absolute;
            top: 50%;
            left: 10px;
            bottom: auto;
            right: auto;
            margin-top: -4px;
        }

        .o_search_options .o_filters_menu li {
            position: relative;
        }

        .o_search_options .o_filters_menu li>a {
            color: #4c4c4c;
        }

        .o_search_options .o_filters_menu li>a:hover {
            color: #333333;
        }

        .o_search_options .o_filters_menu li.selected>a {
            color: #333333;
            font-weight: bold;
        }

        .o_search_options .o_filters_menu li.selected>a:before {
            font-family: FontAwesome;
            position: absolute;
            left: 6px;
            content: "\f00c";
        }

        .o_search_options .o_filters_menu .o_filter_condition {
            margin: 3px 25px;
            width: auto;
            max-width: 250px;
            margin-bottom: 8px;
        }

        .o_search_options .o_filters_menu .o_filter_condition .o_or_filter {
            display: none;
            position: absolute;
            top: 7px;
            left: -18px;
            bottom: auto;
            right: auto;
        }

        .o_search_options .o_filters_menu .o_filter_condition+.o_filter_condition .o_or_filter {
            display: block;
        }

        .o_search_options .o_filters_menu .o_searchview_extended_prop_op {
            margin: 3px 0px;
        }

        .o_search_options .o_filters_menu .o_searchview_extended_prop_value .datepickerbutton {
            position: absolute;
            top: 3px;
            left: auto;
            bottom: auto;
            right: -20px;
            cursor: pointer;
        }

        .o_search_options .o_filters_menu .o_searchview_extended_delete_prop {
            position: absolute;
            top: 3px;
            left: auto;
            bottom: auto;
            right: -18px;
            cursor: pointer;
        }

        .o_search_options .o_filters_menu .o_add_filter_menu {
            display: none;
            margin: 3px 25px;
            width: auto;
            max-width: 250px;
        }

        .o_search_options .o_group_by_menu li {
            position: relative;
        }

        .o_search_options .o_group_by_menu li>a {
            color: #4c4c4c;
        }

        .o_search_options .o_group_by_menu li>a:hover {
            color: #333333;
        }

        .o_search_options .o_group_by_menu li.selected>a {
            color: #333333;
            font-weight: bold;
        }

        .o_search_options .o_group_by_menu li.selected>a:before {
            font-family: FontAwesome;
            position: absolute;
            left: 6px;
            content: "\f00c";
        }

        .o_search_options .o_group_by_menu .divider {
            display: none;
        }

        .o_search_options .o_group_by_menu .o_add_group {
            display: none;
            margin: 3px 25px;
            width: auto;
            max-width: 250px;
        }

        .o_search_options .o_favorites_menu li {
            position: relative;
        }

        .o_search_options .o_favorites_menu li>a {
            color: #4c4c4c;
        }

        .o_search_options .o_favorites_menu li>a:hover {
            color: #333333;
        }

        .o_search_options .o_favorites_menu li.selected>a {
            color: #333333;
            font-weight: bold;
        }

        .o_search_options .o_favorites_menu li.selected>a:before {
            font-family: FontAwesome;
            position: absolute;
            left: 6px;
            content: "\f00c";
        }

        .o_search_options .o_favorites_menu .divider {
            display: none;
        }

        .o_search_options .o_favorites_menu .o-searchview-custom-private .o-remove-filter,
        .o_search_options .o_favorites_menu .o-searchview-custom-public .o-remove-filter {
            position: absolute;
            top: 50%;
            left: auto;
            bottom: auto;
            right: 12px;
            margin-top: -6px;
            cursor: pointer;
        }

        .o_search_options .o_favorites_menu .o-searchview-custom-public a:after {
            font-family: FontAwesome;
            content: "\f0c0";
            margin-left: 5px;
            font-weight: normal;
        }

        .o_search_options .o_favorites_menu .o_save_name {
            display: none;
            margin: 3px 25px;
            width: auto;
            max-width: 250px;
        }

        .o_searchview {
            background-color: white;
            border: 1px solid #ccc;
            padding: 3px 25px 3px 4px;
        }

        .o_searchview .o_searchview_facet {
            border: 1px solid #777777;
            background: #e9e9f1;
            padding: 1px;
        }

        .o_searchview .o_searchview_facet .o_searchview_facet_label {
            background-color: #777777;
        }

        .o_searchview .o_searchview_facet .o_facet_remove {
            color: #777777;
        }

        .o_searchview .o_searchview_facet .o_facet_remove:hover {
            color: #555555;
        }

        .o_searchview .o_searchview_input {
            border: none;
            padding: 1px 0 2px 0;
            outline: none;
        }

        .o_searchview .o_searchview_more {
            position: absolute;
            top: 6px;
            left: auto;
            bottom: auto;
            right: 5px;
        }

        .o_searchview .o_searchview_autocomplete li.o-selection-focus {
            background-color: #7c7bad;
        }

        .o_searchview .o_searchview_autocomplete li.o-selection-focus>a {
            color: white;
        }

        .o_search_options input[type="text"],
        .o_search_options select {
            display: block;
            width: 100%;
            height: 32px;
            padding: 6px 12px;
            font-size: 13px;
            line-height: 1.42857143;
            color: #555555;
            background-color: #ffffff;
            background-image: none;
            border: 1px solid #cccccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }

        .o_search_options input[type="text"]:focus,
        .o_search_options select:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, 0.6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, 0.6);
        }

        .o_search_options input[type="text"]::-moz-placeholder,
        .o_search_options select::-moz-placeholder {
            color: #999999;
            opacity: 1;
        }

        .o_search_options input[type="text"]:-ms-input-placeholder,
        .o_search_options select:-ms-input-placeholder {
            color: #999999;
        }

        .o_search_options input[type="text"]::-webkit-input-placeholder,
        .o_search_options select::-webkit-input-placeholder {
            color: #999999;
        }

        .o_search_options input[type="text"]::-moz-placeholder,
        .o_search_options select::-moz-placeholder {
            color: #999999;
            opacity: 1;
        }

        .o_search_options input[type="text"]:-ms-input-placeholder,
        .o_search_options select:-ms-input-placeholder {
            color: #999999;
        }

        .o_search_options input[type="text"]::-webkit-input-placeholder,
        .o_search_options select::-webkit-input-placeholder {
            color: #999999;
        }

        .o_search_options input[type="text"][disabled],
        .o_search_options select[disabled],
        .o_search_options input[type="text"][readonly],
        .o_search_options select[readonly],
        fieldset[disabled] .o_search_options input[type="text"],
        fieldset[disabled] .o_search_options select {
            background-color: #eeeeee;
            opacity: 1;
        }

        .o_search_options input[type="text"][disabled],
        .o_search_options select[disabled],
        fieldset[disabled] .o_search_options input[type="text"],
        fieldset[disabled] .o_search_options select {
            cursor: not-allowed;
        }

        textarea.o_search_options input[type="text"],
        textarea.o_search_options select {
            height: auto;
        }
    </style>

    <script>

        var focus_element = function ($li) {
            autocomplete.find('li').removeClass('o-selection-focus');
            $li.addClass('o-selection-focus');
            this.current_result = $li.data('result');
        }

        var move = function (direction) {
            var $next;
            if (direction === 'down') {
                $next = autocomplete.find('li.o-selection-focus').next();
                if (!$next.length) {
                    $next = autocomplete.find('li').first();
                }
            } else {
                $next = autocomplete.find('li.o-selection-focus').prev();
                if (!$next.length) {
                    $next = autocomplete.find('li').last();
                }
            }
            focus_element($next);
        }

        var searchview_autocomplete = false;
        var searchview = null;
        var autocomplete = null;
        var o_searchview_filters = null;

        $(function () {

            searchview = $('#o_cp_searchview');
            autocomplete = searchview.find('.o_searchview_autocomplete');
            filters_menu = searchview.find('.o_filters_menu');

            var fields = [
                { field: 'name', name: '名称', 'type': 'text' },
                { field: 'abc_id', name: '销售员', 'type': 'list' },
                { field: 'bbc_id', name: '关联公司', 'type': 'list' }
            ];

            var filters = [
                { name: '启用', field: 'name', condition: '=', value: '1' },
                { name: '停用', field: 'name', condition: '=', value: '1' },
                { divider: 'true' },
                { name: '启用', field: 'name', condition: '=', value: '1' },
                { name: '停用', field: 'name', condition: '=', value: '1' },
            ];

            var html_fields = [];
            var html_filters = [];

            $.each(fields, function (k, v) {
                html_fields.push('<li class="' + (k == 0 ? 'o-selection-focus' : '') + '"><a href="javascript:;">搜索 <em>' + v.name + '</em> : <strong></strong></a></li>');
            });

            var index = 0;
            $.each(filters, function (k, v) {
                if (v.divider) {
                    html_filters.push('<li class="divider"></li>');
                    index = 0;
                } else {
                    html_filters.push('<li data-index="'+ index +'" class=""><a href="javascript:;">' + v.name + '</a></li>');
                    index ++;
                }
            });

            autocomplete.html(html_fields);
            filters_menu.prepend(html_filters);

            searchview.on('keyup', '.o_searchview_input', function (event) {

                var value = $.trim($(this).val());

                if (event.keyCode == 38) {
                    move('up');
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                }
                if (event.keyCode == 40) {
                    move('down');
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                }

                if (value == '') {

                    if (event.keyCode == 8) {
                        var searchview_facet = $('.o_searchview_facet');
                        if (searchview_facet.length && searchview_autocomplete == false) {
                            searchview_facet.eq(-1).remove();
                        }

                    }

                    if (searchview_autocomplete == true) {
                        searchview_autocomplete = false;
                        autocomplete.hide();
                    }

                } else {

                    autocomplete.find('strong').text(value);

                    if (searchview_autocomplete == false) {
                        searchview_autocomplete = true;
                        autocomplete.show();
                    }
                }

            });

        });

    </script>

    <div class="o_cp_searchview" id="o_cp_searchview">

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-filter"></span>筛选
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu o_filters_menu" role="menu">

                <li class="divider"></li>
                <li class="o_add_filter o_closed_menu">
                    <a href="javascript:;">添加自定义筛选</a>
                </li>
                <li class="o_filter_condition" style="display: none;">
                    <span class="o_or_filter">或</span>
                    <span>
                        <select class="o_searchview_extended_prop_field">

                            <option value="email">
                                Email
                            </option>

                            <option value="notify_email">
                                Email消息以及通知
                            </option>

                            <option value="id">
                                ID
                            </option>

                            <option value="partner_share">
                                Share Partner
                            </option>

                            <option value="signup_token">
                                Signup token
                            </option>

                            <option value="parent_name">
                                上级名称
                            </option>

                            <option value="image_medium">
                                中等尺寸图像
                            </option>

                            <option value="meeting_ids">
                                会议
                            </option>

                            <option value="fax">
                                传真
                            </option>

                            <option value="comment">
                                便签
                            </option>

                            <option value="message_ids">
                                信息
                            </option>

                            <option value="credit_limit">
                                信用额度
                            </option>

                            <option value="company_id">
                                公司
                            </option>

                            <option value="company_name">
                                公司名称
                            </option>

                            <option value="commercial_company_name">
                                公司名称实体
                            </option>

                            <option value="message_follower_ids">
                                关注者
                            </option>

                            <option value="message_partner_ids">
                                关注者(业务伙伴)
                            </option>

                            <option value="message_channel_ids">
                                关注者(渠道)
                            </option>

                            <option value="parent_id">
                                关联公司
                            </option>

                            <option value="ref">
                                内部参考
                            </option>

                            <option value="create_date">
                                创建于
                            </option>

                            <option value="create_uid">
                                创建者
                            </option>

                            <option value="name">
                                名称
                            </option>

                            <option value="employee">
                                员工
                            </option>

                            <option value="commercial_partner_id">
                                商业实体
                            </option>

                            <option value="opportunity_ids">
                                商机
                            </option>

                            <option value="country_id">
                                国家
                            </option>

                            <option value="image">
                                图像
                            </option>

                            <option value="type">
                                地址类型
                            </option>

                            <option value="city">
                                城市
                            </option>

                            <option value="image_small">
                                小尺寸图像
                            </option>

                            <option value="mobile">
                                手机
                            </option>

                            <option value="date">
                                日期
                            </option>

                            <option value="tz">
                                时区
                            </option>

                            <option value="supplier">
                                是供应商
                            </option>

                            <option value="is_company">
                                是公司
                            </option>

                            <option value="message_is_follower">
                                是关注者
                            </option>

                            <option value="customer">
                                是客户
                            </option>

                            <option value="display_name">
                                显示名称
                            </option>

                            <option value="write_uid">
                                最后更新人
                            </option>

                            <option value="message_last_post">
                                最后消息日期
                            </option>

                            <option value="calendar_last_notif_ack">
                                最后的提醒已经标志为已读
                            </option>

                            <option value="write_date">
                                最近更新时间
                            </option>

                            <option value="active">
                                有效
                            </option>

                            <option value="barcode">
                                条码
                            </option>

                            <option value="category_id">
                                标签
                            </option>

                            <option value="signup_type">
                                注册令牌（Token）类型
                            </option>

                            <option value="signup_expiration">
                                注册过期
                            </option>

                            <option value="user_ids">
                                用户
                            </option>

                            <option value="phone">
                                电话
                            </option>

                            <option value="state_id">
                                省/ 州
                            </option>

                            <option value="title">
                                称谓
                            </option>

                            <option value="vat">
                                税务登记证号码
                            </option>

                            <option value="website">
                                网站
                            </option>

                            <option value="function">
                                职位
                            </option>

                            <option value="child_ids">
                                联系人
                            </option>

                            <option value="street">
                                街道
                            </option>

                            <option value="street2">
                                街道 2
                            </option>

                            <option value="lang">
                                语言
                            </option>

                            <option value="opt_out">
                                退出
                            </option>

                            <option value="message_bounce">
                                退回
                            </option>

                            <option value="zip">
                                邮政编码
                            </option>

                            <option value="bank_ids">
                                银行
                            </option>

                            <option value="user_id">
                                销售员
                            </option>

                            <option value="team_id">
                                销售团队
                            </option>

                            <option value="message_needaction">
                                需要行动
                            </option>

                            <option value="channel_ids">
                                频道
                            </option>

                            <option value="color">
                                颜色索引
                            </option>

                        </select>
                        <span class="o_searchview_extended_delete_prop fa fa-trash-o"></span>
                    </span>
                    <select class="o_searchview_extended_prop_op">
                        <option value="ilike">包含</option>
                        <option value="not ilike">不包含</option>
                        <option value="=">等于</option>
                        <option value="!=">不等于</option>
                        <option value="∃">已设置</option>
                        <option value="∄">未设置</option>
                    </select>
                    <span class="o_searchview_extended_prop_value">
                        <input type="text">
                    </span>
                </li>

                <li class="o_add_filter_menu" style="display: none;">
                    <button class="btn btn-primary btn-sm o_apply_filter" type="button">应用</button>
                    <button class="btn btn-default btn-sm o_add_condition" type="button"><span class="fa fa-plus-circle"></span> 添加条件</button>
                </li>

            </ul>
        </div>

        <div class="o_searchview">

            <span class="o_searchview_more fa fa-search-minus" title="高级搜索..."></span>

            <ul class="dropdown-menu o_searchview_autocomplete" role="menu" style="display:none;">

                <li class="o-selection-focus">
                    <a href="javascript:;">搜索
                        <em>名称</em> :
                        <strong>1</strong>
                    </a>
                </li>
                <li>
                    <a href="javascript:;">搜索
                        <em>标签</em> :
                        <strong>1</strong>
                    </a>
                </li>
                <li>
                    <a class="o-expand" href="javascript:;"></a>
                    <a href="javascript:;">搜索
                        <em>销售员</em> :
                        <strong>1</strong>
                    </a>
                </li>
                <li>
                    <a class="o-expand" href="javascript:;"></a>
                    <a href="javascript:;">搜索
                        <em>关联公司</em> :
                        <strong>1</strong>
                    </a>
                </li>


            </ul>

            <div class="o_searchview_facet" tabindex="0">
                <span class="fa fa-filter o_searchview_facet_label"></span>
                <div class="o_facet_values">
                    <span>个人</span>
                </div>
                <div class="fa fa-sm fa-remove o_facet_remove"></div>
            </div>

            <div class="o_searchview_facet" tabindex="0">
                <span class="fa fa-filter o_searchview_facet_label"></span>
                <div class="o_facet_values">
                    <span>个人</span>
                </div>
                <div class="fa fa-sm fa-remove o_facet_remove"></div>
            </div>

            <input name="{{$key}}" type="text" class="o_searchview_input" placeholder="搜索..." value="{{$param}}">

        </div>

    </div>
</div>