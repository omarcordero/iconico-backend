@extends('admin.layouts.admin')

@section('title', 'Edit "' . $store->name . '"' )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.stores.update', $store->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    Name
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" value="{{ $store->name }}" required>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tagline" >
                    Tagline
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="tagline" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('tagline')) parsley-error @endif"
                           name="tagline" value="{{ $store->tagline }}" required>
                    @if($errors->has('tagline'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('tagline') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image_url" >
                    Image Url
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="image_url" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('image_url')) parsley-error @endif"
                           name="image_url" value="{{ $store->image_url }}" required>
                    @if($errors->has('image_url'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('image_url') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delivery_time" >
                    Delivery Time
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="delivery_time" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_time')) parsley-error @endif"
                           name="delivery_time" value="{{ $store->delivery_time }}" required>
                    @if($errors->has('delivery_time'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('delivery_time') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="minimum_order" >
                    Minimum Order
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="minimum_order" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('minimum_order')) parsley-error @endif"
                           name="minimum_order" value="{{ $store->minimum_order }}" required>
                    @if($errors->has('minimum_order'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('minimum_order') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cost_for_two" >
                    Cost For Two
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="cost_for_two" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('cost_for_two')) parsley-error @endif"
                           name="cost_for_two" value="{{ $store->cost_for_two }}" required>
                    @if($errors->has('cost_for_two'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('cost_for_two') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delivery_fee" >
                    Delivery Fee
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="delivery_fee" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_fee')) parsley-error @endif"
                           name="delivery_fee" value="{{ $store->delivery_fee }}" required>
                    @if($errors->has('delivery_fee'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('delivery_fee') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="details" >
                    Details
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="details" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('details')) parsley-error @endif"
                           name="details" value="{{ $store->details }}" required>
                    @if($errors->has('details'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('details') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="delivery_limit" >
                    Delivery Limit (in meters)
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="delivery_limit" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('delivery_limit')) parsley-error @endif"
                           name="delivery_limit" value="{{ $store->delivery_limit }}" required>
                    @if($errors->has('delivery_limit'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('delivery_limit') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="area" >
                    Area
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="area" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('area')) parsley-error @endif"
                           name="area" value="{{ $store->area }}" required>
                    @if($errors->has('area'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('area') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address" >
                    Address
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('address')) parsley-error @endif"
                           name="address" value="{{ $store->address }}" required>
                    @if($errors->has('address'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('address') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="latitude" >
                    Latitude
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="latitude" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('latitude')) parsley-error @endif"
                           name="latitude" value="{{ $store->latitude }}" required>
                    @if($errors->has('latitude'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('latitude') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="longitude" >
                    Longitude
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="longitude" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('longitude')) parsley-error @endif"
                           name="longitude" value="{{ $store->longitude }}" required>
                    @if($errors->has('longitude'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('longitude') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="opens_at" >
                    Opens At
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="opens_at" type="time" class="form-control col-md-7 col-xs-12 @if($errors->has('opens_at')) parsley-error @endif"
                           name="opens_at" value="{{ $store->opens_at }}" required>
                    @if($errors->has('opens_at'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('opens_at') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="closes_at" >
                    Closes At
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="closes_at" type="time" class="form-control col-md-7 col-xs-12 @if($errors->has('closes_at')) parsley-error @endif"
                           name="closes_at" value="{{ $store->closes_at }}" required>
                    @if($errors->has('closes_at'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('closes_at') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="serves_non_veg" >
                    Serves non veg?
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="serves_non_veg" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('serves_non_veg')) parsley-error @endif"
                           name="serves_non_veg" value="{{ $store->serves_non_veg }}" required>
                    @if($errors->has('serves_non_veg'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('serves_non_veg') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="preorder" >
                    Preorder?
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="preorder" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('preorder')) parsley-error @endif"
                           name="preorder" value="{{ $store->preorder }}" required>
                    @if($errors->has('preorder'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('preorder') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">
                    Status
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="status" name="status" class="select2" style="width: 100%" autocomplete="off">
                        <option @if($store->status == 'open') selected="selected" @endif value="open">Open</option>
                        <option @if($store->status == 'close') selected="selected" @endif value="close">Close</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ URL::previous() }}"> {{ __('views.admin.users.edit.cancel') }}</a>
                    <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
                </div>
            </div>

            <div class="form-group" style="margin-top: 30px;">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    Location on map
                </label>
                <div class="col-sm-9 col-xs-12">
                    <div id="map" style="height: 500px;" class="col-xs-9"></div>
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

    <script>
        // map
        var map;
        var marker;
        function initMap() {
            var myLatLng = {lat: parseFloat('{{ $store->latitude }}'), lng: parseFloat('{{ $store->longitude }}')};

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: myLatLng
            });

            marker = new google.maps.Marker({
                map: map,
                position: myLatLng
            });

            console.log(marker);
        }
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('firebase.api_key') }}&callback=initMap"
            type="text/javascript"></script>
@endsection