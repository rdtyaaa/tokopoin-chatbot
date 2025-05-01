@extends('frontend.layouts.app')
@push('stylepush')
    <link href="{{ asset('assets/global/css/chat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/frontend/css/bootstrap-icons.min.css') }}" rel="stylesheet" type="text/css" />
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

                                                    <div class="session-single  get-chat {{ !$isSeen ? 'unread-message' : '' }}  "
                                                        id="{{ $seller->id }}">
                                                        <div class="seller-icon">
                                                            <img src="{{ show_image(file_path()['profile']['seller']['path'] . '/' . $seller->image, file_path()['profile']['seller']['size']) }}"
                                                                alt="profile.jpg">
                                                        </div>
                                                        <div class="content">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center flex-wrap">
                                                                <div class="title w-auto">
                                                                    {{ $seller->name . ' ' . $seller->last_name }}
                                                                </div>
                                                                <div class="time">
                                                                    {{ $seller->latestConversation->created_at->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center flex-wrap">
                                                                <p>{{ $seller->latestConversation->message }}</p>
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
    <script>
        var sellerId   = '{{request()->query("seller_id")}}';
        var productId  = '{{request()->query("product_id")}}';

        $(document).ready(function() {
           if(sellerId){
             getMessage(sellerId);
           }

        });


        $(document).on('click', '.get-chat', function(e) {
            $('.get-chat').removeClass('active');
            $(this).addClass('active');
            sellerId = $(this).attr('id');
            getMessage(sellerId)

        });


        function getMessage(sellerId, loader = true, page = null, scroll = true, append = false) {

            var url = "{{ route('user.seller.chat.message', ':seller_id') }}"
                .replace(':seller_id', sellerId);
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

                    if('{{$product_url}}'){
                       var counter  =  $('#ajaxRequestCounter').val();
                       var chatMessage = $('.chat-message-input');
                       if(counter == 0){
                           chatMessage.html('{{$product_url}}');
                       }
                       else{
                           chatMessage.html('')
                       }

                       $('#ajaxRequestCounter').val(counter+1);
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
