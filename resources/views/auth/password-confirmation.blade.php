@extends('lcframework::layouts.auth')

@section('title')
    Password confirmation
@endsection

@section('content')
    <div>
        @livewire('lcframework::password-confirmation')
    </div>
@endsection
