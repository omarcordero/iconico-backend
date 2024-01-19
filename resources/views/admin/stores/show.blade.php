@extends('admin.layouts.admin')

@section('title', 'View "' . $store->name . '"'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <tr>
                <th>Owner</th>
                <td><a href="{{ route('admin.users.show', [$store->owner_id]) }}">{{ $store->owner_id  }}</a></td>
            </tr>

            <tr>
                <th>Name</th>
                <td>{{ $store->name  }}</td>
            </tr>

            <tr>
                <th>Tagline</th>
                <td>{{ $store->tagline  }}</td>
            </tr>

            <tr>
                <th>Image</th>
                <td><a href="{{ $store->image_url  }}" target="_blank">{{ $store->image_url  }}</a></td>
            </tr>

            <tr>
                <th>Delivery Time</th>
                <td>{{ $store->delivery_time  }}</td>
            </tr>

            <tr>
                <th>Minimum Order</th>
                <td>{{ $store->minimum_order  }}</td>
            </tr>

            <tr>
                <th>Cost For Two</th>
                <td>{{ $store->cost_for_two  }}</td>
            </tr>

            <tr>
                <th>Delivery Fee</th>
                <td>{{ $store->delivery_fee  }}</td>
            </tr>

            <tr>
                <th>Details</th>
                <td>{{ $store->details }}</td>
            </tr>

            <tr>
                <th>Delivery Limit (in meters)</th>
                <td>{{ $store->delivery_limit }}</td>
            </tr>

            <tr>
                <th>Area</th>
                <td>{{ $store->area  }}</td>
            </tr>

            <tr>
                <th>Address</th>
                <td>{{ $store->address  }}</td>
            </tr>

            <tr>
                <th>Longitude</th>
                <td>{{ $store->longitude  }}</td>
            </tr>

            <tr>
                <th>Latitude</th>
                <td>{{ $store->latitude  }}</td>
            </tr>

            <tr>
                <th>Opens At</th>
                <td>{{ $store->opens_at  }}</td>
            </tr>

            <tr>
                <th>Closes At</th>
                <td>{{ $store->closes_at  }}</td>
            </tr>

            <tr>
                <th>Serves Non Veg?</th>
                <td>{{ $store->serves_non_veg  }}</td>
            </tr>

            <tr>
                <th>Preorder</th>
                <td>{{ $store->preorder  }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>{{ $store->status  }}</td>
            </tr>

            <tr>
                <th>Categories</th>
                <td>
                    @foreach($store->categories as $cat)
                        <a href="{{ route('admin.categories.edit', ['category' => $cat->id]) }}">{{ $cat->title }}</a>,&nbsp;
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $store->created_at }} ({{ $store->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $store->updated_at }} ({{ $store->updated_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Menu Items</th>
                <td><a href="{{ route('admin.menuitems', ['store' => $store->id]) }}">View All</a></td>
            </tr>

            <tr>
                <th>Bank Details</th>
                <td><a href="{{ route('admin.bankdetails', ['user' => $store->owner_id]) }}">View</a></td>
            </tr>

            <tr>
                <th>Orders</th>
                <td><a href="{{ route('admin.orders', ['store' => $store->id]) }}">View All</a></td>
            </tr>

            <tr>
                <th>Earnings</th>
                <td><a href="{{ route('admin.earnings', ['user_id' => $store->owner_id]) }}">View All</a></td>
            </tr>

            <tr>
                <th>Total Earnings</th>
                <td>{{ $earnings['total_earnings'] }}</td>
            </tr>

            <tr>
                <th>Unpaid Earnings</th>
                <td>{{ $earnings['unpaid_earnings'] }}</td>
            </tr>

            <tr>
                <th>Location on map</th>
                <td><div id="map" style="height: 500px;"></div></td>
            </tr>

            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    @parent

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