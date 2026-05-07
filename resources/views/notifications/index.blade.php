@foreach($notifications as $notification)

<div class="card mb-2">

    <div class="card-body">

        <h5>
            {{ $notification->data['title'] }}
        </h5>

        <p>
            {{ $notification->data['message'] }}
        </p>

        <small>
            {{ $notification->created_at->diffForHumans() }}
        </small>

    </div>

</div>

@endforeach

{{ $notifications->links() }}