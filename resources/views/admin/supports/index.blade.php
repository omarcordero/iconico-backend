@extends('admin.layouts.admin')

@section('title', "Support")

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($supports as $support)
                <tr>
                    <td>{{ $support->name }}</td>
                    <td>{{ $support->email }}</td>
                    <td>{{ $support->created_at }}</td>
                    <td>{{ $support->updated_at }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.supports.show', [$support->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $supports->links() }}
        </div>
    </div>
@endsection