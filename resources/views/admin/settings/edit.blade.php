@extends('admin.layouts.admin')

@section('title', 'Edit "' . $setting->key . '"' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.settings.update', $setting->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="key" >
                    Key
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="key" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('key')) parsley-error @endif"
                           name="key" value="{{ $setting->key }}" readonly>
                    @if($errors->has('key'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('key') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="value" >
                    Value
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="value" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('value')) parsley-error @endif"
                           name="value" value="{{ $setting->value }}" required>
                    @if($errors->has('value'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('value') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ URL::previous() }}"> {{ __('views.admin.users.edit.cancel') }}</a>
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