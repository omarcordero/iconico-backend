    @extends('admin.layouts.admin')

@section('title', "Orders")

@section('content')
    <div class="col-md-12 col-sm-12 col-xs-12">

        {{ Form::open(['url'=>url()->full(),'method' => 'get','class'=>'form-horizontal form-label-left']) }}
        <input type="hidden" name="status" value="{{request()->input('status')}}">
        <input type="hidden" name="delivery_status" value="{{request()->input('delivery_status')}}">
        <input type="hidden" name="payment_status" value="{{request()->input('payment_status')}}">

        <div class="">
            <strong>Order Status: </strong>
            <a class="btn btn-primary" href="{{ app('request')->fullUrlWithQuery(['status' => 'new'])  }}">New</a>
            <a class="btn btn-warning" href="{{ app('request')->fullUrlWithQuery(['status' => 'pending'])  }}">Pending</a>
            <a class="btn btn-danger" href="{{ app('request')->fullUrlWithQuery(['status' => 'cancelled'])  }}">Cancelled</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['status' => 'accepted'])  }}">Accepted</a>
            <a class="btn btn-danger" href="{{ app('request')->fullUrlWithQuery(['status' => 'rejected'])  }}">Rejected</a>
            <a class="btn btn-info" href="{{ app('request')->fullUrlWithQuery(['status' => 'preparing'])  }}">Preparing</a>
            <a class="btn btn-info" href="{{ app('request')->fullUrlWithQuery(['status' => 'dispatched'])  }}">Dispatched</a>
            <a class="btn btn-info" href="{{ app('request')->fullUrlWithQuery(['status' => 'intransit'])  }}">Intransit</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['status' => 'complete'])  }}">Complete</a>
            <a title="Clear" class="btn btn-default" href="{{ url_without_query_param('status')  }}"><i class="fa fa-remove"></i></a>
        </div>
        <div class="">
            <strong>Delivery Status: </strong> &nbsp;
            <a class="btn btn-warning" href="{{ app('request')->fullUrlWithQuery(['delivery_status' => 'pending'])  }}">Pending</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['delivery_status' => 'allotted'])  }}">Allotted</a>
            <a class="btn btn-info" href="{{ app('request')->fullUrlWithQuery(['delivery_status' => 'started'])  }}">Started</a>
            <a class="btn btn-danger" href="{{ app('request')->fullUrlWithQuery(['delivery_status' => 'cancelled'])  }}">Cancelled</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['delivery_status' => 'complete'])  }}">Complete</a>
            <a title="Clear" class="btn btn-default" href="{{ url_without_query_param('delivery_status')  }}"><i class="fa fa-remove"></i></a>
        </div>
        <div class="">
            <strong>Payment Status: </strong>
            <a class="btn btn-warning" href="{{ app('request')->fullUrlWithQuery(['payment_status' => 'paid'])  }}">Paid</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['payment_status' => 'unpaid'])  }}">Unpaid</a>
            <a title="Clear" class="btn btn-default" href="{{ url_without_query_param('payment_status')  }}"><i class="fa fa-remove"></i></a>
        </div>

        <div class="form-group" style="margin: 30px 0 30px 0;">
            <label class="control-label col-sm-2 col-xs-12">
                Order Create Date:
            </label>
            <div class="col-sm-3 col-xs-12">
                <input id="from" type="date" class="form-control col-md-7 col-xs-12" placeholder="Date From"
                       name="from" value="{{ app('request')->input('from')  }}">
            </div>
            <div class="col-sm-3 col-xs-12">
                <input id="to" type="date" class="form-control col-md-7 col-xs-12" placeholder="Date To"
                       name="to" value="{{ app('request')->input('to')  }}">
            </div>
            <div class="col-sm-4 col-xs-12">
                <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Filter</button>
                <a title="Clear" class="btn btn-default" href="{{ url_without_query_param(['from', 'to'])  }}"><i class="fa fa-remove"></i></a>
            </div>
        </div>


        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="search">
                    Search
                </label>
                <div class="col-sm-4 col-xs-12">
                    <input id="search" type="text" class="form-control col-md-7 col-xs-12"
                           placeholder="Search user, store, address, total"
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
        </div>

        {{ Form::close() }}
    </div>


    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>User ID</th>
                <th>Store ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Delivery Status</th>
                <th>Payment Status</th>
                <th>Delivery At</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.users.show', [$order->user_id]) }}">{{ $order->user->name }}</a></td>
                    <td><a href="{{ route('admin.stores.show', [$order->store_id]) }}">{{ $order->store->name }}</a></td>
                    <td>{{ $order->total }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->delivery_status }}</td>
                    <td>{{ $order->payment_status }}</td>
                    <td>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $order->address->latitude . "," . $order->address->longitude }}" target="_blank">
                            {{ $order->address->address }}
                        </a>
                    </td>
                    <td>{{ $order->created_at->toDayDateTimeString() }}</td>
                    <td>{{ $order->updated_at->toDayDateTimeString() }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.orders.show', [$order->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $orders->links() }}
        </div>
    </div>
@endsection