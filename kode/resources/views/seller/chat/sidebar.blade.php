@forelse ($customers as $customer)
    @php
        $isSeen = true;
        if (
            $customer->latestSellerMessage &&
            $customer->latestSellerMessage->sender_role == 'customer' &&
            $customer->latestSellerMessage->is_seen == 0
        ) {
            $isSeen = false;
        }
    @endphp

    <div class="session-single get-chat {{ !$isSeen ? 'unread-message' : '' }}" id="{{ $customer->id }}">
        <div class="seller-icon">
            <img src="{{ show_image(file_path()['profile']['user']['path'] . '/' . $customer->image, file_path()['profile']['user']['size']) }}"
                alt="profile.jpg">
        </div>
        <div class="content">
            <div class="online-status" data-role="customer" data-id="{{ $customer->id }}">
                <span class="status-dot"></span> <!-- dot bulat -->
                <span class="status-text">Loading...</span>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="title w-auto">
                    {{ $customer->name . ' ' . $customer->last_name }}
                </div>
                <div class="time">
                    {{ $customer->latestSellerMessage->created_at->diffForHumans() }}
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <p>{{ $customer->latestSellerMessage->message }}</p>
                @if (!$isSeen)
                    <span class="message-num">
                        {{ translate('New') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="empty-list">
        @include('frontend.partials.empty', [
            'message' => translate('No customer found'),
        ])
    </div>
@endforelse
