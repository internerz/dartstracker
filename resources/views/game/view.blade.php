@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Game #{{ $game->id }}</div>

                    <div class="panel-body">
                        Mode: {{ $game->mode }}<br />
                        Ruleset: {{ $game->ruleset }}<br />
                        Players: @foreach ($game->users as $user)
                            {{ $user->name }},
                        @endforeach
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Round</div>

                    <div class="panel-body">
                        Active player: {{ $game->users->first()->name }}
                    </div>

                    <div class="panel-heading">Score</div>

                    <div class="panel-body" id="points">
                        @for ($i = 0; $i <= 21; $i++)
                            @if ($i == 0)
                                <a href="#" data-points="{{ $i }}">{{ $i }}</a><br />
                            @elseif ($i < 21)
                                <a href="#" data-points="{{ $i }}">{{ $i }}</a> | <a href="#" data-double="1">Double</a> | <a href="#" data-triple="1">Triple</a><br />
                            @else
                                <a href="#" data-points="25">Bullseye</a> | <a href="#" data-double="1">Double Bullseye</a><br /><br />
                            @endif
                        @endfor

                        <form method="POST" action="/game/{{ $game->id }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="points" value="0" />
                            <input type="hidden" name="double" value="0" />
                            <input type="hidden" name="triple" value="0" />

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            var points = {};

            $('#points').find('a').click(function() {
                if ($(this).data('points')) {
                    console.log($(this).data('points'));
                }

                return false;
            });
        });
    </script>
@endsection
