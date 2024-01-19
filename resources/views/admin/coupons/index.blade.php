@extends('admin.layouts.admin')

@section('title')
    Coupons &nbsp; <a class="btn btn-primary" href="{{ route('admin.coupons.create') }}"><i class="fa fa-plus-circle"></i>
        Add</a>
@endsection

@section('content')
    <div class="row">

        <table class="table table-striped table-bordered dt-responsive nowrap jambo_table" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th>Code</th>
                <th>Reward</th>
                <th>Type</th>
                <th>Expires At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($coupons as $coupon)
                <tr @if($coupon->isExpired()) class="text-danger" @endif>
                    <td>{{ $coupon->code }}</td>
                    <td>{{ $coupon->reward }}</td>
                    <td>{{ $coupon->type }}</td>
                    <td>{{ $coupon->expires_at->toDateString() }}</td>
                    <td>
                        <a class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" href="{{ route('admin.coupons.edit', $coupon->id) }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                        {{ Form::open(['route'=>['admin.coupons.destroy', $coupon->id],'method' => 'delete','class'=>'form-horizontal form-label-left']) }}
                        <button type="submit" title="Delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top">
                            <i class="fa fa-trash"></i>
                        </button>
                        {{ Form::close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection