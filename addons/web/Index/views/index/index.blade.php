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
            <a href="#tab_home" aria-controls="0" data-toggle="tab" role="tab">
                <i class="fa fa-square-o"></i> <span>个人空间</span>
            </a>
        </li>
    </ul>

    <!-- 通知模块 -->
    <div id="notification"></div>

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
              <span class="text-avatar text-muted text-xs block m-t-xs"><?php echo Auth::user()->name; ?></span>
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

                                        @if($group['children'])
                                        <span class="pull-right">
                                            <i class="fa fa-fw fa-angle-right text"></i>
                                            <i class="fa fa-fw fa-angle-down text-active"></i>
                                        </span>
                                        @endif

                                        {{$group['name']}}

                                    </a>

                                    @if($group['children'])
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
    <script src="{{$asset_url}}/dist/bundle.min.js" type="text/javascript"></script>
</body>
</html>
