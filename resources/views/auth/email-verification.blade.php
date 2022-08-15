@extends('lcframework::layouts.auth')

@section('title')
    Email verification
@endsection

@section('content')
    <div>
        @livewire('lcframework::email-verification')
    </div>
@endsection
