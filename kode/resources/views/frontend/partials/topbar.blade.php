@php
    $header = frontend_section('floating-header');
    $header_value = json_decode( $header->value,true);
@endphp
@if($header->status == '1')
    <div class="header-top">
        <div class="Container">
            <p class="topbar-offer">{{ $header_value['heading']['value']}} ✨
            </p>
        </div>
        <span class="header-top-hide"><i class="fa-solid fa-xmark"></i></span>
    </div>
@endif
