@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Friends</h1>

                @if (count($friends) > 0)
                    <ul class="list-group">

                        @foreach($friends as $friend)

                            <li class="list-group-item">

                                {{ $friend->name }}

                                <a href="{{ route('remove-friend') }}"
                                   onclick="event.preventDefault();
                                           document.getElementById('delete{{$friend->id}}').submit();">
                                    <span type="" class="glyphicon glyphicon-remove"></span>
                                </a>

                                <form method="POST" action="{{ route('remove-friend') }}" id="delete{{$friend->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <input type="hidden" name="friend_id" value="{{$friend->id}}">
                                </form>

                                <a href="{{ route('create-game', array('friend' => $friend->id)) }}">
                                    <span type="" class="glyphicon glyphicon-plus-sign"></span>
                                </a>

                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('add-friend') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name">Add Friend</label>
                        <input type="text" class="form-control" id="name" name="friend_name">
                        <input type="hidden" name="friend_id" id="friend_id">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Friend</button>
                </form>

                @if(count($errors))
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $('#name').autocomplete({
            source: '{{ route('find-user') }}',
            minLength: 1,
            select: function (event, ui) {
                $('#friend_id').val(ui.item.id);
            }
        })
    </script>
@endsection
