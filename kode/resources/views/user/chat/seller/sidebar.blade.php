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
            <div class="online-status" data-role="seller" data-id="{{ $seller->id }}">
                <span class="status-dot"></span> <!-- dot bulat -->
                <span class="status-text">Loading...</span>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="title w-auto">
                    {{ $seller->name . ' ' . $seller->last_name }}
                </div>
                <div class="time">
                    @if($seller->latestConversation && $seller->latestConversation->created_at)
                        {{ $seller->latestConversation->created_at->diffForHumans() }}
                    @else
                        {{ translate('Just now') }}
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <p>
                    @if($seller->latestConversation && $seller->latestConversation->message)
                        {{ $seller->latestConversation->message }}
                    @else
                        {{ translate('Start a conversation...') }}
                    @endif
                </p>
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
