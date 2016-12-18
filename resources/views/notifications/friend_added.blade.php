How nice. <a href="{{ route('show-user', $notification->data['user_id']) }}">
    {{ \App\User::find($notification->data['user_id'])->name }}
</a> added you as a friend.

@if (\App\User::find($notification->data['user_id'])->friendStatuses()->where('friend_id', Auth::id())->first()->status == 0)
    <form method="POST" action="{{ route('accept-friend', $notification->data['user_id']) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        <input type="hidden" name="notification" value="{{ $notification->id }}"/>

        <button type="submit" title="Accept friend" class="btn btn-default btn-sm">
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        </button>
    </form>

    <form method="POST" action="{{ route('reject-friend', $notification->data['user_id']) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        <input type="hidden" name="notification" value="{{ $notification->id }}"/>

        <button type="submit" title="Reject friend" class="btn btn-default btn-sm">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        </button>
    </form>
@endif