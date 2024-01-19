@extends('admin.layouts.admin')

@section('title', 'View "' . $bankDetail->name . '"'))

@section('content')
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>
            
            <tr>
                <th>Name</th>
                <td>{{ $bankDetail->name  }}</td>
            </tr>

            <tr>
                <th>Bank Name</th>
                <td>{{ $bankDetail->bank_name  }}</td>
            </tr>

            <tr>
                <th>Account Number</th>
                <td>{{ $bankDetail->account_number  }}</td>
            </tr>

            <tr>
                <th>IFSC</th>
                <td>{{ $bankDetail->ifsc  }}</td>
            </tr>

            <tr>
                <th>User ID</th>
                <td><a href="{{ route('admin.users.show', [$bankDetail->user_id]) }}">{{ $bankDetail->user_id  }}</a></td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $bankDetail->created_at }} ({{ $bankDetail->created_at->diffForHumans() }})</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $bankDetail->updated_at }} ({{ $bankDetail->updated_at->diffForHumans() }})</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection