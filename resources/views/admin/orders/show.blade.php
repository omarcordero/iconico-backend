@extends('admin.layouts.admin')

@section('title', 'View'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <tr>
                <th>User</th>
                <td><a target="_blank"
                       href="{{ route('admin.users.show', [$order->owner_id]) }}">{{ $order->user->name  }}</a></td>
            </tr>

            <tr>
                <th>Store</th>
                <td><a target="_blank"
                       href="{{ route('admin.stores.show', [$order->owner_id]) }}">{{ $order->store->name  }}</a></td>
            </tr>

            <tr>
                <th>Delivery Person</th>
                <td>
                    @if($order->delivery_profile_id)
                        <a target="_blank"
                           href="{{ route('admin.delivery_profiles.show', [$order->delivery_profile_id]) }}">{{ $order->deliveryProfile->user->name  }}</a>
                    @else
                        Not Assigned
                    @endif

                </td>
            </tr>

            <tr>
                <th>Subtotal</th>
                <td>{{ $order->subtotal }}</td>
            </tr>

            <tr>
                <th>Taxes</th>
                <td>{{ $order->taxes }}</td>
            </tr>

            <tr>
                <th>Delivery Fee</th>
                <td>{{ $order->delivery_fee }}</td>
            </tr>

            <tr>
                <th>Total</th>
                <td>{{ $order->total }}</td>
            </tr>

            <tr>
                <th>Discount</th>
                <td>{{ $order->discount }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>{{ $order->status }}</td>
            </tr>

            <tr>
                <th>Delivery Status</th>
                <td>{{ $order->delivery_status }}</td>
            </tr>

            <tr>
                <th>Payment Status</th>
                <td>{{ $order->payment_status }}</td>
            </tr>

            <tr>
                <th>Payment Method</th>
                <td>{{ $order->paymentMethod->title }}</td>
            </tr>

            <tr>
                <th>Special Instructions</th>
                <td>{{ $order->special_instructions }}</td>
            </tr>

            <tr>
                <th>Address</th>
                <td>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $order->address->latitude . "," . $order->address->longitude }}"
                       target="_blank">
                        {{ $order->address->address }}
                    </a>
                </td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $order->created_at->toDayDateTimeString() }} ({{ $order->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $order->updated_at->toDayDateTimeString() }} ({{ $order->updated_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated Status</th>
                <td>
                    @if(in_array($order->status, ['new', 'pending']))
                        <button id="update-status" class="btn btn-success" data-status="accepted">Accept Order</button>
                    @endif
                    @if($order->status == 'accepted')
                        <button id="update-status" class="btn btn-success" data-status="dispatched">Dispatch Order</button>
                    @endif
                </td>
            </tr>

            <tr>
                <th class="col-xs-2">Location on map</th>
                <td>
                    <div id="map" style="height: 600px;"></div>
                </td>
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
        var storeMarker;
        var deiveryMarker;
        var deliveryAssigned = @if($order->delivery_profile_id) true; @else false; @endif
        var deliveryLat = @if($order->delivery_profile_id) {{$order->deliveryProfile->latitude}}; @else null; @endif
        var deliveryLng = @if($order->delivery_profile_id) {{$order->deliveryProfile->longitude}}; @else null; @endif

        function initMap() {
            var myLatLng = {
                lat: parseFloat('{{ $order->address->latitude }}'),
                lng: parseFloat('{{ $order->address->longitude }}')
            };

            var storeLatLng = {
                lat: parseFloat('{{ $order->store->latitude }}'),
                lng: parseFloat('{{ $order->store->longitude }}')
            };

            var deliveryLatLng = deliveryAssigned ? {
                lat: parseFloat(deliveryLat),
                lng: parseFloat(deliveryLng)
            } : null;

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: myLatLng
            });

            marker = new google.maps.Marker({
                map: map,
                position: myLatLng
            });

            marker = new google.maps.Marker({
                map: map,
                position: storeLatLng,
                icon: 'http://localhost/chefqu/public/assets/store.png'
            });

            if(deliveryLatLng != null) {
                deiveryMarker = new google.maps.Marker({
                    map: map,
                    position: deliveryLatLng,
                    icon: 'http://localhost/chefqu/public/assets/bike.png'
                });
            }
        }
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('firebase.api_key') }}&callback=initMap"
            type="text/javascript"></script>

    <script type="text/javascript">
        $("#update-status").on('click', (function (event) {
            var elem = this;
            $.ajax({
                type: 'PUT',
                url: "{{ route('admin.json.orders.update', ['order' => $order->id]) }}",
                data: {status: $(elem).attr("data-status")},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json'
            }).done(function (data) {
                alert('Order status updated successfully!');
                window.location.reload();
            }).fail(function(err) {
                if(err.status == 404) {
                    alert('Delivery person not assigned. Unable to dispatch order.');
                }
                if(err.status == 422) {
                    alert('Just assigned delivery person. Please wait till he reaches your store to dispatch order.');
                    window.location.reload();
                }
            });
        }));
    </script>
@endsection
