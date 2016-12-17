@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Home</h1>

                @if (Auth::guest())
                    <ul class="nav nav-pills nav-stacked">
                        <li role="presentation"><a href="{{ url('/login') }}">Login</a></li>
                        <li role="presentation"><a href="{{ url('/register') }}">Register</a></li>
                    </ul>
                @else
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="/game">List games</a></li>
                        <li><a href="/game/create">Create game</a></li>
                        @if (Auth::user()->admin == true)
                            <li><a href="/admin/modes">Modes</a></li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
