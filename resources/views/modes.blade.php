@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Modes</h1>

                @if (count($modes) > 0)
                    <ul class="list-group">
                        @foreach ($modes as $mode)
                            <li class="list-group-item">
                                {{$mode->name}}
                                <a href="{{ route('delete-mode') }}"
                                   onclick="event.preventDefault();
                                           document.getElementById('delete{{$mode->id}}').submit();">
                                    <span type="" class="glyphicon glyphicon-remove"></span>
                                </a>
                                <form method="POST" action="{{ route('delete-mode') }}" id="delete{{$mode->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <input type="hidden" name="id" value="{{$mode->id}}">
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('store-mode') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name">Mode name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Mode</button>
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
