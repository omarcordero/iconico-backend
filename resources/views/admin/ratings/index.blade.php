@extends('admin.layouts.admin')

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Rating</th>
                <th>Review</th>
                <th>Store</th>
                <th>User</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ratings as $rating)
                <tr>
                    <td>{{ $rating->rating }}</td>
                    <td>{{ $rating->review }}</td>
                    <td><a href="{{ route('admin.stores.show', $rating->store->id)  }}">{{ $rating->store->name }}</a></td>
                    <td><a href="{{ route('admin.users.show', $rating->user_id)  }}">{{ $rating->user->name }}</a></td>
                    <td>{{ $rating->created_at->diffForHumans() }}</td>
                    <td>{{ $rating->updated_at->diffForHumans() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $ratings->links() }}
        </div>
    </div>
@endsection