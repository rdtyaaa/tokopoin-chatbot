
@extends('frontend.layouts.error')

@push('stylepush')

<style>
    .page_404 {
        width: 100%;
        height:100vh;
        background: #fff;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .page_404 img {
      width: 100%;
    }

    .four_zero_four_bg {
        background-image: url({{asset('assets/images/error.gif')}});
        height: 400px;
        background-position: center;
    }

    .four_zero_four_bg > h1 {
        font-size: 80px;
    }

    .contant_box_404 > h3 {
        font-size: 6rem;
        margin-bottom: 2rem;
    }
    .link_404 {
        color: var(--text-primary) !important;
        background: var(--primary);
        padding: 1rem 2rem;
        margin: 2rem 0;
        display: inline-block;
        border-radius: var(--radius-8);
    }
    .contant_box_404 {
        margin-top:2rem;
    }
</style>

@endpush

@section('content')
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <div class="four_zero_four_bg" >
                            <h1 class="text-center">404</h1>
                        </div>

                        <div class="contant_box_404">
                            <h3>
                                {{translate("Look like you're lost")}}
                            </h3>

                            <p class="fs-18 text-muted" >
                                {{translate("We can't seem to find the page that you're looking for")}}
                            .</p>
                            <a href="{{route('home')}}" class="link_404">
                                {{translate("Go to Home")}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
