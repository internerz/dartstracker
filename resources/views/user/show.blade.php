@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>
                    {{ $user->name }}
                    @if (\Auth::user()->id == $user->id)
                        <a href="/profile/edit"><span class="glyphicon glyphicon-edit"></span></a>
                    @elseif (\Auth::check())
                        @if ($areFriends)
                            <a href="{{ url('/friends') }}"
                               onclick="event.preventDefault();
                                               document.getElementById('deleteFriendForm').submit();">
                                <span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-minus"></span>
                            </a>

                            <form method="POST" action="/friends" id="deleteFriendForm">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <input type="hidden" name="friend_id" value="{{ $user->id }}">
                            </form>
                        @else
                            <a href="{{ url('/friends') }}"
                               onclick="event.preventDefault();
                                               document.getElementById('addFriendForm').submit();">
                                <span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-plus"></span>
                            </a>

                            <form method="POST" action="/friends" id="addFriendForm">
                                {{ csrf_field() }}
                                <input type="hidden" name="friend_id" value="{{ $user->id }}">
                            </form>
                        @endif
                    @endif
                </h1>

                <div class="stats">
                    <h2>Stats</h2>
                    <p>Stats</p>
                </div>

                @if (\Auth::user()->id == $user->id)
                    <div class="friends">
                        <h2>Friends</h2>

                        @if (count($user->friends))
                            <ul class="list-group">
                                @foreach ($user->friends as $friend)
                                    <li class="list-group-item">
                                        <a href="/user/{{ $friend->id }}">{{ $friend->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            You don't have friends.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
@endsection