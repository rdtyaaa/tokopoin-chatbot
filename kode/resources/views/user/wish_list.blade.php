
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
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="card-title">
                            {{translate("wishlist product")}}
                        </h4>
                    </div>
                    @if(auth_user('web'))

                        <a class="view-more-btn" href="{{route('user.dashboard')}}">
                             {{translate('Dashboard')}}
                        </a>

                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr class="text-muted fs-14">
                                <th scope="col" class="text-start">
                                    {{translate("Product")}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{translate("Qty")}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{translate("Price")}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{translate("Discount Price")}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{translate('Stock')}}
                                </th>
                                <th scope="col" class="text-end">
                                    {{translate("Actions")}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="border-bottom-0">
                            @forelse($wishlists as $wishlist)
                                @if($wishlist->product)
                                    <tr class="fs-14 wish-item tr-item" id="wishlist-{{$wishlist->id}}">
                                        <td>
                                            <div class="wishlist-product align-items-center">
                                                <div class="wishlist-product-img">
                                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$wishlist->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$wishlist->product->name}}">
                                                </div>

                                                <div class="wishlist-product-info">
                                                    <h4 class="product-title">{{$wishlist->product->name}}</h4>

                                                    <div class="ratting mb-0">
                                                        @php echo show_ratings($wishlist->product->review->avg('ratings')) @endphp
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @php
                                                $randNum = rand(6666,10000000);
                                                $randNum = $randNum."wish".$randNum;
                                            @endphp
                                            <form class="attribute-options-form-{{$randNum}}">
                                                <div class="input-step ">
                                                    <button type="button" class="qty_update x decrement">–</button>

                                                            <input type="hidden" name="id" value="{{ $wishlist->product->id }}">
                                                            <input type="number" class="product-quantity"  name="quantity" value="1" id="quantity">

                                                    <button type="button" class="qty_update y increment ">+</button>
                                                </div>
                                            </form>
                                        </td>

                                        <td class=" text-center">
                                            {{short_amount($wishlist->product->stock->first()?$wishlist->product->stock->first()->price:$product->price)}}
                                        </td>

                                        <td class=" text-center">
                                            @if(($wishlist->product->discount_percentage) > 0)
                                            {{short_amount(cal_discount($wishlist->product->discount_percentage,$wishlist->product->stock->first()->price))}}
                                            @else
                                                {{translate("N/A")}}
                                            @endif
                                        </td>

                                        <td class=" text-center">
                                            @if($wishlist->product->stock->isNotEmpty())
                                                <span class="badge-soft-success fs-12 badge"> {{translate('Instock')}}</span>
                                                @else
                                                <span class="badge-soft-danger fs-12 badge">
                                                    {{translate('Sold Out')}}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex align-items-center gap-3 justify-content-end">
                                                <a href="javascript:void(0)" data-product_id="{{ $randNum  }}" class="badge badge-soft-primary fs-12 pointer addtocartbtn">
                                                    <span class="buy-now-icon d-flex">
                                                    <svg  version="1.1"  x="0" y="0" viewBox="0 0 511.997 511.997"   xml:space="preserve" ><g><path d="M405.387 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536c14.083 0 25.536 11.453 25.536 25.536s-11.453 25.536-25.536 25.536zM507.927 115.875a19.128 19.128 0 0 0-15.079-7.348H118.22l-17.237-72.12a19.16 19.16 0 0 0-18.629-14.702H19.152C8.574 21.704 0 30.278 0 40.856s8.574 19.152 19.152 19.152h48.085l62.244 260.443a19.153 19.153 0 0 0 18.629 14.702h298.135c8.804 0 16.477-6.001 18.59-14.543l46.604-188.329a19.185 19.185 0 0 0-3.512-16.406zM431.261 296.85H163.227l-35.853-150.019h341.003L431.261 296.85zM173.646 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536 25.536 11.453 25.536 25.536-11.453 25.536-25.536 25.536z" opacity="1" data-original="#000000" ></path></g></svg>
                                                    </span>
                                                </a>

                                                @php
                                                   $product = $wishlist->product;
                                                   $slug = $product->slug ? $product->slug : make_slug($product->name);
                                                @endphp

                                                <a href="{{route('product.details',[$slug,$product->id])}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i></a>

                                                <button data-id="{{$wishlist->id}}" class=" remove-wishlist-item badge badge-soft-danger fs-12 pointer"><i class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                            <tr>
                                <td class="text-center py-5" colspan="100">{{translate('No Data Found')}}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                <div class="mt-4 d-flex align-items-center justify-content-end">
                        {{$wishlists->links()}}
                </div>
            </div>

        </div>
    </div>
</section>

@endsection


@push('scriptpush')

<script>
   "use strict"
    $(document).on('click', '.remove-wishlist-item', function(e) {

    var item = $(this);
    var id = $(this).attr('data-id');
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            url: "{{route('user.wish.item.delete')}}",
            method: "POST",
            data: {
                id: id
            },
            success: function(response) {
                if (response.success) {
                    item.parents('.wish-item').remove();
                    wishlistItemCount();
                    toaster(response.success,'success')

                } else {
                    toaster(response.error,'danger')

                }
            }
        });
    });


</script>

@endpush
