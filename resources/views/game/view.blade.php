@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Game #{{ $game->id }}</div>

                    <div class="panel-body">
                        Mode: {{ $game->mode->name }}<br />
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
                                <a href="#" data-points="{{ $i }}">{{ $i }}</a><span> | <a href="#" data-points="{{ $i }}" data-double="1">{{ $i }} Double</a> | <a href="#" data-points="{{ $i }}" data-triple="1">{{ $i }} Triple</a></span><br />
                            @else
                                <a href="#" data-points="25">Bullseye</a> | <a href="#" data-points="25" data-double="1">Double Bullseye</a><br /><br />
                            @endif
                        @endfor

                        <form method="POST" action="/game/{{ $game->id }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="points" value="0" />
                            <input type="hidden" name="double" value="0" />
                            <input type="hidden" name="triple" value="0" />

                            <button type="submit" class="btn btn-primary disabled" id="submit">Submit</button>
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
            points['player'] = "{{ \Auth::user()->id }}";
            points['points'] = [];
            var button = $('#submit');

            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajaxPrefilter(function(options, originalOptions, jqXHR){
                if (options.type.toLowerCase() === "post") {
                    options.data = options.data || "";
                    options.data += options.data?"&":"";
                    options.data += "_token=" + csrf_token;
                }
            });

            $('#points').find('a').click(function() {
                if ($(this).data('points') && points['points'].length < 3) {
                    if ($(this).data('double')) {
                        points['points'].push([$(this).data('points'), 2]);
                    } else if ($(this).data('trriple')) {
                        points['points'].push([$(this).data('points'), 3]);
                    } else {
                        points['points'].push([$(this).data('points'), 1]);
                    }
                }

                if (points['points'].length == 3) {
                    button.removeClass('disabled');
                }

                return false;
            });

            button.click(function() {
                var data = JSON.stringify(points);
console.log(data);
//                button.addClass('disabled');
//                points['points'] = [];

                $.ajax({
                    type: "POST",
                    url: window.location.href.split('#')[0],
                    data: data,
                    success: function() {
                        console.log('success');
                    },
                    dataType: 'json'
                });

                return false;
            });


        });
    </script>
@endsection
