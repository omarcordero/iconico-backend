@extends('admin.layouts.admin')

@section('title', "Delivery Profiles")

@section('content')
    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.delivery_profiles'],'method' => 'get','class'=>'form-horizontal form-label-left']) }}
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="search">
                    Search
                </label>
                <div class="col-sm-4 col-xs-12">
                    <input id="search" type="text" class="form-control col-md-7 col-xs-12"
                           placeholder="Search for name, mobile, email"
                           name="search" required value="{{ app('request')->input('search')  }}">
                </div>
                <div class="col-sm-5 col-xs-12">
                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Filter</button>
                    <a title="Clear" class="btn btn-default" href="{{ url_without_query_param('search')  }}"><i
                                class="fa fa-remove"></i></a>
                </div>
            </div>
            <div class="form-group">

            </div>
            {{ Form::close() }}
        </div>

        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th>Is Online?</th>
                <th>Assigned?</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($deliveryProfiles as $deliveryProfile)
                <tr>
                    <td>{{ $deliveryProfile->user->name }}</td>
                    <td>{{ $deliveryProfile->is_online }}</td>
                    <td>{{ $deliveryProfile->assigned }}</td>
                    <td>{{ $deliveryProfile->latitude }}</td>
                    <td>{{ $deliveryProfile->longitude }}</td>
                    <td>{{ $deliveryProfile->created_at }}</td>
                    <td>{{ $deliveryProfile->updated_at }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.delivery_profiles.show', [$deliveryProfile->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $deliveryProfiles->links() }}
        </div>
    </div>
@endsection