@extends('admin.layouts.admin')

@section('title', 'View "' . $menuitem->title . '"'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <tr>
                <th>Store</th>
                <td><a href="{{ route('admin.menuitems.show', [$menuitem->store_id]) }}">{{ $menuitem->owner_id  }}</a></td>
            </tr>

            <tr>
                <th>Title</th>
                <td>{{ $menuitem->title  }}</td>
            </tr>

            <tr>
                <th>Detail</th>
                <td>{{ $menuitem->detail }}</td>
            </tr>

            <tr>
                <th>Specification</th>
                <td>{{ $menuitem->specification  }}</td>
            </tr>

            <tr>
                <th>Image Url</th>
                <td><a target="_blank" href="{{ $menuitem->image_url  }}">{{ $menuitem->image_url  }}</a></td>
            </tr>

            <tr>
                <th>Price</th>
                <td>{{ $menuitem->price  }}</td>
            </tr>

            <tr>
                <th>Is Available</th>
                <td>{{ $menuitem->is_available  }}</td>
            </tr>

            <tr>
                <th>Is Non Veg</th>
                <td>{{ $menuitem->is_non_veg }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>{{ $menuitem->status  }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $menuitem->created_at }} ({{ $menuitem->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $menuitem->updated_at }} ({{ $menuitem->updated_at->diffForHumans() }})</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection