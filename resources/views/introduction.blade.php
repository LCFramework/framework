@extends('lcframework::layouts.blank')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-end py-4">
            <ul class="flex items-center space-x-4">
                @auth
                    <li>
                        <a
                            href="{{route('filament.pages.dashboard')}}"
                            class="test-sm font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Administration
                        </a>
                    </li>
                    <li>
                        <form
                            action="{{route('logout')}}"
                            method="post"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="test-sm font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                            >
                                Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a
                            href="{{route('login')}}"
                            class="test-sm font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Login
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{route('register')}}"
                            class="test-sm font-medium transition hover:text-primary-500 focus:text-primary-700 dark:text-gray-300"
                        >
                            Register
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endsection
