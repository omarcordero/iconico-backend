<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false">
                        {{ auth()->user()->name }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="fa fa-sign-out pull-right"></i> {{ __('views.backend.section.header.menu_0') }}
                            </a>
                        </li>
                    </ul>
                </li>
                @if(Auth::user()->hasRole('administrator'))
                    <li role="presentation" class="dropdown" id="user-notifications">
                        <a href="javascript;;" class="dropdown-toggle info-number" data-toggle="dropdown"
                           aria-expanded="false" title="New Users Notifications" id="user-notifications-container">
                            <i class="fa fa-user-o"></i>
                            <span class="badge bg-green" id="user-notifications-count">{{Auth::user()->unreadNotifications->where('type', 'App\Notifications\Admin\NewUser')->count()}}</span>
                        </a>
                        <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                            <li>
                                <div class="text-center">
                                    <a id="delete-user-notifications">
                                        <strong>Clear All</strong>
                                    </a>
                                </div>
                            </li>
                            @foreach(Auth::user()->notifications->where('type', 'App\Notifications\Admin\NewUser') as $notification)
                                <li class="user-notifications-row">
                                    <a href="{{ route('admin.users.show', ['user' => $notification->data['user_id']]) }}">
                                    <span>
                                        <span>New {{ title_case($notification->data['role']) }} Registered</span>
                                        <span class="time">{{$notification->created_at->diffForHumans()}}</span>
                                    </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <li role="presentation" class="dropdown" id="order-notifications">
                        <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown"
                           aria-expanded="false" title="New Orders Notifications" id="order-notifications-container">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge bg-green" id="order-notifications-count">{{Auth::user()->unreadNotifications->where('type', 'App\Notifications\Admin\NewOrder')->count()}}</span>
                        </a>
                        <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                            <li>
                                <div class="text-center">
                                    <a id="delete-order-notifications" href="#">
                                        <strong>Clear All</strong>
                                    </a>
                                </div>
                            </li>
                            @foreach(Auth::user()->notifications->where('type', 'App\Notifications\Admin\NewOrder') as $notification)
                                <li class="order-notifications-row">
                                    <a href="{{ route('admin.orders.show', ['order' => $notification->data['order_id']]) }}">
                                    <span>
                                        <span>New Order Placed</span>
                                        <span class="time">{{$notification->created_at->diffForHumans()}}</span>
                                    </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</div>