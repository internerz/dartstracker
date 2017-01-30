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
                    <p id="stats"></p>
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
        // Adapted from http://martin.ankerl.com/2009/12/09/how-to-create-random-colors-programmatically/

        var randomColor = (function(){
            var golden_ratio_conjugate = 0.618033988749895;
            var h = Math.random();

            var hslToRgb = function (h, s, l){
                var r, g, b;

                if(s == 0){
                    r = g = b = l; // achromatic
                }else{
                    function hue2rgb(p, q, t){
                        if(t < 0) t += 1;
                        if(t > 1) t -= 1;
                        if(t < 1/6) return p + (q - p) * 6 * t;
                        if(t < 1/2) return q;
                        if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                        return p;
                    }

                    var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                    var p = 2 * l - q;
                    r = hue2rgb(p, q, h + 1/3);
                    g = hue2rgb(p, q, h);
                    b = hue2rgb(p, q, h - 1/3);
                }

                return '#'+Math.round(r * 255).toString(16)+Math.round(g * 255).toString(16)+Math.round(b * 255).toString(16);
            };

            return function(){
                h += golden_ratio_conjugate;
                h %= 1;
                return hslToRgb(h, 0.5, 0.60);
            };
        })();
    </script>
    <script type="text/javascript">
        var data2 = {!! json_encode($userLegPointStats) !!};
        console.log(typeof data2, data2, _.size(data2));
        var data = [3, 6, 2, 7, 5, 2, 1, 3, 8, 9, 2, 5, 7];
        w = 800;
        h = 400;
        margin = 20;
//        var xMax = data2.length;
        var xMax = 0;
//        var yMax = d3.max(data);
        var yMax = 501;

        for(var data in data2) {
            if(!(typeof data2[data] === "undefined")){
                if(_.size(data2[data]) > xMax) {
                    xMax = _.size(data2[data]);
                }
            }
        };

        y = d3.scaleLinear().domain([0, yMax]).range([0 + margin, h - margin]);
        x = d3.scaleLinear().domain([0, xMax]).range([0 + margin, w - margin]);

        var vis = d3.select("#stats")
                .append("svg:svg")
                .attr("width", w)
                .attr("height", h);

        var g = vis.append("svg:g")
                .attr("transform", "translate(" + margin + "," + h + ")");

        var line = d3.line()
                .x(function(d,i) { return x(i); })
                .y(function(d) { return -1 * y(d.rest); });

        function drawGraph(data) {
            var color = randomColor();

            g.append("svg:path")
                .attr("d", line(data))
                .style("stroke", color);

            g.append("svg:line")
                    .attr("x1", x(0))
                    .attr("y1", -1 * y(0))
                    .attr("x2", x(w))
                    .attr("y2", -1 * y(0));

            g.append("svg:line")
                    .attr("x1", x(0))
                    .attr("y1", -1 * y(0))
                    .attr("x2", x(0))
                    .attr("y2", -1 * y(yMax));

            g.selectAll(".xLabel")
                    .data(x.ticks(5))
                    .enter().append("svg:text")
                    .attr("class", "xLabel")
                    .text(String)
                    .attr("x", function(d) { return x(d) })
                    .attr("y", 0)
                    .attr("text-anchor", "middle")
                    .attr("transform", "translate(0,-5)");

            g.selectAll(".yLabel")
                    .data(y.ticks(4))
                    .enter().append("svg:text")
                    .attr("class", "yLabel")
                    .text(String)
                    .attr("x", 0)
                    .attr("y", function(d) { return -1 * y(d) })
                    .attr("text-anchor", "right")
                    .attr("dy", 4)
                    .attr("transform", "translate(-12,0)");

            g.selectAll(".xTicks")
                    .data(x.ticks(5))
                    .enter().append("svg:line")
                    .attr("class", "xTicks")
                    .attr("x1", function(d) { return x(d); })
                    .attr("y1", -1 * y(0))
                    .attr("x2", function(d) { return x(d); })
                    .attr("y2", -1 * y(0.3));

            g.selectAll(".yTicks")
                    .data(y.ticks(4))
                    .enter().append("svg:line")
                    .attr("class", "yTicks")
                    .attr("y1", function(d) { return -1 * y(d); })
                    .attr("x1", x(-0.05))
                    .attr("y2", function(d) { return -1 * y(d); })
                    .attr("x2", x(0));
        }

        for(var data in data2) {
            if(!(typeof data2[data] === "undefined")){
                drawGraph(data2[data]);
            }
        };

    </script>
@endsection

