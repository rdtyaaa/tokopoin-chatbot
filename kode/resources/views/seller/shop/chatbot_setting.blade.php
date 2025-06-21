@extends('seller.layouts.app')

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ translate('Chatbot Setting') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('seller.dashboard') }}">{{ translate('Home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ translate('Chatbot Settings') }}</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">{{ translate('Chatbot Settings') }}</h5>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('seller.chatbot.setting.update') }}" method="POST">
                        @csrf

                        <!-- Main Chatbot Settings -->
                        <div class="mb-4 rounded border p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ translate('Chatbot Status') }}</h6>
                                    <p class="text-muted mb-0">
                                        {{ translate('Enable or disable the chatbot functionality') }}</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="chatbot_status" name="status"
                                        value="active" {{ $chatbotSetting->status == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_status">
                                        <span class="status-text">
                                            {{ $chatbotSetting->status == 'active' ? translate('Active') : translate('Inactive') }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Chatbot Triggers -->
                        <div class="mb-4 rounded border p-3">
                            <h6 class="fw-bold mb-3">
                                {{ translate('Chatbot Triggers') }}
                            </h6>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="trigger_when_offline"
                                            name="trigger_when_offline" value="1"
                                            {{ $chatbotSetting->trigger_when_offline ? 'checked' : '' }}>
                                        <label class="form-check-label" for="trigger_when_offline">
                                            {{ translate('Trigger when seller is offline') }}
                                        </label>
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Chatbot will respond when you are not online') }}</small>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="trigger_when_no_reply"
                                            name="trigger_when_no_reply" value="1"
                                            {{ $chatbotSetting->trigger_when_no_reply ? 'checked' : '' }}>
                                        <label class="form-check-label" for="trigger_when_no_reply">
                                            {{ translate('Trigger when no reply after delay') }}
                                        </label>
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Chatbot will respond if you don\'t reply within specified time') }}</small>
                                </div>

                                <div class="col-lg-6" id="delay_minutes_container">
                                    <label for="delay_minutes" class="form-label">
                                        {{ translate('Delay in Minutes (for no reply trigger)') }}
                                    </label>
                                    <input type="number" min="1" max="120" name="delay_minutes"
                                        id="delay_minutes" class="form-control"
                                        value="{{ $chatbotSetting->delay_minutes }}" placeholder="e.g., 5">
                                    <small
                                        class="text-muted">{{ translate('Minutes to wait before chatbot responds') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Notifications -->
                        <div class="mb-4 rounded border p-3">
                            <h6 class="fw-bold mb-3">
                                {{ translate('WhatsApp Notifications') }}
                            </h6>

                            <!-- Phone Number Verification -->
                            <div class="alert alert-info mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ri-phone-line me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong>{{ translate('Current Phone Number') }}:</strong>
                                        @if ($sellerShop && $sellerShop->whatsapp_number)
                                            <span class="text-success">{{ $sellerShop->whatsapp_number }}</span>
                                        @else
                                            <span class="text-danger">{{ translate('No phone number set') }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('seller.shop.setting') }}" class="btn btn-sm btn-outline-primary">
                                        {{ translate('Update Phone') }}
                                    </a>
                                </div>
                                @if ($sellerShop && $sellerShop->whatsapp_number)
                                    <small
                                        class="text-muted">{{ translate('Is this your correct WhatsApp number? If not, please update it in shop settings.') }}</small>
                                @else
                                    <small
                                        class="text-danger">{{ translate('Please set your phone number in shop settings to receive WhatsApp notifications.') }}</small>
                                @endif
                            </div>

                            <!-- Notification Settings -->
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notify_new_message"
                                            name="whatsapp_notify_new_message" value="1"
                                            {{ $chatbotSetting->whatsapp_notify_new_message ? 'checked' : '' }}
                                            {{ !$sellerShop || !$sellerShop->whatsapp_number ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notify_new_message">
                                            {{ translate('New message received') }}
                                        </label>
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Get notified when customer sends a message') }}</small>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notify_chatbot_reply"
                                            name="whatsapp_notify_chatbot_reply" value="1"
                                            {{ $chatbotSetting->whatsapp_notify_chatbot_reply ? 'checked' : '' }}
                                            {{ !$sellerShop || !$sellerShop->whatsapp_number ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notify_chatbot_reply">
                                            {{ translate('Chatbot replied') }}
                                        </label>
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Get notified when chatbot responds to customer') }}</small>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notify_no_reply"
                                            name="whatsapp_notify_no_reply" value="1"
                                            {{ $chatbotSetting->whatsapp_notify_no_reply ? 'checked' : '' }}
                                            {{ !$sellerShop || !$sellerShop->whatsapp_number ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notify_no_reply">
                                            {{ translate('Message not replied') }}
                                        </label>
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('Get notified when message is not replied within delay time') }}</small>
                                </div>
                            </div>

                            @if (!$sellerShop || !$sellerShop->whatsapp_number)
                                <div class="alert alert-warning mt-3">
                                    <i class="ri-alert-line me-2"></i>
                                    {{ translate('WhatsApp notifications are disabled because no phone number is configured. Please set your phone number in shop settings.') }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 text-start">
                            <button type="submit" class="btn btn-success waves ripple-light" id="add-btn">
                                {{ translate('Update Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const triggerNoReply = document.getElementById('trigger_when_no_reply');
            const delayMinutesContainer = document.getElementById('delay_minutes_container');
            const delayMinutesField = document.getElementById('delay_minutes');

            function toggleDelayField() {
                if (triggerNoReply.checked) {
                    delayMinutesField.required = true;
                    delayMinutesContainer.style.display = 'block';
                } else {
                    delayMinutesField.required = false;
                    delayMinutesContainer.style.display = 'none';
                }
            }

            // Initial toggle
            toggleDelayField();

            // Toggle on change
            triggerNoReply.addEventListener('change', toggleDelayField);
        });
    </script>
@endsection
