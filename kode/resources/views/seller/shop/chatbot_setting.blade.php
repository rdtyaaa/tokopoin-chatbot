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
                    <form action="{{ route('seller.chatbot.setting.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="rounded border p-3">
                            <h6 class="fw-bold mb-3">
                                {{ translate('Chatbot Setting') }} <span class="text-danger">*</span>
                            </h6>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <label for="status" class="form-label">
                                        {{ translate('Enable Chatbot') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1" {{ @$setting->status == 1 ? 'selected' : '' }}>
                                            {{ translate('Enabled') }}
                                        </option>
                                        <option value="0" {{ @$setting->status == 0 ? 'selected' : '' }}>
                                            {{ translate('Disabled') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label for="mode" class="form-label">
                                        {{ translate('Chatbot Mode') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="mode" id="mode" required>
                                        <option value="offline_only"
                                            {{ @$setting->mode == 'offline_only' ? 'selected' : '' }}>
                                            {{ translate('When seller offline') }}
                                        </option>
                                        <option value="unreplied_timeout"
                                            {{ @$setting->mode == 'unreplied_timeout' ? 'selected' : '' }}>
                                            {{ translate('When not replied after delay') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label for="delay_minutes" class="form-label">
                                        {{ translate('Delay in Minutes (if not replied)') }}
                                    </label>
                                    <input type="number" min="0" name="delay_minutes" id="delay_minutes"
                                        class="form-control" value="{{ @$setting->delay_minutes }}" placeholder="e.g., 3">
                                </div>

                                <div class="col-lg-6">
                                    <label for="response_delay" class="form-label">
                                        {{ translate('Typing Simulation Duration (seconds)') }}
                                    </label>
                                    <input type="number" min="0" name="response_delay" id="response_delay"
                                        class="form-control" value="{{ @$setting->response_delay }}" placeholder="e.g., 2">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-start">
                            <button type="submit" class="btn btn-success waves ripple-light" id="add-btn">
                                {{ translate('Submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
Z
