@extends('admin.layouts.admin')

@section('title', 'Edit' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.coupons.update', $coupon->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="code" >
                    Code
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="code" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('code')) parsley-error @endif"
                           name="code" required value="{{$coupon->code}}" readonly>
                    @if($errors->has('code'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('code') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="reward" >
                    Reward
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="reward" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('reward')) parsley-error @endif"
                           name="reward" required value="{{$coupon->reward}}">
                    @if($errors->has('reward'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('reward') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="type">
                    Type
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="status" name="type" class="select2" style="width: 100%" autocomplete="off">
                        <option @if($coupon->type == 'fixed') selected @endif value="fixed">Fixed</option>
                        <option @if($coupon->type == 'percent') selected @endif value="percent">Percent</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="expires_at" >
                    Expires At
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="expires_at" type="date" class="form-control col-md-7 col-xs-12 @if($errors->has('expires_at')) parsley-error @endif"
                           name="expires_at" required value="{{$coupon->expires_at->toDateString()}}">
                    @if($errors->has('expires_at'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('expires_at') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>


            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ route('admin.coupons') }}"> {{ __('views.admin.users.edit.cancel') }}</a>
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