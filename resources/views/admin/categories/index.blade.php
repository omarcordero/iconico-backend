@extends('admin.layouts.admin')

@section('title')
    Categories &nbsp; <a class="btn btn-primary" href="{{ route('admin.categories.create') }}"><i class="fa fa-plus-circle"></i>
        Add</a>
@endsection

@section('content')
    <div class="row">

        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Title</th>
                <th>Image URL</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->title }}</td>
                    <td>{{ $category->image_url }}</td>
                    <td>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.categories.edit', [$category->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $categories->links() }}
        </div>
    </div>
@endsection