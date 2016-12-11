@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h3>
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
                </h3>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Stats
                    </div>

                    <div class="panel-body">
                        Stats
                    </div>
                </div>

                @if (\Auth::user()->id == $user->id)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Friends
                        </div>

                        <div class="panel-body">
                            @if (count($friends))
                                <ul class="list-group">
                                    @foreach ($friends as $friend)
                                        <li class="list-group-item">
                                            <a href="/user/{{ $friend->id }}">{{ $friend->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                You don't have friends.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
@endsection