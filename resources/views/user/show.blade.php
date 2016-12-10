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
                            <a href="/friend/remove/{{ $user->id }}"><span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-minus"></span></a>
                        @else
                            <a href="/friend/add/{{ $user->id }}"><span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-plus"></span></a>
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