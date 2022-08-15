@extends('lcframework::layouts.auth')

@section('title')
    Forgot password
@endsection

@section('content')
    <div>
        @livewire('lcframework::password-request')
    </div>
@endsection
