@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Games</div>

                    <div class="panel-body">
                        <form method="POST" action="/game">
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
                                <input type="number" class="form-control" id="numberOfLegsToWin" name="legs" min="1" max="99" value="2">
                            </div>

                            <div class="form-group">
                                <label for="opponentSearch">Search for opponent (ID of a user)</label>
                                <input type="number" class="form-control" id="opponentSearch" name="opponent" min="1" max="9999" value="2">
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
