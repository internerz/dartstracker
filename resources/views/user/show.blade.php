@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>
                    {{ $user->name }}
                    @if (\Auth::user()->id == $user->id)
                        <a href="{{ route('edit-profile') }}"><span class="glyphicon glyphicon-edit"></span></a>
                    @elseif (\Auth::check())
                        @if ($areFriends)
                            <a href="{{ route('remove-friend') }}"
                               onclick="event.preventDefault();
                                               document.getElementById('deleteFriendForm').submit();">
                                <span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-minus"></span>
                            </a>

                            <form method="POST" action="{{ route('remove-friend') }}" id="deleteFriendForm">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <input type="hidden" name="friend_id" value="{{ $user->id }}">
                            </form>
                        @else
                            <a href="{{ route('add-friend') }}"
                               onclick="event.preventDefault();
                                               document.getElementById('addFriendForm').submit();">
                                <span class="glyphicon glyphicon-user"></span><span
                                        class="glyphicon glyphicon-plus"></span>
                            </a>

                            <form method="POST" action="{{ route('add-friend') }}" id="addFriendForm">
                                {{ csrf_field() }}
                                <input type="hidden" name="friend_id" value="{{ $user->id }}">
                            </form>
                        @endif
                    @endif
                </h1>

                <div class="stats">
                    <h2>Stats</h2>
                    <p id="stats">Stats</p>
                </div>

                @if (\Auth::user()->id == $user->id)
                    <div class="friends">
                        <h2>Friends</h2>

                        @if (count($user->friends))
                            <ul class="list-group">
                                @foreach ($user->friends as $friend)
                                    <li class="list-group-item">
                                        <a href="{{ route('show-user', $friend->id) }}">{{ $friend->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            You don't have friends.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        var data2 = {!! json_encode($userLegPointStats) !!};
        console.log(data2);
        var data = [3, 6, 2, 7, 5, 2, 1, 3, 8, 9, 2, 5, 7];
        w = 400;
        h = 200;
        margin = 20;
        y = d3.scaleLinear().domain([0, d3.max(data)]).range([0 + margin, h - margin]);
        x = d3.scaleLinear().domain([0, data.length]).range([0 + margin, w - margin]);

        var vis = d3.select("#stats")
                .append("svg:svg")
                .attr("width", w)
                .attr("height", h);

        var g = vis.append("svg:g")
                .attr("transform", "translate(0, 200)");

        var line = d3.line()
                .x(function(d,i) { return x(i); })
                .y(function(d) { return -1 * y(d); });

        g.append("svg:path").attr("d", line(data));

        g.append("svg:line")
                .attr("x1", x(0))
                .attr("y1", -1 * y(0))
                .attr("x2", x(w))
                .attr("y2", -1 * y(0));

        g.append("svg:line")
                .attr("x1", x(0))
                .attr("y1", -1 * y(0))
                .attr("x2", x(0))
                .attr("y2", -1 * y(d3.max(data)));

        g.selectAll(".xLabel")
                .data(x.ticks(5))
                .enter().append("svg:text")
                .attr("class", "xLabel")
                .text(String)
                .attr("x", function(d) { return x(d) })
                .attr("y", 0)
                .attr("text-anchor", "middle");

        g.selectAll(".yLabel")
                .data(y.ticks(4))
                .enter().append("svg:text")
                .attr("class", "yLabel")
                .text(String)
                .attr("x", 0)
                .attr("y", function(d) { return -1 * y(d) })
                .attr("text-anchor", "right")
                .attr("dy", 4);

        g.selectAll(".xTicks")
                .data(x.ticks(5))
                .enter().append("svg:line")
                .attr("class", "xTicks")
                .attr("x1", function(d) { return x(d); })
                .attr("y1", -1 * y(0))
                .attr("x2", function(d) { return x(d); })
                .attr("y2", -1 * y(-0.3));

        g.selectAll(".yTicks")
                .data(y.ticks(4))
                .enter().append("svg:line")
                .attr("class", "yTicks")
                .attr("y1", function(d) { return -1 * y(d); })
                .attr("x1", x(-0.3))
                .attr("y2", function(d) { return -1 * y(d); })
                .attr("x2", x(0));
    </script>
@endsection