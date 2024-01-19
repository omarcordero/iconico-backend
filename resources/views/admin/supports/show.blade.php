@extends('admin.layouts.admin')

@section('title', 'View "' . $support->name . '"'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <tr>
                <th>Name</th>
                <td>{{ $support->name  }}</td>
            </tr>

            <tr>
                <th>Email</th>
                <td>{{ $support->email  }}</td>
            </tr>

            <tr>
                <th>Message</th>
                <td>{{ $support->message  }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $support->created_at }} ({{ $support->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $support->updated_at }} ({{ $support->updated_at->diffForHumans() }})</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection