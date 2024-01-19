@extends('admin.layouts.admin')

@section('title', __('views.admin.users.show.title', ['name' => $user->name]))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_1') }}</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_2') }}</th>
                <td>
                    <a href="mailto:{{ $user->email }}">
                        {{ $user->email }}
                    </a>
                </td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td>{{ $user->mobile_number }}</td>
            </tr>
            <tr>
                <th>Mobile Verified</th>
                <td>
                    @if($user->mobile_verified)
                        <span class="label label-primary"><i class="fa fa-check"></i></span>
                    @else
                        <span class="label label-danger"><i class="fa fa-times"></i></span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('views.admin.users.show.table_header_3') }}</th>
                <td>
                    {{ $user->roles->pluck('name')->implode(',') }}
                </td>
            </tr>
            {{--<tr>--}}
                {{--<th>{{ __('views.admin.users.show.table_header_4') }}</th>--}}
                {{--<td>--}}
                    {{--@if($user->active)--}}
                        {{--<span class="label label-primary">{{ __('views.admin.users.show.active') }}</span>--}}
                    {{--@else--}}
                        {{--<span class="label label-danger">{{ __('views.admin.users.show.inactive') }}</span>--}}
                    {{--@endif--}}
                {{--</td>--}}
            {{--</tr>--}}

            {{--<tr>--}}
                {{--<th>{{ __('views.admin.users.show.table_header_5') }}</th>--}}
                {{--<td>--}}
                    {{--@if($user->confirmed)--}}
                        {{--<span class="label label-success">{{ __('views.admin.users.show.confirmed') }}</span>--}}
                    {{--@else--}}
                        {{--<span class="label label-warning">{{ __('views.admin.users.show.not_confirmed') }}</span>--}}
                    {{--@endif</td>--}}
                {{--</td>--}}
            {{--</tr>--}}

            <tr>
                <th>{{ __('views.admin.users.show.table_header_6') }}</th>
                <td>{{ $user->created_at }} ({{ $user->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>{{ __('views.admin.users.show.table_header_7') }}</th>
                <td>{{ $user->updated_at }} ({{ $user->updated_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Links</th>
                <td>
                    @if($user->hasRole('owner'))
                        <a href="{{ route('admin.orders', ["store" => $user->store->id]) }}">Orders</a> |
                        <a href="{{ route('admin.earnings', ["user_id" => $user->id]) }}">Earnings</a> |
                        <a href="{{ route('admin.bankdetails', ["user" => $user->id]) }}">Bank Details</a>
                        <br/>
                        <a href="{{ route('admin.stores', ["store" => $user->store->id]) }}">Store</a> |
                        <a href="{{ route('admin.menuitems', ["store" => $user->store->id]) }}">Menu Items</a> |
                        <a href="{{ route('admin.ratings', ["store" => $user->store->id]) }}">Ratings</a>
                    @endif
                    @if($user->hasRole('delivery'))
                        <a href="{{ route('admin.orders', ["delivery_profile_id" => $user->deliveryProfile->id]) }}">Orders</a> |
                        <a href="{{ route('admin.delivery_profiles', ["delivery_profile_id" => $user->deliveryProfile->id]) }}">Delivery Profile</a> |
                        <a href="{{ route('admin.earnings', ["user_id" => $user->id]) }}">Earnings</a> |
                    @endif
                    @if($user->hasRole('customer'))
                        <a href="{{ route('admin.orders', ["user" => $user->id]) }}">Orders</a>
                    @endif
                </td>
            </tr>
            <tr>
                <td><a class="btn btn-primary" href="{{ URL::previous() }}"> Back</a></td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection