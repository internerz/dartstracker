@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Create Game</h1>
                @if (Auth::guest())
                    <form method="POST" action="{{ route('view-game-guest') }}">
                @else
                    <form method="POST" action="{{ route('store-game') }}">
                @endif
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="mode">Choose mode</label>
                        <select class="form-control" id="mode" name="mode">
                            @if (count($modes) > 0)
                                @foreach ($modes as $mode)
                                    <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ruleset">Ruleset</label>
                        <select class="form-control" id="ruleset" name="ruleset">
                            <option value="1">Double-Out</option>
                            <option value="2">Double-In</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start">Starting Rule</label>
                        <select class="form-control" id="starting-rule" name="starting-rule">
                            <option value="6">Single-In</option>
                            <option value="1">Double-In</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ending-rule" name="ending-rule">Ending Rule</label>
                        <select class="form-control" id="ending-rule" name="ending-rule">
                            <option value="2">Double-Out</option>
                            <option value="7">Single-Out</option>
                            <!--option value="5">Bulls-Out</option-->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="numberOfLegsToWin">Number of legs to win</label>
                        <input type="number" class="form-control" id="numberOfLegsToWin" name="legs" min="1" max="99"
                               value="2">
                    </div>

                    @if (Auth::guest())
                        <div class="form-group" id="players">
                            <label for="player1">Player 1</label>
                            <input type="text" class="form-control" id="player1" name="player1">
                            <label for="player2">Player 2</label>
                            <input type="text" class="form-control" id="player2" name="player2">
                        </div>
                    @else
                        <div class="form-group">
                            <label for="opponentSearch">Search for opponent</label>
                            <input type="text" class="form-control" id="opponentSearch">
                            <input type="hidden" id="opponents" name="opponents">
                        </div>

                        <label>Opponents</label>
                        <div class="list-group" id="opponentList">
                            <a href="#" class="list-group-item hidden" title="Remove opponent">
                                <span class="name"></span> <span class="glyphicon glyphicon-trash pull-right"
                                                                 aria-hidden="true"></span>
                            </a>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {

            @if (Auth::guest())
            $('form').submit(function () {
                var states = JSON.parse('{!! $states !!}');
                var gameInfo = {};
                gameInfo['users'] = [];
                var stateInfo = {};
                var scoreInfo = {};

                // get player names
                $('#players').find('input').each(function (i) {
                    var playerName = $(this).val().trim().length ? $(this).val().trim() : 'Player' + (i+1);
                    gameInfo['users'].push({ 'id': i, 'name': playerName });

                    // set state info
                    var state = states.filter(function( obj ) {
                        return obj.id == $('#starting-rule').val();
                    });

                    stateInfo[i] = {
                        'id': $('#starting-rule').val(),
                        'name': state[0].name,
                        'phase': state[0].phase
                    };

                    // set score info
                    scoreInfo[i] = parseInt($('#mode').find('option:selected').text());
                });

                // get the rest of the info
                $(this).find('input, select').each(function () {
                    if ($(this).attr('name') != '_token' && !$(this).attr('name').match('/player/')) {
                        gameInfo[$(this).attr('name')] = $(this).val();
                    }
                });

                // set current player
                gameInfo['currentPlayer'] = gameInfo['users'][0];

                toLocalStorage('gameInfo', gameInfo);
                toLocalStorage('stateInfo', stateInfo);
                toLocalStorage('scoreInfo', scoreInfo);
            });

                    @else
            var users = [];
            var opponentList = $('#opponentList');

            var friend = {!! json_encode($friend) !!};

            function addOpponent(friend) {
                if (users.indexOf(friend.id) == -1) {
                    users.push(friend.id);
                    var element = opponentList.find('a.hidden').clone();
                    element
                            .removeClass('hidden')
                            .data('id', friend.id)
                            .find('.name').text(friend.name);

                    element.click(function () {
                        users.splice(users.indexOf(friend.id), 1);
                        $(this).remove();
                        $('#opponents').val(JSON.stringify(users));
                        return false;
                    });
                    element.appendTo(opponentList);
                    $('#opponents').val(JSON.stringify(users));
                }
            };

            if (friend != null) {
                addOpponent(friend);
            }

            $('#opponentSearch').autocomplete({
                source: function (request, response) {
                    $.getJSON('{{ route('find-user') }}', {
                        term: request.term,
                        friendsFirst: 1
                    }, response);
                },
                minLength: 1,
                select: function (event, ui) {
                    if (users.indexOf(ui.item.id) == -1) {
                        users.push(ui.item.id);

                        var element = opponentList.find('a.hidden').clone();
                        element
                                .removeClass('hidden')
                                .data('id', ui.item.id)
                                .find('.name').text(ui.item.value);

                        element.click(function () {
                            users.splice(users.indexOf(ui.item.id), 1);
                            $(this).remove();
                            $('#opponents').val(JSON.stringify(users));
                            return false;
                        });
                        element.appendTo(opponentList);
                    }
                },
                close: function () {
                    this.value = '';
                    $('#opponents').val(JSON.stringify(users));
                }
            });
            @endif
        });
    </script>
@endsection
