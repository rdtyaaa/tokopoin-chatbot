@extends('seller.layouts.app')
@push('style-include')
    <link href="{{ asset('assets/global/css/chat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/frontend/css/bootstrap-icons.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('style-push')
    <style>
        .empty-message img , .empty-list img {
            max-width: 20% !important;
            height: auto;
            vertical-align: middle;
        }

        .message-inputs .message-submit {
            background-color: #0f0d19;
        }

        .no-products{
            display: flex;
            justify-content: center;
            text-align: center;
            flex-direction: column;
            align-items: center;
            gap:10px;
            .icon{
            
            max-width: 100px;
            width: 100%;
            img{
                width:100%;
            }
        }
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

                                                    <div class="session-single  get-chat {{ !$isSeen ? 'unread-message' : '' }}  "
                                                        id="{{ $customer->id }}">
                                                        <div class="seller-icon">
                                                            <img src="{{ show_image(file_path()['profile']['user']['path'] . '/' . $customer->image, file_path()['profile']['user']['size']) }}"
                                                                alt="profile.jpg">
                                                        </div>
                                                        <div class="content">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center flex-wrap">
                                                                <div class="title w-auto">
                                                                    {{ $customer->name . ' ' . $customer->last_name }}
                                                                </div>
                                                                <div class="time">
                                                                    {{ $customer->latestSellerMessage->created_at->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center flex-wrap">
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
    </div>

    <input type="hidden" name="page" id="pageNumber">
@endsection

@push('script-include')
    <script src="{{ asset('assets/global/js/chat.js') }}"></script>
@endpush

@push('script-push')
    <script>
        var customerId;

        var userId   = '{{request()->query("user_id")}}';

        $(document).ready(function() {
           if(userId){
             getMessage(userId);
           }

        });



        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');
            customerId = $(this).attr('id');
            getMessage(customerId)

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
