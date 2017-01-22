@if (Auth::user()->notifications->count() > 0)
    <ul class="notifications dropdown-menu">
        @foreach(Auth::user()->notifications->take(15) as $notification)
            <li class="notification {{ snake_case(class_basename($notification->type)) }}<?php echo $notification->read_at == null ? '' : ' read' ?>">
                @include('notifications.' . snake_case(class_basename($notification->type)))

                @if ($notification->read_at == null)
                    <form method="POST" action="{{ route('read-notification') }}">
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}

                        <input type="hidden" name="notification" value="{{ $notification->id }}"/>

                        <button type="submit" title="Marks as read" class="btn btn-default btn-sm">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        </button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@endif
