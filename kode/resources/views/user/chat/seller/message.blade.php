<div class="col-lg-12">
    <div class="seller-message-view">

        <div class="seller-store" data-role="seller" data-id={{ $seller->id }}>
            <div class="d-flex align-items-center gap-2">
                <div class="profile-image">
                    <img src="{{ show_image(file_path()['profile']['seller']['path'] . '/' . $seller->image, file_path()['profile']['seller']['size']) }}"
                        alt="profile.jpg">
                </div>
                <h5>{{ $seller->name . ' ' . $seller->last_name }}</h5>
                <div class="user-status" data-role="seller" data-id={{ $seller->id }}>
                    <span class="status-text">Loading...</span>
                </div>
            </div>
        </div>

        <div class="messages">
            @forelse ($messages as $message)

                @php

                    $positionClass = $message->sender_role == 'seller' ? 'message-left' : ' message-right';

                    $imgURL =
                        $message->sender_role == 'seller'
                            ? show_image(
                                file_path()['profile']['seller']['path'] . '/' . $message->seller->image,
                                file_path()['profile']['seller']['size'],
                            )
                            : show_image(
                                file_path()['profile']['user']['path'] . '/' . $message->customer->image,
                                file_path()['profile']['user']['size'],
                            );

                @endphp

                <div class="message-single {{ $positionClass }} d-flex flex-column">
                    <div
                        class="user-area d-inline-flex @if ($message->sender_role != 'seller') justify-content-end @endif align-items-center mb-2 gap-3">

                        <div class="image">

                            <img src="{{ $imgURL }}" alt="profile.jpg">
                        </div>

                        <div class="meta">

                            <h6>
                                @if ($message->sender_role == 'seller')
                                    {{ $message->seller->name . ' ' . $message->seller->last_name }}
                                @else
                                    {{ $message->customer->name }}
                                @endif
                            </h6>
                        </div>
                    </div>
                    <div class="message-body">
                        <p>
                            {{ $message->message }}
                        </p>


                        @if ($message->files)
                            <div class="message-file gap-3">

                                @foreach ($message->files as $file)
                                    <a target="_blank"
                                        href="{{ @show_image(file_path()['chat']['path'] . '/' . $file) }}"
                                        class="m-2"><i class="bi bi-file-pdf"></i> {{ $file }} </a>
                                @endforeach


                            </div>
                        @endif


                        <div class="message-time">
                            <span>
                                {{ $message->created_at->format('H:i') }}
                            </span>
                        </div>

                        @if ($message->sender_role != 'seller')
                            <span class="text-success check-message-icon">
                                <i
                                    class="bi bi-check2{{ $message->is_seen == App\Enums\StatusEnum::true->status() ? '-all' : '' }}"></i>
                            </span>
                        @endif

                    </div>
                </div>

            @empty
                @include('frontend.partials.empty', [
                    'message' => translate('No message found'),
                ])
            @endforelse
        </div>

        <div class="template-messages">
            <button type="button" class="btn btn-outline-secondary btn-sm template-btn rounded-pill">
                Apakah barang masih ada?</button>
            <button type="button" class="btn btn-outline-secondary btn-sm template-btn rounded-pill">
                Bisa nego harga?</button>
            <button type="button" class="btn btn-outline-secondary btn-sm template-btn rounded-pill">
                Dimana lokasi COD-nya?</button>
        </div>

        <form enctype="multipart/form-data" id="chatinput-form">
            @csrf
            <div class="message-inputs">
                <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                <div class="upload-filed image-upload">
                    <input id="media-file" multiple type="file" name="files[]">
                    <label for="media-file mb-0">
                        <span class="d-flex align-items-center flex-row gap-2">
                            <span class="upload-drop-file">
                                <i class="bi bi-image"></i>
                            </span>
                        </span>
                    </label>
                </div>
                <textarea class="chat-message-input" name="message" placeholder="{{ translate('Write Message') }}"></textarea>
                <button type="submit" class="message-submit"><i class="bi bi-send"></i></button>
            </div>
        </form>

        <ul class="file-list"></ul>

    </div>
</div>
