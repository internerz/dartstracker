@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>
                    Games
                    <a href="{{ route('create-game') }}" title="Create Game"><span class="glyphicon glyphicon-plus-sign"></span></a>
                </h1>

                @if (count($games) > 0)
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="hidden-xs">#</th>
                            <th>Date</th>
                            <th>Players</th>
                            <th>Rules</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($games as $i => $game)
                            <tr>
                                <th scope="row" class="hidden-xs">{{ $i+1 }}</th>
                                <td>
                                    <a href="{{ route('view-game', $game->id) }}">{{ date('Y-m-d @H:i', strtotime($game->created_at)) }}</a>
                                </td>
                                <td class="players">
                                    @foreach ($game->users as $player)
                                        <span>{{ $player->name }}</span><br/>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $game->mode->name }}<br/>
                                    {{ $game->ruleset }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
