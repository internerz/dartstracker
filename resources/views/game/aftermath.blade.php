@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>THIS GAME IS FINISHED</h1>
                <p>This game is over and *insert winner here* has won the game. Below, you can see how the games went</p>
            </div>
        </div>
        <div class="row">
            @foreach ($gameInformation as $i => $leg)
                <div class="col-xs-12 score-table">
                    <div style="background: green; color: red">Leg: #{{array_search($i, array_keys($gameInformation)) + 1}}</div>
                    <div>
                        @foreach($leg as $playerId => $player)
                            <div class="col-xs-{{12/count($leg)}}">
                                <div class="name" style="width:100%;">{{\App\User::find($playerId)->name}}</div>
                                @foreach($player as $rounds)
                                    @if (array_search($playerId, array_keys($leg)) % 2 == 0)
                                        <div class="col-xs-6">geworfen</div>
                                        <div class="col-xs-6">Rest</div>
                                    @else
                                        <div class="col-xs-6">Rest</div>
                                        <div class="col-xs-6">geworfen</div>
                                    @endif
                                    @foreach($rounds as $round)
                                        <div class="col-xs-6">{{$round->score}}</div>
                                        <div class="col-xs-6 {{$round->rest == 0 ? 'underline' : ''}}">{{$round->rest}}</div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection