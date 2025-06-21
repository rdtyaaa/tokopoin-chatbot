@extends('frontend.layouts.app')
@push('stylepush')
    <link href="{{ asset('assets/global/css/chat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/frontend/css/bootstrap-icons.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
            background-color: gray;
            /* default */
        }

        .status-online .status-dot {
            background-color: #28a745;
        }

        .status-offline .status-dot {
            background-color: #ccc;
        }

        .template-btn {
            white-space: nowrap;
            margin: 0.5em;
        }
    </style>
@endpush
@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="breadcrumb-banner">
        <div class="breadcrumb-banner-img">
            <img src="{{ show_image(file_path()['frontend']['path'] . '/' . @frontend_section_data($breadcrumb->value, 'image'), @frontend_section_data($breadcrumb->value, 'image', 'size')) }}"
                alt="breadcrumb.jpg">
        </div>
        <div class="page-Breadcrumb">
            <div class="Container">
                <div class="breadcrumb-container">
                    <h1 class="breadcrumb-title">{{ $title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">
                                    {{ translate('home') }}
                                </a></li>

                            <li class="breadcrumb-item active" aria-current="page">
                                {{ translate($title) }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="Container">
            <div class="row g-4">

                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{ translate('Chat List') }}
                                    </h4>
                                </div>

                                <a class="view-more-btn" href="{{ route('user.dashboard') }}">
                                    {{ translate('Dashboard') }}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="chat-area">
                                <div class="row g-lg-0 g-3">
                                    <div class="col-lg-3">
                                        <div class="chat-left-sidebar">
                                            <div class="session-toolbar">
                                                <h5>{{ translate('Chat list') }}</h5>
                                            </div>
                                            <div class="session-list">
                                                @include('user.chat.seller.sidebar')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 position-relative chat-message-section">
                                        <div id="message" class="chat-message">
                                            <div class="empty-message">
                                                @include('frontend.partials.empty', [
                                                    'message' => translate('Please select a seller'),
                                                ])
                                            </div>

                                        </div>

                                        <div class="spinner-border chat-spinner-loader d-none" role="status">
                                            <span class="visually-hidden">

                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <input type="hidden" name="page" id="pageNumber">

        <input type="hidden" name="ajax_request_counter" value="0" id="ajaxRequestCounter">

    </section>
@endsection

@push('script-include')
    <script src="{{ asset('assets/global/js/chat.js') }}"></script>
@endpush

@push('scriptpush')
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script>
        var userRole = "customer";
        var sellerId = '{{ request()->query('seller_id') }}';
        var productId = '{{ request()->query('product_id') }}';
        const customerId = {{ auth()->id() }};
        const sellerIds = {!! json_encode($sellers->pluck('id')) !!};

        let statusCheckTimeout;
        let onlineUsersCache = new Set(); // Cache untuk mencegah request berulang
        let lastSeenCache = new Map(); // Cache untuk last seen data
        let requestInProgress = new Set(); // Track request yang sedang berlangsung

        let heartbeatInterval;
        let heartbeatTimeout;


        const socket = io("http://localhost:3000", {
            query: {
                role: userRole,
                user_id: customerId
            }
        });

        function debouncedStatusCheck(userIds) {
            clearTimeout(statusCheckTimeout);
            statusCheckTimeout = setTimeout(() => {
                checkOnlineStatus(userIds);
            }, 500);
        }

        socket.on("connect", () => {
            console.log("Connected to WebSocket as", userRole + '-' + customerId);

            // Request online users after connection is established
            setTimeout(() => {
                socket.emit("request-online-users");
            }, 500);
            startHeartbeat();
        });

        socket.on('disconnect', (reason) => {
            console.log('Disconnected:', reason);
            // Clear caches on disconnect
            onlineUsersCache.clear();
            lastSeenCache.clear();
            requestInProgress.clear();

            if (reason === 'io server disconnect') {
                socket.connect();
            }
            stopHeartbeat();
        });

        window.addEventListener('beforeunload', () => {
            console.log('Page is being unloaded, disconnecting socket...');
            socket.disconnect();
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Tab hidden/minimized');
            } else {
                console.log('Tab visible again');
                if (!socket.connected) {
                    socket.connect();
                }
            }
        });

        window.addEventListener('pagehide', () => {
            console.log('Page hidden, disconnecting socket...');
            socket.disconnect();
        });

        function startHeartbeat() {
            heartbeatInterval = setInterval(() => {
                socket.emit('ping');

                heartbeatTimeout = setTimeout(() => {
                    console.log('Server heartbeat timeout, reconnecting...');
                    socket.disconnect();
                    socket.connect();
                }, 10000); // 10 second timeout
            }, 30000); // Send ping every 30 seconds
        }

        function stopHeartbeat() {
            if (heartbeatInterval) {
                clearInterval(heartbeatInterval);
                heartbeatInterval = null;
            }
            if (heartbeatTimeout) {
                clearTimeout(heartbeatTimeout);
                heartbeatTimeout = null;
            }
        }

        setInterval(() => {
            socket.emit('ping');
        }, 45000);

        socket.on('pong', () => {
            console.log('Connection alive');
            // Clear timeout since server responded
            if (heartbeatTimeout) {
                clearTimeout(heartbeatTimeout);
                heartbeatTimeout = null;
            }
        });

        socket.on("all-users-online", function(userIds) {
            console.log("[all-users-online] Event received:", userIds);

            // Update cache
            onlineUsersCache = new Set(userIds);

            debouncedStatusCheck(userIds);
        });

        setInterval(() => {
            if (socket.connected) {
                socket.emit("request-online-users");
            }
        }, 60000);

        // Error handler WebSocket
        socket.on("connect_error", (err) => {
            console.error("WebSocket connection error:", err.message);
        });
        socket.on("error", (err) => {
            console.error("WebSocket general error:", err);
        });

        $(document).ready(function() {
            if (sellerId) {
                console.log(`[DOC READY] sellerId found in query: ${sellerId}, joining room`);

                // Set active class pada sidebar sebelum load message
                setTimeout(function() {
                    $('.get-chat').removeClass('active');
                    const $targetSeller = $(`.get-chat#${sellerId}`);
                    if ($targetSeller.length > 0) {
                        $targetSeller.addClass('active');
                        console.log(`Set active class for seller: ${sellerId}`);
                    } else {
                        console.warn(`Seller with ID ${sellerId} not found in sidebar`);
                        setTimeout(function() {
                            const $retryTargetSeller = $(`.get-chat#${sellerId}`);
                            if ($retryTargetSeller.length > 0) {
                                $('.get-chat').removeClass('active');
                                $retryTargetSeller.addClass('active');
                                console.log(`Set active class for seller after retry: ${sellerId}`);
                            }
                        }, 500);
                    }
                }, 100);

                getMessage(sellerId);
            }
        });

        function joinRoom(sellerId) {
            const room = `chat-channel.seller.${sellerId}`;
            console.log(`[SOCKET] Customer joining room: ${room}`);
            socket.emit("join", room);
        }

        try {
            sellerIds.forEach(sellerId => {
                joinRoom(sellerId);
            });
        } catch (e) {
            console.error("Error emitting join events (customer):", e);
        }

        // Klik di daftar seller → aktifkan, ambil pesan, join room
        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');

            $(this).find('.message-num').remove();

            sellerId = $(this).attr('id');
            getMessage(sellerId, false, null, true, false);
        });

        // Fungsi notifikasi untuk pesan baru dari seller
        function showNewSellerMessageNotification(data) {
            const currentSellerId = $('.get-chat.active').attr('id');
            let senderName = data.seller_name || 'Seller';

            // Update nama untuk chatbot
            if (data.message.sender_role === 'chatbot') {
                senderName = `Chatbot (${senderName})`;
            }

            // Hanya tampilkan notifikasi jika bukan untuk chat yang sedang aktif
            if (data.seller_id != currentSellerId) {
                // Toaster notification
                if (typeof toaster === 'function') {
                    const message = data.message.sender_role === 'chatbot' ?
                        `Balasan otomatis dari ${senderName}` :
                        `Pesan baru dari ${senderName}!`;
                    toaster(message, 'info');
                }
            }
        }

        // Terima pesan baru dari WebSocket
        socket.on("new-message", function(data) {
            console.log("New message received:", data);

            const currentSellerId = $('.get-chat.active').attr('id');
            console.log("Current Seller Id:", currentSellerId);
            const $item = $(`#${data.seller_id}`);

            if (data.seller_id == currentSellerId) {
                // PERBAIKAN: Handle semua jenis sender_role (seller & chatbot)
                if (data.message.sender_role == "seller" || data.message.sender_role == "chatbot") {
                    updateMessage(data.message.message, data.message.created_at, data.message.sender_role);
                }
                updateSidebar($item, data.message.message, data.message.created_at);
            } else {
                updateSidebar($item, data.message.message, data.message.created_at);

                if ($item.find('.message-num').length === 0) {
                    $item.find('.d-flex.justify-content-between.align-items-center.flex-wrap')
                        .last()
                        .append(`<span class="message-num">New</span>`);
                }
                $item.addClass('unread-message');

                // Tampilkan notifikasi untuk pesan dari seller atau chatbot yang tidak aktif
                if (data.message.sender_role == "seller" || data.message.sender_role == "chatbot") {
                    showNewSellerMessageNotification(data);
                }
            }
        });

        function updateMessage(messageText, createdAt, senderRole = 'seller') {
            console.log('updateMessage called with:', {
                messageText,
                createdAt,
                senderRole
            });

            // Clone dari message terakhir yang tepat berdasarkan sender_role
            let $templateMsg;

            if (senderRole === 'chatbot') {
                // Cari template chatbot message, atau fallback ke seller message
                $templateMsg = $('.messages .message-left').filter(function() {
                    return $(this).find('.chatbot-name').length > 0;
                }).last();

                // Jika tidak ada template chatbot, buat structure baru
                if ($templateMsg.length === 0) {
                    $templateMsg = $('.messages .message-left').last();
                }
            } else {
                // Untuk seller, cari message seller (bukan chatbot)
                $templateMsg = $('.messages .message-left').filter(function() {
                    return $(this).find('.chatbot-name').length === 0;
                }).last();
            }

            // Fallback jika tidak ada template
            if ($templateMsg.length === 0) {
                $templateMsg = $('.messages .message-left').last();
            }

            const $newMsg = $templateMsg.clone();

            // Update isi pesan dan waktu
            $newMsg.find('.message-body p').text(messageText);
            $newMsg.find('.message-time span').text(formatTime(createdAt));

            // PERBAIKAN: Update sender info berdasarkan role
            if (senderRole === 'chatbot') {
                // Pastikan struktur chatbot benar
                const $userArea = $newMsg.find('.user-area');
                const $meta = $userArea.find('.meta h6');

                if ($meta.length > 0) {
                    // Update nama menjadi chatbot
                    $meta.html('<span class="chatbot-name"><i class="bi bi-robot"></i> Chatbot - Assistant</span>');
                }

                // Tambahkan chatbot indicator jika belum ada
                const $messageBody = $newMsg.find('.message-body');
                if ($messageBody.find('.chatbot-indicator').length === 0) {
                    $messageBody.prepend(`
                <div class="chatbot-indicator mb-1">
                    <small class="text-muted">
                        <i class="bi bi-cpu"></i> Automated Response
                    </small>
                </div>
            `);
                }

                // Tambahkan class chatbot-message
                $messageBody.addClass('chatbot-message');

                // Update avatar menjadi chatbot avatar
                const $avatar = $newMsg.find('.user-area .image img');
                if ($avatar.length > 0) {
                    $avatar.attr('src', '/assets/images/chatbot-avatar.jpg');
                }
            } else {
                // Untuk seller, pastikan tidak ada elemen chatbot
                $newMsg.find('.chatbot-indicator').remove();
                $newMsg.find('.message-body').removeClass('chatbot-message');

                // Pastikan nama seller benar (tidak berubah jadi chatbot)
                const $meta = $newMsg.find('.user-area .meta h6');
                if ($meta.find('.chatbot-name').length > 0) {
                    // Reset ke seller name - ambil dari data original
                    const sellerName = $('.seller-store h5').text().trim();
                    $meta.text(sellerName);
                }
            }

            // Remove check icon (karena ini dari seller/chatbot, bukan customer)
            $newMsg.find('.check-message-icon').remove();

            // Tambahkan ke elemen chat
            $('.messages').append($newMsg);
            scroll_bottom();
        };

        function updateSidebar($item, message, createdAt) {
            // Update preview pesan
            $item.find('p').text(message);

            // Update waktu
            const formattedTime = formatTime(createdAt);
            $item.find('.time').text(formattedTime);
        }

        function refreshChatSidebar() {
            console.log("Refreshing chat sidebar...");

            const activeId = $('.get-chat.active').attr('id'); // Simpan ID yg aktif

            $.ajax({
                url: `/user/seller/chat/sidebar`,
                type: 'GET',
                success: function(data) {
                    console.log("Sidebar updated.");
                    $('.session-list').html(data);

                    // Restore active class setelah sidebar di-refresh
                    if (activeId) {
                        const restoredElement = $(`.get-chat#${activeId}`);
                        if (restoredElement.length) {
                            restoredElement.addClass('active');
                            console.log(`Restored active chat to ID: ${activeId}`);
                        } else {
                            console.warn(`Active ID ${activeId} not found after refresh.`);
                        }
                    }
                },
                error: function(xhr) {
                    console.error("Failed to refresh sidebar:", xhr.responseText);
                }
            });
        }

        socket.on("user-online-status", function(data) {
            console.log("[user-online-status] Event received:", data);

            // Parse the user_id to get role and id
            const parts = data.user_id.split("-");
            if (parts.length < 2) return; // Invalid format

            const role = parts[0];
            const id = parts.slice(1).join("-"); // Handle cases where ID might contain dashes

            const globalUserId = `${role}-${id}`;
            if (data.online) {
                onlineUsersCache.add(globalUserId);
                lastSeenCache.delete(globalUserId); // Clear last seen for online users
            } else {
                onlineUsersCache.delete(globalUserId);
                if (data.last_seen) {
                    lastSeenCache.set(globalUserId, data.last_seen);
                }
            }

            const selectors =
                `.online-status[data-role="${role}"][data-id="${id}"], .user-status[data-role="${role}"][data-id="${id}"]`;
            const $targets = $(selectors);

            $targets.each(function() {
                updateStatusElement($(this), data.online, data.last_seen);
            });
        });

        function checkOnlineStatus(userIds) {
            const onlineSet = new Set(userIds);

            $(".online-status, .user-status").each(function() {
                const role = $(this).data("role");
                const id = $(this).data("id");
                const globalUserId = `${role}-${id}`;

                if (onlineSet.has(globalUserId)) {
                    updateStatusElement($(this), true);
                } else {
                    if (lastSeenCache.has(globalUserId)) {
                        updateStatusElement($(this), false, lastSeenCache.get(globalUserId));
                        return;
                    }

                    const requestKey = `${role}-${id}`;
                    if (requestInProgress.has(requestKey)) {
                        console.log(`Request already in progress for ${requestKey}`);
                        return;
                    }

                    requestInProgress.add(requestKey);

                    // Only make API call if not in online set and not cached
                    $.get(`/api/user-last-seen?role=${role}&id=${id}`)
                        .done((res) => {
                            // Cache the result
                            lastSeenCache.set(globalUserId, res.last_seen);
                            updateStatusElement($(this), false, res.last_seen);
                        })
                        .fail(() => {
                            updateStatusElement($(this), false, null);
                        })
                        .always(() => {
                            // Remove from in-progress tracking
                            requestInProgress.delete(requestKey);
                        });
                }
            });
        }

        function updateStatusElement($el, isOnline, lastSeen = null) {
            const $text = $el.find(".status-text");
            const $dot = $el.find(".status-dot");

            if (isOnline) {
                $text.text("Online");
                $el.removeClass("status-offline").addClass("status-online");
            } else {
                $text.text(timeAgo(lastSeen));
                $el.removeClass("status-online").addClass("status-offline");
            }
        }

        function formatTime(date) {
            if (!date) return "";
            const localTime = moment.utc(date).local();
            if (!localTime.isValid()) return "Waktu tidak valid";
            return localTime.format('HH:mm'); // Format 24 jam
        }

        function timeAgo(date) {
            if (!date) return "Baru saja aktif";

            const localTime = moment.utc(date).local();
            if (!localTime.isValid()) return "Waktu tidak valid";

            const now = moment();
            const diffMinutes = now.diff(localTime, 'minutes');
            const diffHours = now.diff(localTime, 'hours');
            const diffDays = now.diff(localTime, 'days');
            const diffMonths = now.diff(localTime, 'months');
            const diffYears = now.diff(localTime, 'years');

            if (diffMinutes < 1) return "Baru saja aktif";
            if (diffMinutes < 60) return `Aktif ${diffMinutes} menit lalu`;
            if (diffHours < 24) return `Aktif ${diffHours} jam lalu`;
            if (diffDays < 30) return `Aktif ${diffDays} hari lalu`;
            if (diffMonths < 12) return `Aktif ${diffMonths} bulan lalu`;
            return `Aktif ${diffYears} tahun lalu`;
        }

        function getMessage(sellerId, loader = true, page = null, scroll = true, append = false) {
            var url = "{{ route('user.seller.chat.message', ':seller_id') }}".replace(':seller_id', sellerId);
            if (page) {
                url = url + '?page=' + page
            }

            // Add product_id to URL if exists - PERBAIKAN
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('product_id');
            if (productId) {
                url += (url.includes('?') ? '&' : '?') + 'product_id=' + productId;
            }

            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    if (loader) {
                        $('.chat-spinner-loader').removeClass('d-none')
                        $('.empty-message').addClass('d-none')
                    }
                },
                success: function(response) {
                    $('.chat-message').html(response.chat);

                    setTimeout(function() {
                        initializeChatComponents();
                    }, 100);

                    // PERBAIKAN: Request online users setelah chat dimuat
                    if (socket.connected) {
                        socket.emit("request-online-users");
                    }

                    if (scroll) {
                        scroll_bottom()
                    }
                },
                error: function(error) {
                    if (error && error.responseJSON) {
                        if (error.responseJSON.errors) {
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0], 'danger')
                            }
                        } else {
                            if ((error.responseJSON.message)) {
                                toaster(error.responseJSON.message, 'danger')
                            } else {
                                toaster(error.responseJSON.error, 'danger')
                            }
                        }
                    } else {
                        toaster(error.message, 'danger')
                    }
                },
                complete: function() {
                    $('.chat-spinner-loader').addClass('d-none')
                },
            });
        }

        // scroll bottom to chat list when new message appear
        function scroll_bottom() {
            $('.chat-message').animate({
                scrollTop: $('.chat-message')[0].scrollHeight
            }, 1);
        }

        //send message to seller - ENHANCED VERSION
        $(document).on('submit', '#chatinput-form', function(e) {
            e.preventDefault()
            var submitButton = $(e.originalEvent.submitter);
            var data = new FormData(this);
            var $btnHtml = '<i class="bi bi-send-fill"></i>';
            var messageInput = $(this).find('.chat-message-input');
            var message = messageInput.val().trim();

            // Validation
            if (!message) {
                toaster('{{ translate('Pesan tidak boleh kosong') }}', 'warning');
                messageInput.focus();
                return;
            }

            $.ajax({
                method: 'POST',
                url: "{{ route('user.seller.chat.send_message') }}",
                beforeSend: function() {
                    $('.message-submit').html(`<div class="spinner-border spinner-border-sm note-btn-spinner ms-1 text-white" role="status">
                <span class="visually-hidden"></span>
            </div>`);
                    // Disable input during sending
                    messageInput.prop('disabled', true);
                },
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.message-submit').html($btnHtml);
                    if (response.status) {
                        // Clear form and reset UI
                        messageInput.val('').css('height', 'auto');
                        $('#media-file').val('');
                        $('.file-list').empty();

                        // Remove product attachment preview if exists
                        $('.product-attachment-preview').fadeOut(300, function() {
                            $(this).remove();
                        });
                        $('input[name="product_id"]').remove();

                        // Refresh messages
                        getMessage(response.seller_id, false);

                        // Success feedback
                        $('.message-submit').addClass('text-success');
                        setTimeout(() => {
                            $('.message-submit').removeClass('text-success');
                        }, 1000);

                    } else {
                        toaster(response.message, 'danger')
                    }
                },
                error: function(error) {
                    if (error && error.responseJSON) {
                        if (error.responseJSON.errors) {
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0], 'danger')
                            }
                        } else {
                            if ((error.responseJSON.message)) {
                                toaster(error.responseJSON.message, 'danger')
                            } else {
                                toaster(error.responseJSON.error, 'danger')
                            }
                        }
                    } else {
                        toaster(error.message, 'danger')
                    }
                },
                complete: function() {
                    $('.message-submit').html($btnHtml);
                    messageInput.prop('disabled', false).focus();
                },
            })
        });

        // Template button click handler
        $(document).on('click', '.template-btn', function() {
            const message = $(this).data('message');
            const textarea = $('.chat-message-input');
            textarea.val(message).focus();

            // Add visual feedback
            $(this).addClass('template-btn-clicked');
            setTimeout(() => {
                $(this).removeClass('template-btn-clicked');
            }, 200);
        });

        $(document).on('input', '.chat-message-input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Enter key handler (Ctrl+Enter to send)
        $(document).on('keydown', '.chat-message-input', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                $('#chatinput-form').submit();
            }
        });

        $('<style>').text(`
            .template-btn-clicked {
                transform: scale(0.95);
                opacity: 0.8;
            }

            .message-submit.text-success {
                color: #28a745 !important;
            }
        `).appendTo('head');

        // MESSAGE SCRIPT
        // Fungsi utama untuk inisialisasi semua komponen chat
        function initializeChatComponents() {
            const templateBtn = document.querySelector('.template-message-btn');
            const templateContainer = document.getElementById('templateMessagesContainer');
            const templateItems = document.querySelectorAll('.template-message-item');
            const textarea = document.querySelector('.chat-message-input');

            // Toggle template container
            if (templateBtn && templateContainer) {
                templateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    templateContainer.classList.toggle('show');
                    templateBtn.classList.toggle('active');

                    // Scroll to bottom after template shows
                    setTimeout(() => {
                        scroll_bottom();
                    }, 100);
                });
            }

            // Handle template item clicks
            templateItems.forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const message = this.getAttribute('data-message');

                    if (textarea) {
                        textarea.value = message;
                        textarea.focus();

                        // Auto resize textarea
                        textarea.style.height = 'auto';
                        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                    }

                    // Hide template container
                    templateContainer.classList.remove('show');
                    templateBtn.classList.remove('active');

                    // Visual feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);

                    // Scroll to bottom after template closes
                    setTimeout(() => {
                        scroll_bottom();
                    }, 200);
                });
            });

            // Close template when clicking outside
            document.addEventListener('click', function(e) {
                if (!templateBtn.contains(e.target) && !templateContainer.contains(e.target)) {
                    templateContainer.classList.remove('show');
                    templateBtn.classList.remove('active');

                    // Scroll to bottom when template closes
                    setTimeout(() => {
                        scroll_bottom();
                    }, 100);
                }
            });

            // Auto resize textarea
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';

                    // Scroll to bottom when textarea resizes
                    setTimeout(() => {
                        scroll_bottom();
                    }, 50);
                });
            }

            // Product attachment remove functionality
            const removeBtn = document.querySelector('.remove-product-attachment');
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const attachmentPreview = document.querySelector('.product-attachment-preview');
                    if (attachmentPreview) {
                        attachmentPreview.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => {
                            attachmentPreview.remove();

                            // Remove product_id from form
                            const productIdInput = document.querySelector('input[name="product_id"]');
                            if (productIdInput) {
                                productIdInput.remove();
                            }

                            // Update URL untuk menghapus product_id parameter
                            const url = new URL(window.location);
                            url.searchParams.delete('product_id');
                            window.history.replaceState({}, '', url);

                            // Scroll to bottom after removing attachment
                            setTimeout(() => {
                                scroll_bottom();
                            }, 100);
                        }, 300);
                    }
                });
            }
        }
    </script>
@endpush
