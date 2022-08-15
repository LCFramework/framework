@extends('lcframework::layouts.auth')

@section('title')
    Reset password
@endsection

@section('content')
    <div>
        @livewire('lcframework::password-reset')
    </div>
@endsection
