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
                        <li class="breadcrumb-item">
                            <a href="{{url('/')}}">
                                {{translate('home')}}
                            </a>
                        </li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="pb-80">
    <div class="Container">
        <div class="card">
            <div class="card-header">
                 <div class="d-flex align-items-center">
                       <div class="flex-grow-1">
                           <h5 class="card-title">
                            {{translate('Compare Product List')}}
                           </h5>
                       </div>

                 </div>
            </div>
            <div class="card-body">
                <div class="compare-container">
                    <table class="compare-table">
                        <tr class="compare-table-top">
                            <th>
                                <div class="add-option">
                                    <a href="{{route('product')}}"><i class="fa-solid fa-plus"></i></a>
                                    <p class="w-75">
                                        {{translate("Add Another Product")}}
                                    </p>
                                </div>
                            </th>
                            @foreach($items as $item)
                                @if($item->product)
                                    <td>
                                        <div class="compare-product">
                                            <div class="compare-product-img">
                                                <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$item->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$item->product->name}}" />
                                                <a href="{{route('compare.delete', $item->id)}}" class="compare-remove-btn">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </a>
                                            </div>
                                            <div class="compare-product-title">
                                                <h3>{{$item->product->name}}</h3>
                                                <span>{{($item->product->brand ? get_translation($item->product->brand->name) : ' ')}}</span>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            @endforeach

                        </tr>

                        <tr class="compare-table-column">
                            <th>
                                {{translate('Price')}}
                            </th>
                            @foreach($items as $item)
                                @if($item->product)
                                   <td>{{short_amount($item->product->stock->first()->price)}}</td>
                                @endif
                            @endforeach
                        </tr>

                        <tr class="compare-table-column">
                            <th>{{translate('Discount Price')}}</th>
                            @foreach($items as $item)
                              @if($item->product)
                                <td>{{short_amount(cal_discount($item->product->discount_percentage,$item->product->stock->first()->price))}}</td>
                              @endif
                            @endforeach
                        </tr>

                        <tr class="compare-table-column">
                            <th>{{translate('Ratings')}}</th>
                            @foreach($items as $item)
                                @if($item->product)
                                    <td>
                                        <div class="ratting mb-0">
                                            @php echo show_ratings($item->product->review->avg('ratings')) @endphp
                                        </div>
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        <tr class="compare-table-column">
                            <th>{{translate('Short Description')}}</th>
                            @foreach($items as $item)
                                @if($item->product)
                                    <td>
                                         @php echo $item->product->short_description @endphp
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        <tr class="compare-table-column">
                            <th></th>

                            @foreach($items as $item)
                                @if($item->product)
                                    <td>
                                        @php
                                            $randNum = rand(6666,10000000);
                                            $randNum = $randNum."compare".$randNum;
                                        @endphp

                                        <form class="attribute-options-form-{{$randNum}}">
                                            <input type="hidden" name="id" value="{{ $item->product->id }}">
                                        </form>

                                        <button  data-product_id="{{ $randNum  }}" type="button" class="compare-item-btn addtocartbtn wave-btn">{{translate('Add Cart')}}</button>
                                    </td>
                                @endif
                            @endforeach

                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
