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
@endpush

@push('script-push')
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
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

        socket.on("connect", () => {
            console.log("Connected to WebSocket as", userRole + '-' + sellerId);
        });

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


        console.log("[INIT] userId (seller):", sellerId);
        console.log("[INIT] customerIds:", customerIds);

        // Function untuk join WebSocket room
        function joinRoom(customerId) {
            const room = `chat-channel.${sellerId}.${customerId}`;
            console.log(`[SOCKET] Joining room: ${room}`);
            socket.emit("join", room);
        }

        try {
            customerIds.forEach(cId => {
                joinRoom(cId);
            });
        } catch (e) {
            console.error("Error emitting join events:", e);
        }

        // Saat dokumen siap dan ada userId dari query, load pesan & join room
        $(document).ready(function() {
            console.log("[DOC READY] Page loaded");
            if (userId) {
                console.log(`[DOC READY] userId found in query: ${userId}, joining room`);
                getMessage(userId);
                // joinRoom(userId);
            } else {
                console.log("[DOC READY] No userId in query");
            }

            // Terima status online user lain (misalnya untuk update badge)
            socket.on("all-users-online", function(userIds) {
                console.log("[all-users-online] Event received:", userIds);
                checkOnlineStatus(userIds);
            });
        });

        // Klik customer di sidebar
        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');

            $(this).find('.message-num').remove();

            customerId = $(this).attr('id');
            console.log(`[SIDEBAR CLICK] Clicked customerId: ${customerId}`);
            getMessage(customerId, false, null, true, false);

        });

        // Terima pesan baru
        socket.on("new-message", function(data) {
            console.log("[SOCKET] New message received:", data);

            const currentCustomerId = $('.get-chat.active').attr('id');
            console.log(`[SOCKET] Active customerId: ${currentCustomerId}`);


            if (data.customer_id == currentCustomerId) {
                getMessage(currentCustomerId, false, null, true, false);


                const $item = $(`#${data.customer_id}`);

                // Update pesan preview
                $item.find('p').text(data.message.message);
                console.log("data message: ", data.message.message);

                // Update waktu
                $item.find('.time').text(moment(data.message.created_at).fromNow());

                // Tambah badge 'New' jika belum dibaca (bisa berdasarkan data.is_seen = false)
                // if (!data.is_seen) {
                //     if ($item.find('.message-num').length === 0) {
                //         $item.find('.d-flex.justify-content-between.align-items-center.flex-wrap')
                //             .last()
                //             .append(`<span class="message-num">New</span>`);
                //     }
                // } else {
                //     $item.find('.message-num').remove();
                // }
            } else {
                const $item = $(`#${data.customer_id}`);
                console.log("rubah customer ini: ", $item);


                // Tambahkan badge dan update preview
                $item.find('p').text(data.message.message);
                $item.find('.time').text(moment(data.message.created_at).fromNow());

                if ($item.find('.message-num').length === 0) {
                    $item.find('.d-flex.justify-content-between.align-items-center.flex-wrap')
                        .last()
                        .append(`<span class="message-num">New</span>`);
                }

                // Tambahkan highlight class
                $item.addClass('unread-message');
            }


            // if (data.customer_id == currentCustomerId) {
            //     console.log(`[SOCKET] Message is for active chat. Refreshing messages...`);
            //     getMessage(currentCustomerId, false, null, true, false);
            // } else {
            //     console.log(`[SOCKET] Message is for another chat. Refreshing sidebar only.`);
            // }

            // refreshChatSidebar();
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

        function timeAgo(date) {
            console.log("Raw date:", date, "Parsed:", moment.utc(date).local().format());
            if (!date) return "Baru saja aktif";

            const localTime = moment.utc(date).local();
            if (!localTime.isValid()) return "Waktu tidak valid";

            const diffMin = moment().diff(localTime, 'minutes');

            if (diffMin < 1) return "Baru saja aktif";
            if (diffMin === 1) return "Aktif 1 menit lalu";
            return `Aktif ${diffMin} menit lalu`;
        }

        // // Sidebar refresh
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
