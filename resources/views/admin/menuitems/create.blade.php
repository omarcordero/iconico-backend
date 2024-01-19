@extends('admin.layouts.admin')

@section('title', 'Add')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.menuitems.store'],'method' => 'post','class'=>'form-horizontal form-label-left', 'enctype' => 'multipart/form-data']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title" >
                    Name
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="title" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('title')) parsley-error @endif"
                           name="title" required>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="detail" >
                    Detail
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="detail" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('detail')) parsley-error @endif"
                           name="detail" required>
                    @if($errors->has('detail'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('detail') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="specification" >
                    Specification
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="specification" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('specification')) parsley-error @endif"
                           name="specification" required>
                    @if($errors->has('specification'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('specification') as $error)
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="price" >
                    Price
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="price" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('price')) parsley-error @endif"
                           name="price" required>
                    @if($errors->has('price'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('price') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="is_available" >
                    Is Available?
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="is_available" type="checkbox" class="form-check-input @if($errors->has('is_available')) parsley-error @endif"
                           name="is_available">
                    @if($errors->has('is_available'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('is_available') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="is_non_veg" >
                    Is Non Veg?
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="is_non_veg" type="checkbox" class="form-check-input @if($errors->has('is_non_veg')) parsley-error @endif"
                           name="is_non_veg">
                    @if($errors->has('is_non_veg'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('is_non_veg') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>  

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">
                    Status
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="status" name="status" class="select2" style="width: 100%" autocomplete="off" required>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                        <option value="approved" selected>Approved</option>
                    </select>
                    @if($errors->has('status'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('status') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="store_id">
                    Store
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="store_id" name="store_id" class="select2" style="width: 100%" autocomplete="off">
                        <option value="">Select</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('store_id'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('store_id') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="categories">
                    Categories
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="categories" name="categories[]" class="select2" multiple="multiple" style="width: 100%" autocomplete="off">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('categories'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('categories') as $error)
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