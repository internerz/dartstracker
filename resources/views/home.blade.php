@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Home</h1>

                @if (Auth::guest())
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                        <li><a href="{{ route('create-game-guest') }}">Create game</a></li>
                    </ul>
                @else
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="{{ route('list-games') }}">List games</a></li>
                        <li><a href="{{ route('create-game') }}">Create game</a></li>
                        @if (Auth::user()->admin == true)
                            <li><a href="{{ route('list-modes') }}">Modes</a></li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
