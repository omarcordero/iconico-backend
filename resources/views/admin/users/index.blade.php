@extends('admin.layouts.admin')

@section('title')
    Users &nbsp; <a class="btn btn-primary" href="{{ route('admin.users.create') }}"><i class="fa fa-plus-circle"></i>
        Add</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 20px">
            {{ Form::open(['route'=>['admin.users'],'method' => 'get','class'=>'form-horizontal form-label-left']) }}
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
                <label class="control-label col-sm-3 col-xs-12">
                    Role
                </label>
                <div class="col-sm-9 col-xs-12">
                    <a class="btn btn-primary @if(request('role') == 'owner') active @endif"
                       href="{{ app('request')->fullUrlWithQuery(['role' => 'owner'])  }}"><i class="fa fa-cutlery"></i>
                        Store</a>
                    <a class="btn btn-info @if(request('role') == 'delivery') active @endif"
                       href="{{ app('request')->fullUrlWithQuery(['role' => 'delivery'])  }}"><i
                                class="fa fa-motorcycle"></i> Delivery</a>
                    <a class="btn btn-success @if(request('role') == 'customer') active @endif"
                       href="{{ app('request')->fullUrlWithQuery(['role' => 'customer'])  }}"><i class="fa fa-user-o"></i>
                        User</a>
                    <a title="Clear" class="btn btn-default" href="{{ url_without_query_param('role')  }}"><i
                                class="fa fa-remove"></i></a>
                </div>
            </div>
            {{ Form::close() }}
        </div>

        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%" style="text-align: center">
            <thead>
            <tr>
                <th>@sortablelink('email', __('views.admin.users.index.table_header_0'),['page' =>
                    $users->currentPage()])
                </th>
                <th>Mobile</th>
                <th>@sortablelink('name', 'Name',['page' => $users->currentPage()])</th>
                <th>{{ __('views.admin.users.index.table_header_2') }}</th>
                <th>Mobile Verified</th>
                <th>Quick Links</th>
                {{--<th>@sortablelink('active', __('views.admin.users.index.table_header_3'),['page' => $users->currentPage()])</th>--}}
                {{--<th>@sortablelink('confirmed', __('views.admin.users.index.table_header_4'),['page' => $users->currentPage()])</th>--}}
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->mobile_number }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        <h4>
                            {{--{{ $user->roles->pluck('name')->implode(',') }}--}}
                            @if($user->hasRole('administrator'))
                                <i class="fa fa-lock" title="Administrator"></i>
                            @endif
                            @if($user->hasRole('owner'))
                                <i class="fa fa-cutlery" title="Store"></i>
                            @endif
                            @if($user->hasRole('delivery'))
                                <i class="fa fa-motorcycle" title="Delivery Executive"></i>
                            @endif
                            @if($user->hasRole('customer'))
                                <i class="fa fa-user-o" title="Customer"></i>
                            @endif
                        </h4>
                    </td>
                    <td>
                        @if($user->mobile_verified)
                            <span class="label label-primary"><i class="fa fa-check"></i></span>
                        @else
                            <span class="label label-danger"><i class="fa fa-times"></i></span>
                        @endif
                    </td>
                    <td>
                        @if($user->hasRole('owner') && $user->store)
                            <a href="{{ route('admin.orders', ["store" => $user->store->id]) }}">Orders</a> |
                            <a href="{{ route('admin.earnings', ["user_id" => $user->id]) }}">Earnings</a> |
                            <a href="{{ route('admin.bankdetails', ["user" => $user->id]) }}">Bank Details</a>
                            <br/>
                            <a href="{{ route('admin.stores', ["store" => $user->store->id]) }}">Store</a> |
                            <a href="{{ route('admin.menuitems', ["store" => $user->store->id]) }}">Menu Items</a> |
                            <a href="{{ route('admin.ratings', ["store" => $user->store->id]) }}">Ratings</a>
                        @endif
                            @if($user->hasRole('delivery') && $user->deliveryProfile)
                                <a href="{{ route('admin.orders', ["delivery_profile_id" => $user->deliveryProfile->id]) }}">Orders</a> |
                                <a href="{{ route('admin.delivery_profiles', ["delivery_profile_id" => $user->deliveryProfile->id]) }}">Delivery Profile</a> |
                                <a href="{{ route('admin.earnings', ["user_id" => $user->id]) }}">Earnings</a> |
                            @endif
                        @if($user->hasRole('customer'))
                            <a href="{{ route('admin.orders', ["user" => $user->id]) }}">Orders</a>
                        @endif
                    </td>
                    {{--<td>--}}
                    {{--@if($user->confirmed)--}}
                    {{--<span class="label label-success">{{ __('views.admin.users.index.confirmed') }}</span>--}}
                    {{--@else--}}
                    {{--<span class="label label-warning">{{ __('views.admin.users.index.not_confirmed') }}</span>--}}
                    {{--@endif</td>--}}
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', [$user->id]) }}"
                           data-toggle="tooltip" data-placement="top"
                           data-title="{{ __('views.admin.users.index.show') }}">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.users.edit', [$user->id]) }}"
                           data-toggle="tooltip" data-placement="top"
                           data-title="{{ __('views.admin.users.index.edit') }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @if(!$user->hasRole('administrator'))
                        <a class="btn btn-xs btn-danger user_destroy delete" href="{{ route('admin.users.destroy', [$user->id]) }}"
                           data-toggle="tooltip" data-placement="top" data-title="{{ __('views.admin.users.index.delete') }}">
                        <i class="fa fa-trash"></i>
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $users->links() }}
        </div>
    </div>
@endsection
