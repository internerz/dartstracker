@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Game #{{ $game->id }}</div>

                    <div class="panel-body">
                        Mode: {{ $game->mode->name }}<br/>
                        Ruleset: {{ $game->ruleset }}<br/>
                        Number of legs to win: {{ $game->number_of_legs_to_win }}<br/>
                        Current leg: {{ $game->legs->count() }}<br/>
                        Current player: {{ $game->getCurrentPlayer()->name }}<br/>
                        Next player: {{ $game->getNextPlayer()->name }}<br/>
                        @if ($currentLeg)
                            Current leg ID: {{ $currentLeg->id }}<br/>
                        @endif
                        Players: @foreach ($game->users as $user)
                            {{ $user->name }},
                        @endforeach
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Round</div>

                    <div class="panel-body">
                        Active player: <span id="playerName">{{ $game->users->first()->name }}</span>
                    </div>

                    <div class="panel-heading">Score</div>

                    @if ($currentLeg)
                        <div class="panel-body" id="points">
                            @for ($i = 0; $i <= 21; $i++)
                                @if ($i == 0)
                                    <a href="#" data-points="{{ $i }}">{{ $i }}</a><br/>
                                @elseif ($i < 21)
                                    <a href="#" data-points="{{ $i }}">{{ $i }}</a><span> | <a href="#"
                                                                                               data-points="{{ $i }}"
                                                                                               data-double="1">{{ $i }}
                                            Double</a> | <a href="#" data-points="{{ $i }}" data-triple="1">{{ $i }}
                                            Triple</a></span><br/>
                                @else
                                    <a href="#" data-points="25">Bullseye</a> | <a href="#" data-points="25"
                                                                                   data-double="1">Double Bullseye</a>
                                    <br/><br/>
                                @endif
                            @endfor

                            <form method="POST" action="/game/{{ $game->id }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="points" value="0"/>
                                <input type="hidden" name="double" value="0"/>
                                <input type="hidden" name="triple" value="0"/>

                                <button type="submit" class="btn btn-primary disabled" id="submit">Submit</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dartboard</div>

                    <div class="panel-body">
                        {{svg_icon('dartboard')->inline()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($currentLeg)
        <script type="text/javascript"
                src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                        var points = {};
                        var player = {
                            id: "{{ \Auth::user()->id }}",
                            name: "{{ \Auth::user()->name }}",
                        };
                        points['points'] = [];
                        var button = $('#submit');
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        var board = $('#board');


                        button.click(function () {
                            var data = JSON.stringify(points);
                            button.addClass('disabled');
                            points['points'] = [];

                            $.ajax({
                                type: "POST",
                                url: window.location.href.split('#')[0],
                                data: {
                                    _token: csrf_token,
                                    user: player.id,
                                    leg: "{{ $currentLeg->id }}",
                                    points: data,
                                },
                                success: function (response) {
                                    console.log('response', JSON.parse(response));
                                    player = {
                                        id: JSON.parse(response)['nextPlayerId'],
                                        name: JSON.parse(response)['nextPlayerName']
                                    };

                                    updatePlayerStrings();
                                },
                                dataType: 'json'
                            });

                            return false;
                        });

                        function updatePlayerStrings() {
                            $('#playerName').text(player.name);
                        };

                        function updateScore(el) {

                            var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                            if (points['points'].length < 3) {
                                switch (scoreParameters[0]) {
                                    case "s":
                                        points['points'].push([scoreParameters[1], 1]);
                                        break;
                                    case "d":
                                        points['points'].push([scoreParameters[1], 2]);
                                        break;
                                    case "t":
                                        points['points'].push([scoreParameters[1], 3]);
                                        break;
                                    case "Bull":
                                        points['points'].push([scoreParameters[0], 1]);
                                        break;
                                    case "Outer":
                                        points['points'].push([scoreParameters[0], 1]);
                                        break;
                                    default:
                                        console.log("something bad happened");
                                }
                            }

                            if (points['points'].length == 3) {
                                button.removeClass('disabled');
                            }
                        }

                        board.find("#areas g").children().hover(
                                function () {
                                    $(this).css("opacity", "0.5").css('cursor', 'pointer');
                                },
                                function () {
                                    $(this).css("opacity", "1");
                                }
                        ).click(function () {
                            updateScore($(this));
                        });


                        /*

                         some helper function(s), maybe not needed
                         */
                        function getScorePoints(el) {
                            var scoredPoints = 0;
                            var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);


                            switch (scoreParameters[0]) {
                                case "s":
                                    scoredPoints = 1 * scoreParameters[1];
                                    break;
                                case "d":
                                    scoredPoints = 2 * scoreParameters[1];
                                    break;
                                case "t":
                                    scoredPoints = 3 * scoreParameters[1];
                                    break;
                                case "Bull":
                                    scoredPoints = 50;
                                    break;
                                case "Outer":
                                    scoredPoints = 25;
                                    break;
                                default:
                                    console.log("something happened");
                            }

                            return scoredPoints;
                        }

                        $('#points').find('a').click(function () {
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
                    }
            );
        </script>

    @endif
@endsection
