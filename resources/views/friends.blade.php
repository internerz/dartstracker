@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Friends</div>
                        <div class="panel-body">

                            @if (count($friends) > 0)
                                <ul class="list-group">
                                    @for ($i = 0; $i < sizeof($friends); $i++)

                                        <li class="list-group-item" >

                                            {{ $friends_names[$i] }}

                                            <a href="{{ url('/friends') }}"
                                               onclick="event.preventDefault();
                                                       document.getElementById('delete{{$friends[$i]->friends_id}}').submit();">
                                                <span type="" class="glyphicon glyphicon-remove"></span>
                                            </a>

                                            <form method="POST" action="/friends" id="delete{{$friends[$i]->friends_id}}">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <input type="hidden" name="friends_id" value="{{$friends[$i]->friends_id}}">
                                            </form>
                                        </li>
                                            @endfor
                                </ul>
                            @endif

                            <form method="POST" action="/friends">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="name">Add Friend (id)</label>
                                    <input type="number" class="form-control" id="name" name="friend_id">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Friend</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
