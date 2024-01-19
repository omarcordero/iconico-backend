@extends('admin.layouts.admin')

@section('title', 'View "' . $deliveryProfile->user->name . '"'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <tr>
                <th>Name</th>
                <td>
                    <a href="{{ route('admin.users.show', [$deliveryProfile->user->id]) }}">{{ $deliveryProfile->user->name  }}</a>
                </td>
            </tr>

            <tr>
                <th>Is Online?</th>
                <td>{{ $deliveryProfile->is_online  }}</td>
            </tr>

            <tr>
                <th>Assigned</th>
                <td>{{ $deliveryProfile->assigned  }}</td>
            </tr>

            <tr>
                <th>Latitude</th>
                <td>{{ $deliveryProfile->latitude  }}</td>
            </tr>

            <tr>
                <th>Longitude</th>
                <td>{{ $deliveryProfile->longitude  }}</td>
            </tr>

            <tr>
                <th class="col-xs-2">Location on map</th>
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
            var myLatLng = {lat: parseFloat('{{ $deliveryProfile->latitude }}'), lng: parseFloat('{{ $deliveryProfile->longitude }}')};

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