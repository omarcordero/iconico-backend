@extends('admin.layouts.admin')

@section('title', 'Add User' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.users.store'],'method' => 'post','class'=>'form-horizontal form-label-left']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                    Name
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" type="text"
                           class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" required>
                    @if($errors->has('name'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('name') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                    Email
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="email" type="email"
                           class="form-control col-md-7 col-xs-12 @if($errors->has('email')) parsley-error @endif"
                           name="email" required>
                    @if($errors->has('email'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('email') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile_number">
                    Mobile
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="mobile_number" type="text"
                           class="form-control col-md-7 col-xs-12 @if($errors->has('mobile_number')) parsley-error @endif"
                           name="mobile_number" required>
                    @if($errors->has('mobile_number'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('mobile_number') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="mobile_verified">
                    Mobile Verified
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="checkbox">
                        <label>
                            <input id="mobile_verified" type="checkbox" class="@if($errors->has('mobile_verified')) parsley-error @endif"
                                   name="mobile_verified" value="1">
                            @if($errors->has('mobile_verified'))
                                <ul class="parsley-errors-list filled">
                                    @foreach($errors->get('mobile_verified') as $error)
                                        <li class="parsley-required">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </label>
                    </div>
                </div>
            </div>

            {{--@if(!$user->hasRole('administrator'))--}}

            {{--<div class="form-group">--}}
            {{--<label class="control-label col-md-3 col-sm-3 col-xs-12" for="confirmed" >--}}
            {{--{{ __('views.admin.users.edit.confirmed') }}--}}
            {{--</label>--}}
            {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
            {{--<div class="checkbox">--}}
            {{--<label>--}}
            {{--<input id="confirmed" type="checkbox" class="@if($errors->has('confirmed')) parsley-error @endif"--}}
            {{--name="confirmed" @if($user->confirmed) checked="checked" @endif value="1">--}}
            {{--@if($errors->has('confirmed'))--}}
            {{--<ul class="parsley-errors-list filled">--}}
            {{--@foreach($errors->get('confirmed') as $error)--}}
            {{--<li class="parsley-required">{{ $error }}</li>--}}
            {{--@endforeach--}}
            {{--</ul>--}}
            {{--@endif--}}
            {{--</label>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                    Password
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password" type="password"
                           class="form-control col-md-7 col-xs-12 @if($errors->has('password')) parsley-error @endif"
                           name="password" required>
                    @if($errors->has('password'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('password') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">
                    Confirm Password
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password_confirmation" type="password"
                           class="form-control col-md-7 col-xs-12 @if($errors->has('password_confirmation')) parsley-error @endif"
                           name="password_confirmation" required>
                    @if($errors->has('password_confirmation'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('password_confirmation') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="roles">
                    Roles
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="roles" name="role" class="select2" style="width: 100%"
                            autocomplete="off" required>
                        <option value="">Select</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary"
                       href="{{ URL::previous() }}"> {{ __('views.admin.users.edit.cancel') }}</a>
                    <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
@endsection