@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
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
                    </div>

                    <div class="panel-body">
                        Stats
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
@endsection