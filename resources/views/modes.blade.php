@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Modes</div>

                    <div class="panel-body">
                        @if (count($modes) > 0)
                            <ul class="list-group">
                                @foreach ($modes as $mode)
                                    <li class="list-group-item" >
                                        {{$mode->name}}
                                            <a href="{{ url('/modes') }}"
                                                onclick="event.preventDefault();
                                                    document.getElementById('delete{{$mode->id}}').submit();">
                                                <span type="" class="glyphicon glyphicon-remove"></span>
                                            </a>
                                            <form method="POST" action="/modes" id="delete{{$mode->id}}">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <input type="hidden" name="id" value="{{$mode->id}}">
                                            </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="/modes">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="name">Mode name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Mode</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
