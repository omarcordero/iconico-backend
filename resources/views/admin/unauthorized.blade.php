@extends('admin.layouts.admin')

@section('content')
    <h2>You are unauthorized to perform this action. </h2>
    <h3>This feature is working perfectly but disabled right now to prevent spam.</h3>
    <a class="btn btn-primary" href="{{ URL::previous() }}"> Go Back</a>
@endsection