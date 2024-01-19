@extends('layouts.app')

@section('body_class','nav-md')

@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('admin.sections.navigation')
                @include('admin.sections.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                    </div>
                    @if(Breadcrumbs::exists())
                        <div class="title_right">
                            <div class="pull-right">
                                {!! Breadcrumbs::render() !!}
                            </div>
                        </div>
                    @endif
                </div>
                @yield('content')
            </div>

            <footer>
                @include('admin.sections.footer')
            </footer>
            <audio id="notification-sound" src="{{ asset('assets/admin/notification.mp3') }}" preload="auto"></audio>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}

    <script type="text/javascript">
        window.PUSHER_APP_KEY = "{{env('PUSHER_APP_KEY')}}";
        window.PUSHER_APP_CLUSTER = "{{env('PUSHER_APP_CLUSTER')}}";
        window.URL_NOTIFICATION_MARK_AS_READ = "{{route('admin.json.notifications.read')}}";
        window.URL_NOTIFICATION_DELETE = "{{route('admin.json.notifications.delete')}}";
        window.ADMIN_USER_ID = parseInt("{{Auth::user()->hasRole('administrator') ? Auth::user()->id : 0 }}");
        window.BROADCAST_AUTH_ENDPOINT = "{{url('/') . '/broadcasting/auth'}}"
    </script>
    {{ Html::script(mix('assets/admin/js/init.js')) }}
@endsection