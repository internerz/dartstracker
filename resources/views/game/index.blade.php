@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Games</div>

                    <div class="panel-body">
                        <a href="/game/create">Create game</a>
                        @if (count($games) > 0)
                            <ul class="list-group">
                                @foreach ($games as $game)
                                    <li class="list-group-item" <?php echo ($game->winner_user_id > 0) ? ' disabled' : ''; ?>><a href="/game/{{ $game->id }}">{{ $game->created_at }}, {{ $game->mode->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
