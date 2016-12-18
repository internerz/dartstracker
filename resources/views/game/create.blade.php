@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Create Game</h1>
                <form method="POST" action="{{ route('store-game') }}">
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
                        <label for="numberOfLegsToWin">Number of legs to win</label>
                        <input type="number" class="form-control" id="numberOfLegsToWin" name="legs" min="1" max="99"
                               value="2">
                    </div>

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

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            var users = [];
            var opponentList = $('#opponentList');

            $('#opponentSearch').autocomplete({
                source: function(request, response) {
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
        });
    </script>
@endsection
