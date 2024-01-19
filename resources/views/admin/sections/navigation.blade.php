<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title" style="text-align: center">
                @if(config('app.logo'))
                    <img src="{{config('app.logo')}}" style="width: 72px; height: 50px;"/>
                @else
                    <span>&nbsp;{{ config('app.name') }}</span>
                @endif
            </a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
    {{--<div class="profile clearfix">--}}
    {{--<div class="profile_pic">--}}
    {{--<img src="{{ auth()->user()->avatar }}" alt="..." class="img-circle profile_img">--}}
    {{--</div>--}}
    {{--<div class="profile_info">--}}
    {{--<h2>{{ auth()->user()->name }}</h2>--}}
    {{--</div>--}}
    {{--</div>--}}
    <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_0') }}</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">

                    @if(auth()->user()->hasRole('administrator'))

                        <li>
                            <a href="{{ route('admin.users') }}">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_1') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.coupons') }}">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                                Coupons
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.supports') }}">
                                <i class="fa fa-life-ring" aria-hidden="true"></i>
                                Support Requests
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings') }}">
                                <i class="fa fa-cogs" aria-hidden="true"></i>
                                Settings
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.delivery_profiles') }}">
                                <i class="fa fa-bicycle" aria-hidden="true"></i>
                                Delivery Profiles
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories') }}">
                                <i class="fa fa-tags" aria-hidden="true"></i>
                                Categories
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('admin.stores') }}">
                            <i class="fa fa-key" aria-hidden="true"></i>
                            Stores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders') }}">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.ratings') }}">
                            <i class="fa fa-star" aria-hidden="true"></i>
                            Ratings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.menuitems') }}">
                            <i class="fa fa-coffee" aria-hidden="true"></i>
                            Menu Items
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.bankdetails') }}">
                            <i class="fa fa-bank" aria-hidden="true"></i>
                            Bank Details
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.earnings') }}">
                            <i class="fa fa-money" aria-hidden="true"></i>
                            Earnings
                        </a>
                    </li>
                </ul>
            </div>
            {{--<div class="menu_section">--}}
                {{--<h3>Administration</h3>--}}
                {{--<ul class="nav side-menu">--}}
                    {{--<li>--}}
                        {{--<a href="{{ url(config('dotenveditor.route')) }}" target="_blank">--}}
                            {{--<i class="fa fa-user" aria-hidden="true"></i>--}}
                            {{--Configuration Editor--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
