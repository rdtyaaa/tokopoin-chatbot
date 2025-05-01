@extends('frontend.layouts.app')
@section('content')
    <main>
        <div class="breadcrumb-banner">
            <div class="breadcrumb-banner-img">
                <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
            </div>
            <div class="page-Breadcrumb">
                <div class="Container">
                    <div class="breadcrumb-container">
                        <h2 class="breadcrumb-title">{{$page->name}}</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{url('/')}}">
                                    {{translate("Home")}}
                                </a></li>
                                <li class="breadcrumb-item"> {{translate($title)}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="pb-80">
            <div class="Container">
                <div class="w-75 mx-auto">
                    <div class="mb-3">
                        <h4 class="fs-20 mb-2">
                           {{$title}}
                        </h4>
                        <p class="text-muted fs-14">
                            @php echo $page->description @endphp
                        </p>
                    </div>

                </div>
            </div>
        </section>
    </main>
@endsection
