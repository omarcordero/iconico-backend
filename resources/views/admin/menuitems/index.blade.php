@extends('admin.layouts.admin')

@section('title')
    Menu Items &nbsp; <a class="btn btn-primary" href="{{ route('admin.menuitems.create') }}"><i class="fa fa-plus-circle"></i>
        Add</a>
@endsection

@section('content')
    <div class="col-sm-12 pull-left">
        <div class="">
            <strong>Item Status: </strong>
            <a class="btn btn-warning" href="{{ app('request')->fullUrlWithQuery(['status' => 'pending'])  }}">View Pending</a>
            <a class="btn btn-danger" href="{{ app('request')->fullUrlWithQuery(['status' => 'rejected'])  }}">View Rejected</a>
            <a class="btn btn-success" href="{{ app('request')->fullUrlWithQuery(['status' => 'approved'])  }}">View Approved</a>
        </div>
    </div>

    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Title</th>
                <th>Store</th>
                <th>Price</th>
                <th>Is Non Veg?</th>
                <th>Status</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($menuitems as $menuitem)
                <tr class="menuitem-row">
                    <td>{{ $menuitem->title }}</td>
                    <td><a href="{{ route('admin.stores.show', [$menuitem->store_id]) }}">{{ $menuitem->store->name ? $menuitem->store->name : "NA" }}</a></td>
                    <td>{{ $menuitem->price }}</td>
                    <td>{{ $menuitem->is_non_veg }}</td>
                    <td class="status">{{ $menuitem->status }}</td>
                    <td>{{ $menuitem->updated_at->diffForHumans() }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.menuitems.show', [$menuitem->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.menuitems.edit', [$menuitem->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @if($menuitem->status === 'pending')
                            {{ Form::open(['route'=>['admin.menuitems.quickApprove', $menuitem->id],'method' => 'put','class'=>'form-horizontal form-label-left quick-approve']) }}
                                <button type="submit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top">
                                    Quick Approve
                                </button>
                            {{ Form::close() }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $menuitems->links() }}
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        $("form.quick-approve").submit(function (event) {
            event.preventDefault();
            var form = this;
            $.ajax({
                type: 'PUT',
                url: this.action,
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json'
            }).done(function (data) {
                $(form).closest(".menuitem-row").find(".status").text(data.status);
                $(form).hide();
            });
        });
    </script>
@endsection