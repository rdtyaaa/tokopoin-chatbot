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
        var userRole = "seller";
        var userId = '{{ request()->query('user_id') }}';
        const sellerId = {{ auth('seller')->user()->id }};
        const customerIds = {!! json_encode($customers->pluck('id')) !!};
        let existingCustomerChats = new Set();

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

        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');

            // Remove notification badge
            $(this).find('.message-num').remove();
            $(this).removeClass('unread-message');

            const customerId = $(this).attr('id');

            // Ensure customer is tracked
            if (!existingCustomerChats.has(customerId)) {
                existingCustomerChats.add(customerId);
                joinRoom(customerId);
            }

            getMessage(customerId, false, null, true, false);
        });

        socket.on("notify-new-chat", (data) => {
            const customerId = data.customer_id;
            console.log(`[NOTIFY] New chat notification for customer: ${customerId}`);

            if (!existingCustomerChats.has(customerId)) {
                console.log(`[NOTIFY] New customer detected: ${customerId}`);

                joinRoom(customerId);
                existingCustomerChats.add(customerId);
                refreshChatSidebar();

                showNewCustomerNotification(data);
            } else {
                console.log(`[NOTIFY] Existing customer: ${customerId} - ignoring notify event`);
            }
        });


        socket.on("new-message", function(data) {
            const currentCustomerId = $('.get-chat.active').attr('id');
            const customerId = data.customer_id;
            const $item = $(`#${customerId}`);

            console.log(`[MESSAGE] New message from customer: ${customerId}`);

            if (!existingCustomerChats.has(customerId)) {
                existingCustomerChats.add(customerId);
            }

            if (customerId == currentCustomerId) {
                console.log('[MESSAGE] Message for active chat');

                if (data.message.sender_role == "customer") {
                    updateMessage(data.message.message, data.message.created_at);
                }
                updateSidebar($item, data.message.message, data.message.created_at);
            } else {
                console.log('[MESSAGE] Message for inactive chat');

                updateSidebar($item, data.message.message, data.message.created_at);

                if ($item.find('.message-num').length === 0) {
                    $item.find('.d-flex.justify-content-between.align-items-center.flex-wrap')
                        .last()
                        .append(`<span class="message-num">New</span>`);
                }
                $item.addClass('unread-message');

                showNewMessageNotification(data);
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

        // Helper function untuk check online status
        function checkOnlineStatusForAllUsers() {
            const userIds = [];
            $(".online-status, .user-status").each(function() {
                const role = $(this).data("role");
                const id = $(this).data("id");
                userIds.push(`${role}-${id}`);
            });

            if (userIds.length > 0) {
                checkOnlineStatus(userIds);
            }
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

        function refreshChatSidebar() {
            console.log("Refreshing chat sidebar...");

            $.ajax({
                url: `/seller/customer/chat/sidebar`,
                type: 'GET',
                success: function(data) {
                    console.log("Sidebar data fetched.");

                    const $sessionList = $('.session-list');

                    const previousCustomers = new Set(existingCustomerChats);

                    $sessionList.find('.empty-list').remove();
                    $sessionList.removeClass('empty-list');

                    const $tempContainer = $('<div>').html(data);
                    const $newSessions = $tempContainer.find('.session-single');

                    existingCustomerChats.clear();

                    $newSessions.each(function() {
                        const $newItem = $(this);
                        const id = $newItem.attr('id');
                        const $existingItem = $(`.session-single#${id}`);

                        existingCustomerChats.add(id);

                        if ($existingItem.length) {
                            const isActive = $existingItem.hasClass('active');
                            const $oldStatusDiv = $existingItem.find('.online-status');
                            const oldStatusClass = $oldStatusDiv.attr('class');

                            $existingItem.html($newItem.html());

                            if (isActive) $existingItem.addClass('active');
                            const $newStatusDiv = $existingItem.find('.online-status');
                            if (oldStatusClass) {
                                $newStatusDiv.attr('class', oldStatusClass);
                            }
                        } else {
                            $('.session-list').append($newItem);
                            joinRoom(id);
                        }
                    });

                    const newCustomers = Array.from(existingCustomerChats).filter(id => !previousCustomers.has(
                        id));
                    if (newCustomers.length > 0) {
                        console.log('New customers added:', newCustomers);
                    }

                    checkOnlineStatusForAllUsers();
                },
                error: function(xhr) {
                    console.error("Failed to refresh sidebar:", xhr.responseText);
                }
            });
        }

        // Notification functions

        function showNewCustomerNotification(data) {
            // Tampilkan toast atau notification untuk customer baru
            if (typeof toaster === 'function') {
                toaster(`Pesan baru dari customer baru!`, 'info');
            }

            // // Bisa juga trigger desktop notification jika diizinkan
            // if (Notification.permission === "granted") {
            //     new Notification("Customer Baru", {
            //         body: `Ada customer baru yang mengirim pesan`,
            //         icon: "/path/to/icon.png"
            //     });
            // }
        }

        function showNewMessageNotification(data) {
            // const currentCustomerId = $('.get-chat.active').attr('id');

            // // Hanya tampilkan notifikasi jika bukan untuk chat yang sedang aktif
            // if (data.customer_id != currentCustomerId) {
            //     // Desktop notification
            //     if (Notification.permission === "granted") {
            //         new Notification("Pesan Baru", {
            //             body: data.message.message.substring(0, 50) + (data.message.message.length > 50 ? '...' :
            //                 ''),
            //             icon: "/path/to/icon.png"
            //         });
            //     }
            // }
        }

        // WebSocket error handling
        socket.on("connect_error", (err) => {
            console.error("[SOCKET ERROR] Connection error:", err.message);
        });
        socket.on("error", (err) => {
            console.error("[SOCKET ERROR] General error:", err);
        });

        // Initialize existing customer IDs saat page load
        $(document).ready(function() {
            // Populate existing customers from sidebar
            $('.get-chat').each(function() {
                const customerId = $(this).attr('id');
                if (customerId) {
                    existingCustomerChats.add(customerId);
                }
            });

            console.log('Existing customers:', Array.from(existingCustomerChats));
        });

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

                    setTimeout(function() {
                        initializeChatComponents();
                    }, 100);

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

        // Request notification permission saat page load
        $(document).ready(function() {
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission();
            }
        });

        // Debug function untuk monitoring
        function debugChatState() {
            console.log('=== CHAT DEBUG INFO ===');
            console.log('Existing customers:', Array.from(existingCustomerChats));
            console.log('Active customer:', $('.get-chat.active').attr('id'));
            console.log('Chat items count:', $('.get-chat').length);
            console.log('========================');
        }

        // Expose debug function globally
        window.debugChatState = debugChatState;

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
