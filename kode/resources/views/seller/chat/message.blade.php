<div class="col-lg-12">
    <div class="seller-message-view">

        <div class="seller-store">
            <div class="d-flex align-items-center gap-2">
                <div class="profile-image">
                    <img src="{{ show_image(file_path()['profile']['user']['path'] . '/' . $user->image, file_path()['profile']['user']['size']) }}"
                        alt="profile.jpg">
                </div>
                <h5>{{ $user->name . ' ' . $user->last_name }}</h5>
                <div class="user-status" data-role="customer" data-id="{{ $user->id }}">
                    <span class="status-text">Loading...</span>
                </div>
            </div>
        </div>

        <div class="messages">
            @forelse ($messages as $message)
                @php
                    // Update position class untuk handle chatbot
                    // Seller dan chatbot di kiri, customer di kanan
                    $positionClass = $message->sender_role == 'customer' ? 'message-left' : 'message-right';

                    // Update image URL untuk chatbot
                    if ($message->sender_role == 'chatbot') {
                        $imgURL = asset('assets/images/chatbot-avatar.jpg'); // Atau default chatbot image
                    } else {
                        $imgURL =
                            $message->sender_role == 'customer'
                                ? show_image(
                                    file_path()['profile']['user']['path'] . '/' . ($message->customer->image ?? ''),
                                    file_path()['profile']['user']['size'] ?? '',
                                )
                                : show_image(
                                    file_path()['profile']['seller']['path'] . '/' . ($message->seller->image ?? ''),
                                    file_path()['profile']['seller']['size'] ?? '',
                                );
                    }
                @endphp

                <div class="message-single {{ $positionClass }} d-flex flex-column">
                    {{-- User area untuk customer (kanan) --}}
                    @if ($message->sender_role == 'customer')
                        <div class="user-area d-inline-flex align-items-center mb-2 gap-3">
                            <div class="image order-1">
                                <img src="{{ $imgURL }}" alt="profile.jpg">
                            </div>
                            <div class="meta order-2">
                                <h6 class="text-end">
                                    {{ $message->customer->name . ' ' . $message->customer->last_name }}</h6>
                            </div>
                        </div>
                    @else
                        {{-- User area untuk seller & chatbot (kiri) --}}
                        <div class="user-area d-inline-flex justify-content-end align-items-center mb-2 gap-3">
                            <div class="meta">
                                <h6>
                                    @if ($message->sender_role == 'seller')
                                        {{ $message->seller->name . ' ' . $message->seller->last_name }}
                                    @elseif ($message->sender_role == 'chatbot')
                                        <span class="chatbot-name">
                                            <i class="bi bi-robot"></i>
                                            {{ translate('Chatbot') }} -
                                            {{ $message->seller->sellerShop->shop_name ?? 'Assistant' }}
                                        </span>
                                    @endif
                                </h6>
                            </div>
                            <div class="image">
                                <img src="{{ $imgURL }}" alt="profile.jpg">
                            </div>
                        </div>
                    @endif

                    <div
                        class="message-body @if ($message->sender_role == 'chatbot') chatbot-message @endif @if ($message->source == 'whatsapp') whatsapp-message @endif">
                        {{-- Check icon untuk seller messages --}}
                        @if ($message->sender_role == 'seller')
                            <span class="text-success check-message-icon">
                                <i
                                    class="bi bi-check2{{ $message->is_seen == App\Enums\StatusEnum::true->status() ? '-all' : '' }}"></i>
                            </span>
                        @endif

                        {{-- Chatbot indicator --}}
                        @if ($message->sender_role == 'chatbot')
                            <div class="chatbot-indicator mb-1">
                                <small class="text-muted">
                                    <i class="bi bi-cpu"></i> {{ translate('Automated Response') }}
                                </small>
                            </div>
                        @endif

                        {{-- WhatsApp indicator --}}
                        @if ($message->source == 'whatsapp')
                            <div class="whatsapp-indicator mb-1">
                                <small class="text-success">
                                    <i class="bi bi-whatsapp"></i> {{ translate('WhatsApp') }}
                                </small>
                            </div>
                        @endif

                        <p>{{ $message->message }}</p>

                        @if ($message->files)
                            @php
                                $hasProductAttachment = false;
                                $productData = null;

                                if ($message->files && is_array($message->files)) {
                                    foreach ($message->files as $file) {
                                        $data = json_decode($file, true);
                                        if (
                                            is_array($data) &&
                                            ($data['type'] ?? null) === 'product' &&
                                            isset($data['product_id'], $data['product_name'], $data['product_url'])
                                        ) {
                                            $hasProductAttachment = true;
                                            $productData = $data;
                                            break; // Hanya ambil satu product JSON pertama
                                        }
                                    }
                                }
                            @endphp

                            <!-- Product Attachment -->
                            @if ($hasProductAttachment && $productData)
                                <div class="product-attachment mt-2">
                                    <div class="product-card">
                                        <div class="product-card-body">
                                            <div class="product-info">
                                                <h6>{{ $productData['product_name'] ?? 'Product' }}</h6>
                                                <small class="product-label">{{ translate('Produk') }}</small>
                                            </div>
                                            <div class="product-actions">
                                                <a href="{{ $productData['product_url'] ?? '#' }}" target="_blank"
                                                    class="btn-view-product">
                                                    <i class="bi bi-eye"></i> {{ translate('Lihat') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="message-time">
                            <span>{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>

            @empty
                @include('frontend.partials.empty', [
                    'message' => translate('No message found'),
                ])
            @endforelse
        </div>

        <!-- Template Messages Container -->
        <div class="template-messages-container" id="templateMessagesContainer">
            <div class="template-messages-list">
                <div class="template-message-item" data-message="Terima kasih atas pesanannya">
                    <i class="bi bi-heart"></i>
                    <span>Terima kasih atas pesanannya</span>
                </div>
                <div class="template-message-item" data-message="Barang ready stock">
                    <i class="bi bi-check-circle"></i>
                    <span>Barang ready stock</span>
                </div>
                <div class="template-message-item" data-message="Akan segera dikirim">
                    <i class="bi bi-truck"></i>
                    <span>Akan segera dikirim</span>
                </div>
                <div class="template-message-item" data-message="Lokasi COD di toko kami">
                    <i class="bi bi-geo-alt"></i>
                    <span>Lokasi COD di toko kami</span>
                </div>
                <div class="template-message-item" data-message="Harga sudah fix">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Harga sudah fix</span>
                </div>
                <div class="template-message-item" data-message="Ada garansi resmi">
                    <i class="bi bi-shield-check"></i>
                    <span>Ada garansi resmi</span>
                </div>
                <div class="template-message-item" data-message="Kondisi barang baru">
                    <i class="bi bi-star"></i>
                    <span>Kondisi barang baru</span>
                </div>
                <div class="template-message-item" data-message="Silakan order langsung">
                    <i class="bi bi-cart-plus"></i>
                    <span>Silakan order langsung</span>
                </div>
            </div>
        </div>

        <form id="chatinput-form">
            @csrf
            <div class="message-inputs">
                <input type="hidden" name="customer_id" value="{{ $user->id }}">

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
        --chatbot-light: rgba(108, 99, 255, 0.1);
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

    .chatbot-message {
        background-color: #f8f9fa;
    }

    .chatbot-name {
        color: #007bff;
        font-weight: 600;
    }

    .chatbot-indicator {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .chatbot-indicator small {
        color: #007bff !important;
        font-weight: 500;
        font-size: 11px;
        background: var(--chatbot-light);
        padding: 2px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .chatbot-indicator i {
        font-size: 10px;
        animation: spin 3s linear infinite;
    }

    .chatbot-message p {
        margin-bottom: 0.5rem;
    }

    .whatsapp-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .whatsapp-message {
        border-left: 3px solid #25D366 !important;
        background: rgba(37, 211, 102, 0.02) !important;
    }

    .whatsapp-indicator small {
        color: #25D366 !important;
        font-weight: 500;
        font-size: 11px;
        background: rgba(37, 211, 102, 0.1);
        padding: 2px 6px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .whatsapp-indicator i {
        font-size: 10px;
    }

    .whatsapp-indicator small,
    .chatbot-indicator small {
        font-size: 10px;
        padding: 1px 4px;
    }

    /* Message body styling for WhatsApp messages */
    .message-body.whatsapp-message {
        border-left: 3px solid #25D366;
        background: rgba(37, 211, 102, 0.02);
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

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
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

    .user-area h6 {
        color: #080808;
        margin: 0px;
    }

    .message-right .user-area .meta h6 {
        text-align: right;
    }
</style>
