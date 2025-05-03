@forelse ($sellers as $seller)
    @php
        $isSeen = true;
        if (
            $seller->latestConversation &&
            $seller->latestConversation->sender_role == 'seller' &&
            $seller->latestConversation->is_seen == 0
        ) {
            $isSeen = false;
        }

    @endphp

    <div class="session-single get-chat {{ !$isSeen ? 'unread-message' : '' }}" id="{{ $seller->id }}">
        <div class="seller-icon">
            <img src="{{ show_image(file_path()['profile']['seller']['path'] . '/' . $seller->image, file_path()['profile']['seller']['size']) }}"
                alt="profile.jpg">
        </div>
        <div class="content">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="title w-auto">
                    {{ $seller->name . ' ' . $seller->last_name }}
                </div>
                <div class="time">
                    {{ $seller->latestConversation->created_at->diffForHumans() }}
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <p>{{ $seller->latestConversation->message }}</p>
                @if (!$isSeen)
                    <span class="message-num">
                        {{ translate('New') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@empty
    @include('frontend.partials.empty', [
        'message' => translate('No user found'),
    ])
@endforelse
