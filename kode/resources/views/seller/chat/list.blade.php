@extends('seller.layouts.app')
@push('style-include')
    <link href="{{ asset('assets/global/css/chat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/frontend/css/bootstrap-icons.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('style-push')
    <style>
        .empty-message img,
        .empty-list img {
            max-width: 20% !important;
            height: auto;
            vertical-align: middle;
        }

        .message-inputs .message-submit {
            background-color: #0f0d19;
        }

        .no-products {
            display: flex;
            justify-content: center;
            text-align: center;
            flex-direction: column;
            align-items: center;
            gap: 10px;

            .icon {
                max-width: 100px;
                width: 100%;

                img {
                    width: 100%;
                }
            }
        }

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
    </style>
@endpush

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ $title }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">
                                {{ translate('Dashboard') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ $title }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{ translate('Chat') }}
                                    </h4>
                                </div>
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
                                                @include('seller.chat.sidebar')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 position-relative chat-message-section">
                                        <div id="message" class="chat-message">
                                            <div class="empty-message">
                                                @include('frontend.partials.empty', [
                                                    'message' => translate('Please select a Customer'),
                                                ])
                                            </div>
                                        </div>

                                        <div class="spinner-border chat-spinner-loader d-none" role="status">
                                            <span class="visually-hidden"></span>
                                        </div>
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
@endsection

@push('script-include')
    <script src="{{ asset('assets/global/js/chat.js') }}"></script>
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
@endpush

@push('script-push')
    <script>
        // var customerId;
        // var userId = '{{ request()->query('user_id') }}';

        var userRole = "seller";
        var userId = '{{ request()->query('user_id') }}';
        const sellerId = {{ auth('seller')->user()->id }};
        const customerIds = {!! json_encode($customers->pluck('id')) !!};

        const socket = io("http://localhost:3000", {
            query: {
                role: userRole,
                user_id: sellerId
            }
        });

        // Terima status online user lain (misalnya untuk update badge)
        socket.on("all-users-online", function(userIds) {
            checkOnlineStatus(userIds);
        });

        socket.on("user-online-status", function(data) {
            const [role, id] = data.user_id.split("-");
            const selectors =
                `.online-status[data-role="${role}"][data-id="${id}"], .user-status[data-role="${role}"][data-id="${id}"]`;

            const $targets = $(selectors);
            $targets.each(function() {
                updateStatusElement($(this), data.online, data.last_seen);
            });
        });

        function joinRoom(customerId) {
            const room = `chat-channel.customer.${customerId}`;
            socket.emit("join", room);
        }

        try {
            customerIds.forEach(customerId => {
                joinRoom(customerId);
            });
        } catch (e) {
            console.error("Error emitting join events (seller):", e);
        }

        // // Saat dokumen siap dan ada userId dari query, load pesan & join room
        // $(document).ready(function() {
        //     console.log("[DOC READY] Page loaded");
        //     if (userId) {
        //         console.log(`[DOC READY] userId found in query: ${userId}, joining room`);
        //         getMessage(userId);
        //     } else {
        //         console.log("[DOC READY] No userId in query");
        //     }
        // });

        // Klik customer di sidebar
        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');

            $(this).find('.message-num').remove();

            customerId = $(this).attr('id');
            getMessage(customerId, false, null, true, false);

        });

        socket.on("notify-new-chat", (data) => {
            const id = data.customer_id; // atau sesuaikan: data.id / data.userId / data.chatId
            const existingChat = $(`.get-chat#${id}`);

            if (existingChat.length === 0) {
                joinRoom(id)
                refreshChatSidebar();
            }
        });


        // Terima pesan baru
        socket.on("new-message", function(data) {
            const currentCustomerId = $('.get-chat.active').attr('id');
            const $item = $(`#${data.customer_id}`)
            if (data.customer_id == currentCustomerId) {
                if (data.message.sender_role == "customer") {
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
            $item.find('p').text(message);

            const formattedTime = formatTime(createdAt);
            $item.find('.time').text(formattedTime);
        }


        // if (data.customer_id == currentCustomerId) {
        //     console.log(`[SOCKET] Message is for active chat. Refreshing messages...`);
        //     getMessage(currentCustomerId, false, null, true, false);
        // } else {
        //     console.log(`[SOCKET] Message is for another chat. Refreshing sidebar only.`);
        // }

        // refreshChatSidebar();

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
            if (!date) return "Baru saja";

            const localTime = moment.utc(date).local();
            if (!localTime.isValid()) return "Waktu tidak valid";

            const now = moment();
            const diffMinutes = now.diff(localTime, 'minutes');
            const diffHours = now.diff(localTime, 'hours');
            const diffDays = now.diff(localTime, 'days');
            const diffMonths = now.diff(localTime, 'months');
            const diffYears = now.diff(localTime, 'years');

            if (diffMinutes < 1) return "Baru saja";
            if (diffMinutes < 60) return `Aktif ${diffMinutes} menit lalu`;
            if (diffHours < 24) return `Aktif ${diffHours} jam lalu`;
            if (diffDays < 30) return `Aktif ${diffDays} hari lalu`;
            if (diffMonths < 12) return `Aktif ${diffMonths} bulan lalu`;
            return `Aktif ${diffYears} tahun lalu`;
        }

        // function refreshChatSidebar() {
        //     console.log("Refreshing chat sidebar...");

        //     $.ajax({
        //         url: `/seller/customer/chat/sidebar`,
        //         type: 'GET',
        //         success: function(data) {
        //             console.log("Sidebar updated.", data);
        //             $('.session-list').html(data);

        //             const $onlineStatus = $(".online-status, .user-status");
        //             console.log($onlineStatus);

        //             const userIds = [];
        //             $onlineStatus.each(function() {
        //                 const role = $(this).data("role");
        //                 const id = $(this).data("id");
        //                 userIds.push(`${role}-${id}`);
        //             });

        //             checkOnlineStatus(userIds);
        //         },
        //         error: function(xhr) {
        //             console.error("Failed to refresh sidebar:", xhr.responseText);
        //         }
        //     });
        // }

        function refreshChatSidebar() {
            console.log("Refreshing chat sidebar...");

            $.ajax({
                url: `/seller/customer/chat/sidebar`,
                type: 'GET',
                success: function(data) {
                    console.log("Sidebar data fetchedd.");

                    const $sessionList = $('.session-list');

                    // Hapus elemen `.empty-list` dan class-nya jika ada
                    console.log("empty list: ", $sessionList.find('.empty-list'));
                    $sessionList.find('.empty-list').remove();
                    $sessionList.removeClass('empty-list');

                    const $tempContainer = $('<div>').html(data);
                    const $newSessions = $tempContainer.find('.session-single');

                    $newSessions.each(function() {
                        const $newItem = $(this);
                        const id = $newItem.attr('id');
                        const $existingItem = $(`.session-single#${id}`);

                        if ($existingItem.length) {
                            // ✅ Simpan class penting dari elemen lama
                            const isActive = $existingItem.hasClass('active');
                            const $oldStatusDiv = $existingItem.find('.online-status');
                            const oldStatusClass = $oldStatusDiv.attr('class');

                            // Gantikan seluruh isi lama dengan yang baru
                            $existingItem.html($newItem.html());

                            // ✅ Kembalikan class penting
                            if (isActive) $existingItem.addClass('active');
                            const $newStatusDiv = $existingItem.find('.online-status');
                            $newStatusDiv.attr('class',
                                oldStatusClass); // overwrite class online-status

                        } else {
                            // Append chat baru
                            $('.session-list').append($newItem);
                        }
                    });

                    // Jalankan pengecekan status online
                    const userIds = [];
                    $(".online-status, .user-status").each(function() {
                        const role = $(this).data("role");
                        const id = $(this).data("id");
                        userIds.push(`${role}-${id}`);
                    });

                    checkOnlineStatus(userIds);
                },
                error: function(xhr) {
                    console.error("Failed to refresh sidebar:", xhr.responseText);
                }
            });
        }


        // Sidebar refresh
        // function refreshChatSidebar() {
        //     console.log("[AJAX] Refreshing chat sidebar...");

        //     $.ajax({
        //         url: `/seller/customer/chat/sidebar`,
        //         type: 'GET',
        //         success: function(data) {
        //             console.log("[AJAX] Sidebar updated successfully.");
        //             $('.session-list').html(data);
        //         },
        //         error: function(xhr) {
        //             console.error("[AJAX] Failed to refresh sidebar:", xhr.responseText);
        //         }
        //     });
        // }

        // WebSocket error handling
        socket.on("connect_error", (err) => {
            console.error("[SOCKET ERROR] Connection error:", err.message);
        });
        socket.on("error", (err) => {
            console.error("[SOCKET ERROR] General error:", err);
        });

        // $(document).ready(function() {
        //     if (userId) {
        //         getMessage(userId);
        //     }

        // });

        // $(document).on('click', '.get-chat', function(e) {
        //     $('.get-chat').removeClass('active');
        //     $(this).addClass('active');
        //     customerId = $(this).attr('id');
        //     getMessage(customerId)

        // });

        function getMessage(customerId, loader = true, page = null, scroll = true, append = false) {

            var url = "{{ route('seller.customer.chat.message', ':customer_id') }}"
                .replace(':customer_id', customerId);
            if (page) {
                url = url + '?page=' + page
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

        //send message to customer
        $(document).on('submit', '#chatinput-form', function(e) {

            e.preventDefault()
            var submitButton = $(e.originalEvent.submitter);

            var data = new FormData(this);
            var $btnHtml = '<i class="bi bi-send"></i>';

            $.ajax({
                method: 'POST',
                url: "{{ route('seller.customer.chat.send_message') }}",
                beforeSend: function() {
                    $('.message-submit').html(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
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
                        getMessage(response.customer_id, false)
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
