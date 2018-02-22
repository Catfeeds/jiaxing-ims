<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>{{$setting['title']}}</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="stylesheet" href="{{$asset_url}}/dist/index.min.css" type="text/css" />
<script src="{{$public_url}}/common" type="text/javascript"></script>
<script src="{{$asset_url}}/dist/index.min.js" type="text/javascript"></script>
<!--[if lt IE 9]>
<script src="{{$asset_url}}/libs/html5shiv.js"></script>
<script src="{{$asset_url}}/libs/respond.min.js"></script>
<script src="{{$asset_url}}/libs/excanvas.js"></script>
<![endif]-->

<script>
$(function() {
    $("#chats").on("click", function() {
        $(this).parent('li').toggleClass('open');
    });
});
</script>

</head>
<body class="theme-{{auth()->user()->theme ?: 'blue'}}">

	<header class="header navbar">

      <div class="navbar-header">

          <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
            <i class="icon icon-cog"></i>
          </a>
          
          <a href="{{url('')}}" class="navbar-brand">
            <!--
            <img src="{{$asset_url}}/images/logo.png" class="m-r-sm">
            -->
            <i class="fa text-lg fa-buysellads"></i> {{$setting['title']}}
          </a>

          <a class="btn btn-link visible-xs nav-trigger" data-target="#nav">
            <span></span>
          </a>

      </div>

     <ul class="nav navbar-nav tabs-list hidden-xs" id="tabs-list">
        <li role='presentation'>
            <a href="#tab_0" aria-controls="0" data-toggle="tab" role="tab">
                <i class="fa fa-square-o"></i> <span>个人空间</span>
            </a>
        </li>
        <!--
        <li>
            <a href="#tab_002"><span>个人空间</span></a>
        </li>
        <li>
            <a href="#tab_003"><span>个人空间</span></a>
        </li>
        -->
        </ul>
	  
      <ul class="nav navbar-nav navbar-right m-n hidden-xs nav-user">

        <!--
        <li class="dropdown">
          <a href="javascript:openIframe('{{url('index/index/dashboard')}}');" class="dropdown-toggle hidden-xs">
              <i class="fa fa-bar-chart-o"></i>
              <span>个人空间</span>
          </a>
        </li>
        -->

    <li class="dropdown hidden-xs hidden">

        <a href="javascript:;" id="chats" class="dropdown-toggle">
            <i class="fa fa-weixin notify-box">
                <span class="pulse" v-if="count.total > 0"></span>
            </i>
            <span class="visible-xs-inline">消息</span>
        </a>

        <style>
        .tab-content {
            background-color: #fff;
            padding-top: 2px;
        }
        .tab-content .table {
            margin-bottom: 0;
        }
        .nav-tabs {
            margin-top: 10px;
            padding-left: 10px;
        }
        .nav-tabs li > a:hover { color:#666; }
        </style>

        <div class="dropdown-menu app-aside-right no-padder w-xl w-auto-xs bg-white b-l animated fadeInRight">

            <div class="wrapper b-b b-light">
                <form class="m-b-none form-horizontal">
                    <input type="text" class="form-control" placeholder="搜索联系人/会话/组织架构">
                </form>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#chat_session" title="goods" aria-controls="chat_session" role="tab" data-toggle="tab"><i class="fa fa-comment-o"></i> 会话</a></li>
                <li role="presentation"><a href="#chat_org" title="goods" aria-controls="chat_org" role="tab" data-toggle="tab"><i class="fa fa-comment-o"></i> 组织架构</a></li>
                <li role="presentation"><a href="#chat_group" title="shop" aria-controls="chat_group" role="tab" data-toggle="tab"><i class="fa fa-group"></i> 群组</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="chat_session">
                    
                </div>
                <div role="tabpanel" class="tab-pane" id="chat_org">
                    
                </div>
                <div role="tabpanel" class="tab-pane" id="chat_group">
                    
                </div>
            </div>
            
            
            <div class="vbox">
            <div class="wrapper b-b b-t b-light m-b">
                <a href="" class="pull-right text-muted text-md active" ui-toggle-class="show" target=".app-aside-right"><i class="icon-close"></i></a>
                Chat
            </div>
            <div class="row-row">
                <div class="cell">
                <div class="cell-inner padder">
                    <!-- chat list -->
                    <div class="m-b">
                    <a href="" class="pull-left thumb-xs avatar"><img src="{{$asset_url}}/images/a1.jpg" alt="..."></a>
                    <div class="clear">
                        <div class="pos-rlt wrapper-sm b b-light r m-l-sm">
                        <span class="arrow left pull-up"></span>
                        <p class="m-b-none">Hi John, What's up...</p>
                        </div>
                        <small class="text-muted m-l-sm"><i class="fa fa-ok text-success"></i> 2 minutes ago</small>
                    </div>
                    </div>
                    <div class="m-b">
                    <a href="" class="pull-right thumb-xs avatar"><img src="{{$asset_url}}/images/a1.jpg" class="img-circle" alt="..."></a>
                    <div class="clear">
                        <div class="pos-rlt wrapper-sm bg-light r m-r-sm">
                        <span class="arrow right pull-up arrow-light"></span>
                        <p class="m-b-none">Lorem ipsum dolor :)</p>
                        </div>
                        <small class="text-muted">1 minutes ago</small>
                    </div>
                    </div>
                    <div class="m-b">
                    <a href="" class="pull-left thumb-xs avatar"><img src="{{$asset_url}}/images/a1.jpg" alt="..."></a>
                    <div class="clear">
                        <div class="pos-rlt wrapper-sm b b-light r m-l-sm">
                        <span class="arrow left pull-up"></span>
                        <p class="m-b-none">Great!</p>
                        </div>
                        <small class="text-muted m-l-sm"><i class="fa fa-ok text-success"></i>Just Now</small>
                    </div>
                    </div>
                    <!-- / chat list -->
                </div>
                </div>
            </div>
            <div class="wrapper m-t b-t b-light">
                <form class="m-b-none ng-pristine ng-valid">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Say something">
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button">SEND</button>
                    </span>
                </div>
                </form>
            </div>
            </div>
        </div>
    </li>
        
      @include('layouts/notification')

        <li class="dropdown">
    
            <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle clear hidden-xs" data-toggle="dropdown">
                <i class="icon icon-cog"></i>
            </a>

            <!-- animated fadeInUp -->
            <ul class="dropdown-menu">
                <li>
                    <a href="javascript:;" data-toggle="addtab" data-url="{{url('user/user/profile')}}" data-id="02" data-name="个人资料">个人资料</a>
                </li>
                <li>
                    <a>菜单设置</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="<?php echo url('user/auth/logout'); ?>">注销</a>
                </li>
            </ul>
        </li>
      </ul>

    </header>
	
	<div class="nav-scroll">

		<div class="side-nav" id="tabs-left">

          <div class="side-nav-avatar">

                <a href="javascript:;" data-toggle="side-folded" class="folded">
                    <i class="fa fa-angle-left text"></i> 
                    <i class="fa fa-angle-right text-active"></i>
                </a>

              <span class="thumb-md avatar">
                <a href="javascript:;" data-toggle="addtab" data-url="{{url('user/user/profile')}}" data-id="02" data-name="个人资料">
                <img src="{{avatar()}}" class="img-circle">
                <i class="on md b-white bottom"></i>
                </a>
              </span>
              <span class="text-avatar text-muted text-xs block m-t-xs"><?php echo Auth::user()->nickname; ?></span>
          </div>

			<ul>
            <?php $i = 0; ?>
			@foreach($menus['children'] as $menu)
      
				@if($menu['selected'])
				<li class="has-children">
					<a href="javascript:;" class="a{{$i}}" title="{{$menu['name']}}">

                        <span class="pull-right">
                            <i class="fa fa-fw fa-angle-right text"></i>
                            <i class="fa fa-fw fa-angle-down text-active"></i>
                        </span>

                        <i class="fa {{$menu['icon']}}"></i>
                        
                        <span class="title">{{$menu['name']}}</span>
                    </a>
					<ul>
						@foreach($menu['children'] as $groupId => $group)

							@if($group['selected'])

								<li class="has-children">
									<a href="javascript:;" data-toggle="addtab" data-url="{{url($group['url'])}}" data-id="{{$group['id']}}" data-name="{{$group['name']}}">

                                        @if(count($group['children']))
                                        <span class="pull-right">
                                            <i class="fa fa-fw fa-angle-right text"></i>
                                            <i class="fa fa-fw fa-angle-down text-active"></i>
                                        </span>
                                        @endif

                                        {{$group['name']}}

                                    </a>

                                    @if(count($group['children']))
                                    <ul>
                                    @foreach($group['children'] as $action)
                                    @if($action['selected'])
										<li class="@if($action['active']) @endif">
                                            <a href="javascript:;" data-toggle="addtab" data-url="{{url($action['url'])}}" data-id="{{$action['id']}}" data-name="{{$action['name']}}">
                                                {{$action['name']}}
                                            </a>
                                        </li>
									  @endif
                                    @endforeach
                                    
                                    </ul>
                                    @endif

								</li>

							@endif
							
						@endforeach
					</ul>
				</li>
                <?php $i++; ?>
				@endif
			@endforeach
			</ul>
			<ul>
			<li class="label">个人</li>
            <li>
                <a href="javascript:;" data-toggle="addtab" data-url="{{url('index/notification/index')}}" data-id="00" data-name="通知提醒" title="通知提醒">
                    <i class="fa fa-bell"></i>
                    <span>通知提醒</span>
                    <!--
                    <span class="count badge bg-info">3</span>
                    -->
                </a>
            </li>
			</ul>
		</div>

        </div>

        <div class="main-content" id="tabs-content">
            <div role="tabpanel" class="tab-pane active" id="tab_pane_0">
                <iframe src="{{$url}}" id="tab_iframe_0" frameBorder=0 scrolling=auto width="100%" height="100%"></iframe>
            </div>
        </div>

</body>
</html>
