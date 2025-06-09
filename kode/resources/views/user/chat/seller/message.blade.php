<div class="col-lg-12">
    <div class="seller-message-view">

        <div class="seller-store" data-role="seller" data-id={{ $seller->id }}>
            <div class="d-flex align-items-center gap-2">
                <div class="profile-image">
                    <img src="{{ show_image(file_path()['profile']['seller']['path'] . '/' . $seller->image ?? '', file_path()['profile']['seller']['size'] ?? '') }}"
                        alt="profile.jpg">
                </div>
                <h5>{{ $seller->name . ' ' . $seller->last_name }}</h5>
                <div class="user-status" data-role="seller" data-id={{ $seller->id }}>
                    <span class="status-text">Loading...</span>
                </div>
            </div>
        </div>

        <div class="messages">
            @forelse ($messages as $message)

                @php
                    $positionClass = $message->sender_role == 'seller' ? 'message-left' : 'message-right';

                    $imgURL =
                        $message->sender_role == 'seller'
                            ? show_image(
                                (file_path()['profile']['seller']['path'] ?? '') .
                                    '/' .
                                    ($message->seller->image ?? ''),
                                file_path()['profile']['seller']['size'] ?? '',
                            )
                            : show_image(
                                (file_path()['profile']['user']['path'] ?? '') .
                                    '/' .
                                    ($message->customer->image ?? ''),
                                file_path()['profile']['user']['size'] ?? '',
                            );
                @endphp

                <div class="message-single {{ $positionClass }} d-flex flex-column">
                    <div
                        class="user-area d-inline-flex @if ($message->sender_role != 'seller') justify-content-end @endif align-items-center mb-2 gap-3">
                        <div class="image">
                            <img src="{{ $imgURL }}" alt="profile.jpg">
                        </div>
                        <div class="meta">
                            <h6>
                                @if ($message->sender_role == 'seller')
                                    {{ $message->seller->name . ' ' . $message->seller->last_name }}
                                @else
                                    {{ $message->customer->name }}
                                @endif
                            </h6>
                        </div>
                    </div>
                    <div class="message-body">
                        <p>{{ $message->message }}</p>

                        @if ($message->files)
                            <div class="message-attachments">
                                @foreach ($message->files as $file)
                                    @php
                                        $fileData = json_decode($file, true);
                                    @endphp

                                    @if (is_array($fileData) && isset($fileData['type']) && $fileData['type'] === 'product')
                                        <!-- Product Attachment -->
                                        <div class="product-attachment mt-2">
                                            <div class="product-card">
                                                <div class="product-card-body">
                                                    <div class="product-info">
                                                        <h6>{{ $fileData['product_name'] ?? 'Product' }}</h6>
                                                        <small class="product-label">{{ translate('Produk') }}</small>
                                                    </div>
                                                    <div class="product-actions">
                                                        <a href="{{ $fileData['product_url'] ?? '#' }}" target="_blank"
                                                            class="btn-view-product">
                                                            <i class="bi bi-eye"></i> {{ translate('Lihat') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="message-time">
                            <span>{{ $message->created_at->format('H:i') }}</span>
                        </div>

                        @if ($message->sender_role != 'seller')
                            <span class="text-success check-message-icon">
                                <i
                                    class="bi bi-check2{{ $message->is_seen == App\Enums\StatusEnum::true->status() ? '-all' : '' }}"></i>
                            </span>
                        @endif
                    </div>
                </div>

            @empty
                @include('frontend.partials.empty', [
                    'message' => translate('No message found'),
                ])
            @endforelse
        </div>

        @php
            // Check if product attachment was already sent
            $productAlreadySent = false;
            if (request()->query('product_id')) {
                $productAlreadySent = $messages
                    ->where('sender_role', 'customer')
                    ->where('files', '!=', null)
                    ->filter(function ($message) {
                        if (!$message->files) {
                            return false;
                        }
                        foreach ($message->files as $file) {
                            $fileData = json_decode($file, true);
                            if (
                                is_array($fileData) &&
                                isset($fileData['type']) &&
                                $fileData['type'] === 'product' &&
                                $fileData['product_id'] == request()->query('product_id')
                            ) {
                                return true;
                            }
                        }
                        return false;
                    })
                    ->isNotEmpty();
            }
        @endphp

        <!-- Product URL Attachment (only show if not already sent) -->
        @if (request()->query('product_id') && !$productAlreadySent)
            @php
                $product = \App\Models\Product::find(request()->query('product_id'));
                if ($product) {
                    $productUrl =
                        $product->product_type == \App\Models\Product::DIGITAL
                            ? route('digital.product.details', [make_slug($product->name), $product->id])
                            : route('product.details', [make_slug($product->name), $product->id]);
                }
            @endphp
            @if (isset($product))
                <div class="product-attachment-preview">
                    <div class="product-preview-card">
                        <div class="product-preview-content">
                            <div class="product-preview-image">
                                <img src="{{ show_image((file_path()['product']['path'] ?? '') . '/' . ($product->image ?? ''), file_path()['product']['size'] ?? '') }}"
                                    alt="{{ $product->name }}">
                            </div>
                            <div class="product-info">
                                <h6>{{ $product->name }}</h6>
                                <small>{{ translate('Product akan dikirim sebagai attachment') }}</small>
                            </div>
                            <button type="button" class="remove-product-attachment">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Template Messages Container -->
        <div class="template-messages-container" id="templateMessagesContainer">
            <div class="template-messages-list">
                <div class="template-message-item" data-message="Apakah barang masih ada?">
                    <i class="bi bi-question-circle"></i>
                    <span>Apakah barang masih ada?</span>
                </div>
                <div class="template-message-item" data-message="Bisa nego harga?">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Bisa nego harga?</span>
                </div>
                <div class="template-message-item" data-message="Dimana lokasi COD-nya?">
                    <i class="bi bi-geo-alt"></i>
                    <span>Dimana lokasi COD-nya?</span>
                </div>
                <div class="template-message-item" data-message="Kapan bisa kirim?">
                    <i class="bi bi-calendar-event"></i>
                    <span>Kapan bisa kirim?</span>
                </div>
                <div class="template-message-item" data-message="Apakah ada garansi?">
                    <i class="bi bi-shield-check"></i>
                    <span>Apakah ada garansi?</span>
                </div>
                <div class="template-message-item" data-message="Bisa kirim hari ini?">
                    <i class="bi bi-truck"></i>
                    <span>Bisa kirim hari ini?</span>
                </div>
                <div class="template-message-item" data-message="Kondisi barang bagaimana?">
                    <i class="bi bi-star"></i>
                    <span>Kondisi barang bagaimana?</span>
                </div>
                <div class="template-message-item" data-message="Terima kasih">
                    <i class="bi bi-heart"></i>
                    <span>Terima kasih</span>
                </div>
            </div>
        </div>

        <form id="chatinput-form">
            @csrf
            <div class="message-inputs">
                <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                @if (request()->query('product_id') && !$productAlreadySent)
                    <input type="hidden" name="product_id" value="{{ request()->query('product_id') }}">
                @endif

                <!-- Template Message Button -->
                <button type="button" class="template-message-btn" title="{{ translate('Template Pesan') }}">
                    <i class="bi bi-chat-square-text"></i>
                </button>

                <textarea class="chat-message-input" name="message" placeholder="{{ translate('Ketik pesan...') }}" rows="1"></textarea>
                <button type="submit" class="message-submit" title="{{ translate('Kirim') }}">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    :root {
        --primary: #E40046;
        --secondary: #094966;
        --text-primary: #fff !important;
        --primary-light: rgba(228, 0, 70, 0.05) !important;
        --secondary-light: rgba(9, 73, 102, 0.2) !important;
    }

    /* Template Message Container */
    .template-messages-container {
        position: relative;
        margin-bottom: 8px;
        opacity: 0;
        transform: translateY(20px);
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 0;
        overflow: hidden;
    }

    .template-messages-container.show {
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
        max-height: 200px;
    }

    .template-messages-list {
        display: flex;
        gap: 8px;
        padding: 12px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: thin;
    }

    .template-messages-list::-webkit-scrollbar {
        height: 4px;
    }

    .template-messages-list::-webkit-scrollbar-track {
        background: var(--primary-light);
        border-radius: 2px;
    }

    .template-messages-list::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 2px;
    }

    .template-message-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: var(--primary-light);
        border: 1px solid rgba(228, 0, 70, 0.2);
        border-radius: 20px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.3s ease;
        font-size: 13px;
        color: var(--primary);
        font-weight: 500;
        min-width: fit-content;
    }

    .template-message-item:hover {
        background: var(--primary);
        color: var(--text-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(228, 0, 70, 0.3);
    }

    .template-message-item i {
        font-size: 12px;
        flex-shrink: 0;
    }

    .template-message-item span {
        flex-shrink: 0;
    }

    /* Template Message Button */
    .template-message-btn {
        background: var(--primary-light);
        color: var(--primary);
        border: 1px solid rgba(228, 0, 70, 0.2);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .template-message-btn:hover,
    .template-message-btn.active {
        background: var(--primary);
        color: var(--text-primary);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(228, 0, 70, 0.3);
    }

    /* Product Attachment Preview */
    .product-attachment-preview {
        margin-bottom: 12px;
        animation: slideIn 0.3s ease-out;
    }

    .product-preview-card {
        background: var(--primary-light);
        border: 1px solid rgba(228, 0, 70, 0.2);
        border-radius: 12px;
        overflow: hidden;
    }

    .product-preview-content {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
    }

    .product-preview-image img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(228, 0, 70, 0.2);
    }

    .product-preview-card .product-info {
        flex-grow: 1;
    }

    .product-preview-card .product-info h6 {
        color: var(--primary);
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .product-preview-card .product-info small {
        color: var(--secondary);
        font-size: 12px;
    }

    .remove-product-attachment {
        background: rgba(228, 0, 70, 0.1);
        color: var(--primary);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .remove-product-attachment:hover {
        background: var(--primary);
        color: var(--text-primary);
        transform: scale(1.1);
    }

    /* Enhanced Message Input */
    .message-inputs {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        padding: 12px;
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(228, 0, 70, 0.1);
    }

    .chat-message-input {
        flex: 1;
        border: none;
        outline: none;
        resize: none;
        padding: 10px 16px;
        border-radius: 20px;
        background: var(--primary-light);
        min-height: 40px;
        max-height: 120px;
        font-size: 14px;
        line-height: 1.4;
        color: var(--secondary);
    }

    .chat-message-input:focus {
        background: white;
        box-shadow: 0 0 0 2px rgba(228, 0, 70, 0.2);
    }

    .chat-message-input::placeholder {
        color: var(--secondary);
        opacity: 0.7;
    }

    .message-submit {
        background: linear-gradient(135deg, var(--primary) 0%, #c2003a 100%);
        color: var(--text-primary);
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(228, 0, 70, 0.3);
        flex-shrink: 0;
    }

    .message-submit:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(228, 0, 70, 0.4);
    }

    /* Product Attachment in Messages */
    .product-attachment {
        width: 100%;
        max-width: none;
        margin-top: 8px;
    }

    .product-card {
        background: var(--primary-light);
        border: 1px solid rgba(228, 0, 70, 0.2);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        box-shadow: 0 6px 20px rgba(228, 0, 70, 0.15);
        transform: translateY(-2px);
    }

    .product-card-body {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
    }

    .product-thumb img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(228, 0, 70, 0.2);
    }

    .product-card .product-info {
        flex-grow: 1;
    }

    .product-card .product-info h6 {
        color: var(--primary);
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .product-label {
        color: var(--secondary);
        font-size: 12px;
        background: var(--secondary-light);
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-view-product {
        background: var(--primary);
        color: var(--text-primary);
        border: none;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-view-product:hover {
        background: var(--secondary);
        color: var(--text-primary);
        transform: scale(1.05);
        text-decoration: none;
    }

    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .product-card-body {
            padding: 12px;
        }

        .template-messages-list {
            padding: 8px;
        }

        .template-message-item {
            font-size: 12px;
            padding: 6px 10px;
        }

        .message-inputs {
            padding: 8px;
        }

        .chat-message-input {
            padding: 8px 12px;
            min-height: 36px;
        }

        .message-submit {
            width: 40px;
            height: 40px;
        }
    }

    /* Enhanced message area styling */
    .messages {
        position: relative;
    }

    .message-single .message-body {
        position: relative;
    }

    .check-message-icon {
        color: var(--primary) !important;
    }

    .seller-store h5 {
        color: var(--secondary);
        font-weight: 600;
    }

    .user-status .status-text {
        color: var(--primary);
        font-weight: 500;
        font-size: 12px;
    }
</style>
