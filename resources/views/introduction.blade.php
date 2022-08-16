@extends('lcframework::layouts.blank')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-end">
            <ul class="flex items-center space-x-4">
                @auth
                    <li>
                        <a href="{{route('filament.pages.dashboard')}}">
                            Administration
                        </a>
                    </li>
                    <li>
                        <form action="{{route('logout')}}" method="post">
                            @csrf

                            <button type="submit">
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{route('login')}}">
                            Login
                        </a>
                    </li>
                    <li>
                        <a href="{{route('register')}}">
                            Register
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endsection
