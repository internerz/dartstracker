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

                                    @foreach($friends as $friend)

                                        <li class="list-group-item" >

                                            {{ $friend->name }}

                                             <a href="{{ url('/friends') }}"
                                                onclick="event.preventDefault();
                                                        document.getElementById('delete{{$friend->id}}').submit();">
                                                 <span type="" class="glyphicon glyphicon-remove"></span>
                                             </a>

                                             <form method="POST" action="/friends" id="delete{{$friend->id}}">
                                                 {{ csrf_field() }}
                                                 {{ method_field('DELETE') }}
                                                 <input type="hidden" name="friend_id" value="{{$friend->id}}">
                                             </form>
                                         </li>
                                    @endforeach
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
