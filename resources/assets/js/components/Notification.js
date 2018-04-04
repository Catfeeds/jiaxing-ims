import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Notification extends Component {
    constructor(props) {
        super(props)
        this.state = {
            countNotification: 0,
            countTotal: 0
        }
    }
    componentDidMount() {
        this.tick()
        this.timer = setInterval(() => this.tick(), 1000 * 60);
    }
    tick() {
        let me = this;
        $.get(app.url('index/notification/count'), function(count) {
            if(me.state.countNotification == count) {
                return;
            }
            me.setState({
                countTotal: count,
                countNotification: count
            });
        }, 'json');
    }
    render() {
        return (
            <ul className="nav navbar-nav navbar-right m-n hidden-xs nav-user">
        {/*
        <li class="dropdown">
          <a href="javascript:openIframe('{{url('index/index/dashboard')}}');" class="dropdown-toggle hidden-xs">
              <i class="fa fa-bar-chart-o"></i>
              <span>个人空间</span>
          </a>
        </li>
        */}

        <li className="dropdown hidden-xs">

                {/*
                <a href="javascript:;" data-toggle="dropdown" className="dropdown-toggle">
                    <i className="fa fa-bell-o notify-box">
                        <span className="pulse" v-if="count.total > 0"></span>
                    </i>
                    <span className="visible-xs-inline">通知</span>
                </a>
                */}

                <a href="#" data-toggle="dropdown" className="dropdown-toggle">
                    <i className="fa fa-bell-o notify-box">
                        <span className={this.state.countTotal > 0 ? 'pulse' : 'hidden'}></span>
                    </i>
                    <span className="visible-xs-inline">通知</span>
                </a>
    
                <div className="dropdown-menu w-xl">
                    <div className="panel bg-white">
                        <div className="list-group no-radius">
                            <a href="#" className={this.state.countArticle > 0 ? 'list-group-item' : 'hidden'}>
                                <span className="pull-left thumb-sm">
                                    <i className="fa fa-bullhorn fa-2x text-danger"></i>
                                </span>
                                <span className="block m-b-none">
                                    <span><span className="text-danger pull-right-xs">0</span> 条未读公告</span>
                                    <br />
                                    <small className="text-muted text-xs">点击阅读</small>
                                </span>
                            </a>
                            <a href="#" className={this.state.countMail > 0 ? 'list-group-item' : 'hidden'}>
                                <span className="pull-left thumb-sm">
                                    <i className="fa fa-envelope-o fa-2x text-success"></i>
                                </span>
                                <span className="block m-b-none">
                                    <span><span className="text-danger pull-right-xs">2</span> 条未读邮件</span>
                                    <br />
                                    <small className="text-muted text-xs">点击阅读</small>
                                </span>
                            </a>
                            <a href="#" className={this.state.countNotification > 0 ? 'list-group-item' : 'hidden'} data-toggle="addtab" data-url={app.url('index/notification/index')} data-id="00" data-name="通知提醒">
                                <span className="pull-left thumb-sm">
                                    <i className="fa fa-bell-o fa-2x text-info"></i>
                                </span>
                                <span className="block m-b-none">
                                    <span><span className="text-danger pull-right-xs">{this.state.countNotification}</span> 条未读通知</span>
                                    <br />
                                    <span className="text-muted text-xs">点击阅读</span>
                                </span>
                            </a>
                        </div>
                        <div className="panel-footer text-sm">
                            <a href="#" className="pull-right"><i className="fa fa-cog"></i></a>
                            <a href="#">提醒设置</a>
                        </div>
                    </div>
                </div>
            </li>
            <li className="dropdown">
    
            <a href="javascript:;" data-toggle="dropdown" className="dropdown-toggle clear hidden-xs" data-toggle="dropdown">
                <i className="icon icon-cog"></i>
            </a>

            {/* animated fadeInUp */}
            <ul className="dropdown-menu">
                <li>
                    <a href="javascript:;" data-toggle="addtab" data-url={app.url('user/user/profile')} data-id="02" data-name="个人资料">个人资料</a>
                </li>
                <li>
                    <a>菜单设置</a>
                </li>
                <li className="divider"></li>
                <li>
                    <a href={app.url('user/auth/logout')}>注销</a>
                </li>
            </ul>
        </li>
        </ul>
        );
    }
}

if (document.getElementById('notification')) {
    ReactDOM.render(<Notification />, document.getElementById('notification'));
}
