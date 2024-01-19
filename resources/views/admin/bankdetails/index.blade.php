@extends('admin.layouts.admin')

@section('title', "Bank Details")

@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th>User Id</th>
                <th>IFSC</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bankDetails as $bankDetail)
                <tr>
                    <td>{{ $bankDetail->name }}</td>
                    <td>{{ $bankDetail->user_id }}</td>
                    <td>{{ $bankDetail->ifsc }}</td>
                    <td>{{ $bankDetail->created_at }}</td>
                    <td>{{ $bankDetail->updated_at }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.bankdetails.show', [$bankDetail->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-xs btn-info" href="{{ route('admin.bankdetails.edit', [$bankDetail->id]) }}" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pull-right">
            {{ $bankDetails->links() }}
        </div>
    </div>
@endsection