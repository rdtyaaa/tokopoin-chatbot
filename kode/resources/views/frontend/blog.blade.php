@extends('frontend.layouts.app')
@section('content')

    <div class="breadcrumb-banner">
        <div class="breadcrumb-banner-img">
            <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
        </div> 

        <div class="page-Breadcrumb">
            <div class="Container">
                <div class="breadcrumb-container">
                    <h1 class="breadcrumb-title">{{($title)}}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">
                                {{translate('home')}}
                            </a></li>
                            @if(request()->routeIs('blog.category'))
                                <li class="breadcrumb-item">
                                    <a href="{{route('blog')}}">
                                        {{translate('Blogs')}}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{translate($title)}}
                                </li>
                                @else
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{translate($title)}}
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="blog pb-80">
        <div class="Container">
            <div class="row g-4 g-xl-5">
                @php
                    $flag = 1;
                @endphp
                <div class="col-md-8">
                    <div class="blog-left">
                        @forelse($blogs  as $blog)
                            <div class="blog-item">
                                <div class="blog-left-img">
                                    <img class="w-100" src="{{show_image(file_path()['blog']['path'].'/'.$blog->image,file_path()['blog']['size'])}}" alt="{{$blog->image}}" />
                                </div>
                                <div class="blog-left-contents">
                                    <div class="blog-date-wrapper">
                                        <span class="blog-date"
                                        >{{get_date_time($blog->created_at, 'd')}} <br />
                                        {{get_date_time($blog->created_at, 'M')}}</span
                                        >
                                    </div>
                                    <div class="blog-content">
                                        <h2 class="blog-item-title">
                                            {{($blog->post)}}
                                        </h2>
                                        <p>
                                            {{limit_words(strip_tags(@$blog->body), 100)}}
                                        </p>
                                        <a href="{{route('blog.details', [make_slug($blog->post), $blog->id])}}">
                                        {{translate("CONTINUE READING")}}

                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1"   width="16" height="16" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve" ><g><path d="m506.134 241.843-.018-.019-104.504-104c-7.829-7.791-20.492-7.762-28.285.068-7.792 7.829-7.762 20.492.067 28.284L443.558 236H20c-11.046 0-20 8.954-20 20s8.954 20 20 20h423.557l-70.162 69.824c-7.829 7.792-7.859 20.455-.067 28.284 7.793 7.831 20.457 7.858 28.285.068l104.504-104 .018-.019c7.833-7.818 7.808-20.522-.001-28.314z" data-original="#000000"></path></g></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="no-data-wrapper">
                                @include("frontend.partials.empty",['message' => 'No Data Found'])
                            </div>
                        @endforelse

                        <div class="mt-5 mx-4 d-flex align-items-center justify-content-end">
                            {{$blogs->withQueryString()->links()}}
                        </div>


                    </div>
                </div>

                <div class="col-md-4">
                    <div class="blog-right">
                        <div class="card mb-4 mb-xl-5">
                            <div class="card-body">
                                <div>
                                    <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}"  class="blog-search-bar" method="get">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search" placeholder="{{translate('Search by title')}}" value="{{request()->input('search')}}">
                                            <button type="submit" class="input-group-text fs-20 search-btn wave-btn" id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-5">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">
                                            {{translate("Blog Categories")}}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div>
                                    <div class="blog-categories">
                                        <ul>
                                            @forelse($categories as $category)
                                                <li>
                                                    <a  @if(request()->routeIs('blog.category') && request()->route('id') ==  $category->id ) class="active" @endif href="{{route('blog.category',[make_slug(get_translation($category->name)), $category->id])}}">{{(get_translation($category->name))}}</a>
                                                </li>
                                            @empty
                                                <li>
                                                    @include("frontend.partials.empty",['message' => 'No Categories Found'])
                                               </li>
     
                                            @endforelse
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card ">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title">
                                            {{translate('RECENT POSTS')}}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div>
                                  

                                    <div class="recent-post">
                                        @forelse($recentPosts as $recentPost)
                                            <div class="recent-post-item">
                                                <a href="{{route('blog.details', [make_slug($recentPost->post), $recentPost->id])}}">
                                                    {{$recentPost->post}}
                                                </a>
                                                <p>{{get_date_time($recentPost->created_at, 'M d Y')}}</p>
                                            </div>
                                        @empty
                                        
                                           <div class="recent-post-item">
                                               @include("frontend.partials.empty",['message' => 'No Data Found'])
                                           </div>
    
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> 


@endsection
