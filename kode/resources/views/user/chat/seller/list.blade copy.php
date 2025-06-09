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

        const socket = io("http://localhost:3000", {
            query: {
                role: userRole,
                user_id: customerId
            }
        });

        socket.on("connect", () => {
            console.log("Connected to WebSocket as", userRole + '-' + customerId);

            // Terima status online user lain (misalnya untuk update badge)
            socket.on("all-users-online", function(userIds) {
                console.log("[all-users-online] Event received:", userIds);
                checkOnlineStatus(userIds);
            });
        });

        // Error handler WebSocket
        socket.on("connect_error", (err) => {
            console.error("WebSocket connection error:", err.message);
        });
        socket.on("error", (err) => {
            console.error("WebSocket general error:", err);
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

        // Saat dokumen siap dan ada sellerId dari query, load pesan & join room
        $(document).ready(function() {
            if (sellerId) {
                console.log(`[DOC READY] sellerId found in query: ${sellerId}, joining room`);
                getMessage(sellerId);
            }
        });

        $(document).on('click', '.template-btn', function(e) {
            const message = $(this).text().trim().replace(/\s+/g, ' ');
            $('.chat-message-input').val(message).focus();
        });


        // Klik di daftar seller → aktifkan, ambil pesan, join room
        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');

            $(this).find('.message-num').remove();

            sellerId = $(this).attr('id');
            getMessage(sellerId, false, null, true, false);

        });

        // Terima pesan baru dari WebSocket
        socket.on("new-message", function(data) {
            console.log("New message received:", data);

            const currentSellerId = $('.get-chat.active').attr('id');
            console.log("Current Seller Id:", currentSellerId);
            const $item = $(`#${data.seller_id}`);

            // Cek jika customer belum join room seller tersebut, dan lakukan join
            // if (!Object.values(socket.rooms).includes(`chat-channel.seller.${data.seller_id}`)) {
            // joinRoom(data.seller_id);
            // }

            if (data.seller_id == currentSellerId) {
                if (data.message.sender_role == "seller") {
                    updateMessage(data.message.message, data.message.created_at);
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
            }
        });

        function updateMessage(messageText, createdAt) {
            const $lastMsg = $('.messages .message-left').last();
            const $newMsg = $lastMsg.clone();

            // Update isi pesan dan waktu
            $newMsg.find('p').text(messageText);
            $newMsg.find('.message-time span').text(formatTime(createdAt));

            // Tambahkan ke elemen chat
            $('.messages').append($newMsg);
            scroll_bottom()
        }

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
            const [role, id] = data.user_id.split("-");
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
                    $.get(`/api/user-last-seen?role=${role}&id=${id}`, (res) => {
                        updateStatusElement($(this), false, res.last_seen);
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

        function
        getMessage(sellerId, loader = true, page = null, scroll = true, append = false) {
            var
                url = "{{ route('user.seller.chat.message', ':seller_id') }}".replace(':seller_id', sellerId);
            if (page) {
                url = url +
                    '?page=' + page
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
                    if ('{{ $product_url }}') {
                        var counter = $('#ajaxRequestCounter').val();
                        var chatMessage = $('.chat-message-input');
                        if (counter == 0) {
                            chatMessage.html('{{ $product_url }}');
                        } else {
                            chatMessage.html('')
                        }
                        $('#ajaxRequestCounter').val(counter + 1);
                    }
                    const $onlineStatus = $(".online-status, .user-status");
                    if ($onlineStatus.length > 0) {
                        const userIds = [];
                        $onlineStatus.each(function() {
                            const role = $(this).data("role");
                            const id = $(this).data("id");
                            userIds.push(`${role}-${id}`);
                        });

                        checkOnlineStatus(userIds);
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

        //send message to seller
        $(document).on('submit', '#chatinput-form', function(e) {

            e.preventDefault()
            var submitButton = $(e.originalEvent.submitter);

            var data = new FormData(this);
            var $btnHtml = '<i class="bi bi-send"></i>';

            $.ajax({
                method: 'POST',
                url: "{{ route('user.seller.chat.send_message') }}",
                beforeSend: function() {
                    $('.message-submit').html(`<div class="spinner-border spinner-border-sm note-btn-spinner ms-1 text-white"
            role="status">
            <span class="visually-hidden"></span>
        </div>`);
                },
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.message-submit').html($btnHtml);
                    if (response.status) {
                        getMessage(response.seller_id, false)
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
                },
            })

        });
    </script>
@endpush
