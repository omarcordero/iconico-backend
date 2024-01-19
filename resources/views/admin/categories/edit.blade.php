@extends('admin.layouts.admin')

@section('title', 'Edit "' . $category->title . '"' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.categories.update', $category->id],'method' => 'put','class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title" >
                    Title
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="title" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('title')) parsley-error @endif"
                           name="title" value="{{ $category->title }}" required>
                    @if($errors->has('title'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('title') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image" >
                    Image
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="image" type="file" class="form-control col-md-7 col-xs-12 @if($errors->has('image')) parsley-error @endif"
                           name="image">
                    <a target="_blank" href="{{ $category->image_url }}">{{ $category->image_url }}</a>
                    @if($errors->has('image'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('image') as $error)
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