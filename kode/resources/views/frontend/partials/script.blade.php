<script>

    'use strict'
    $('#search-preloader').hide();
    $('.search-result-box').hide();
	"use strict";
    compareItemCount();
	getCartItemData();
	totalCartItem();
    totalCartAmount()
	wishlistItemCount();


    // Quick View
    $('.quick--view--product').on('click', function (){

        var modal = $('#quickView');
        var productId = $(this).data('product_id');
        var slug = $(this).attr('data-slug');
        $.ajax({
            url: "{{route('quick.view.item')}}",
            method: "GET",
            data: {
                id:productId,
                slug:slug,
            },

            beforeSend: function() {
                modal.find('.quick-view-modal-body').html(`<div class="quick-view-loader">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
               </div>`);
            },
            success: function(response){
                modal.find('.quick-view-modal-body').html(response);

            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
                },
            complete: function() {




            },
        });
        modal.modal('show');
    });




	/* Qty Plus and minus */
    $(document).on('click','.plus', function(){
	    var $data = $("#qty");
	    var value = $data.val();
	    value++;
	    $(".minus").prop("disabled", !value);
	    $data.val(value);
	});

	$(".minus").prop("disabled", !$("#qty").val());
    $(document).on('click','.minus', function(){
	    var $data = $("#qty");
	    var minusvalue = $data.val();
	    if (minusvalue > 1) {
	        minusvalue--;
	        $data.val(minusvalue);
	    }
	    else {
	        $(".minus").prop("disabled", true);
	    }
	});

    $(document).on('keyup','#qty', function(){
		var value = $(this).val();
		if(value <= 0){
			('error', "Please enter positive number");
    		$("#qty").val(1);
		}
	});

	/* Attribute and attribute value selsected */
	$(document).on('click','.size-variant-item', function(){
		var key = $(this).data('key');
	    $(`#${key}`).find('.size-variant-item').removeClass('size-variant-item-active');
	    $(this).addClass('size-variant-item-active');
	});

    /* change button text */
    function changeButtonText(e){
           $(e.target).html(
                `<i class="fa-solid fa-basket-shopping"></i>
                {{translate('Add to cart')}}
                `
            )
    }
    /* chnage button text end

    /*Remove Product Item from Cart*/
    $(document).on('click', '.remove-cart-data', function (e) {
    	var item  = $(this);
    	var subtotal = $('#subtotalamount').text();
    	var itemamount = item.parents('.cart-item').find('.item-product-amount').text();
        var id = $(this).data('id');
        var $element = $(this);
        var html  = $element.html();
        $.ajax({
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
            url: "{{route('cart.delete')}}",

            beforeSend: function() {

                    $element.html(`<div class="spinner-border spinner-border-sm cart-spinner" role="status">
                                                <span class="visually-hidden"></span>
                                    </div>`);
                    $('.order-summary-loader').removeClass('d-none');
                    
                },
            method: "POST",
            data: { id: id},
            success: function (response) {
                if (response.status){


                	item.parents('.cart-item').remove();
                	if(itemamount){
                		var number = (subtotal - itemamount).toFixed(2)
                        $('#subtotalamount').text(number);
                        $('#totalamount').text(number);
                    }
                    if(response.coupon){
                        $('.order-coupon-item').addClass('d-none');
                    }
                	getCartItemData();
                	totalCartItem();
                    totalCartAmount()
                    fetchCartItem();
                    $('.order-summary').html(response.order_summary)
                    toaster(response.message,'success')
                    $(`#cart-${id}`).remove()
                }else{
                    toaster(response.message,'danger')

                }
            },

            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){
                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
                },
            complete: function() {


                $element.html(html);

            },
        });
    });



    $(".chanage_currency").on("click", function() {
        var currency = $(this).attr('data-value')
        window.location.href = "{{route('home')}}/currency/change/"+currency;
    });

    // Wish list Item
    $(document).on('click', '.wishlistitem', function (e){
        event.preventDefault();
        var login =  {{ auth()->check() ? 'true' : 'false' }};
        var productId = $(this).attr('data-product_id');

            var $element = $(this);
            $.ajax({
                type:"GET",
                url:"{{route('user.wish.item.store')}}",
                data:{
                    product_id : productId,
                },
                dataType:'json',
                beforeSend: function() {
                    $element.attr('disabled',true);
                    $element.find('i').addClass("d-none");
                    $element.append(`<div class="spinner-border cart-spinner" role="status">
                                                <span class="visually-hidden"></span>
                                    </div>`);
                },
                success:function(data){
                    if(data.status){
                    	wishlistItemCount();
                        if( data.is_removed){
                            $element.html(`<i class="fa-regular fa-heart"></i>`)
                        }else{
                                        $element.html(`<i class="fa-solid fa-heart"></i>`)
                        }
                        toaster( data.message,'success')

                    }else{

                        toaster(data.message,'danger')
                    }
                },

                error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
                },
                complete: function() {

                    $element.find('.spinner-border').remove();
                    $element.find('i').removeClass("d-none");
                    $element.attr('disabled',false);

                },
            });

    });

    // Compare list item
    $(document).on('click', '.comparelist', function (e){
        event.preventDefault();
        var login =  {{ auth()->check() ? 'true' : 'false' }};
        var productId = $(this).attr('data-product_id');
        var $element = $(this);
        $.ajax({
            type:"GET",
            url:"{{route('compare.store')}}",
            beforeSend: function() {

                $element.off('click');
                $element.css('cursor', 'not-allowed');

                var margin = '';
                if ($element.find('i').length === 0) {
                    margin = 'mx-1';
                }
                $element.find('.spinner-border').remove();
                $element.prepend(`<div class="spinner-border ${margin} cart-spinner" role="status">
                                            <span class="visually-hidden"></span>
                                 </div>`);

                $element.find('span').addClass("d-none");
                $element.find('i').addClass("d-none");
            },

            data:{
                product_id : productId,
            },
            success:function(data){
                if(data.message){
                    compareItemCount();
                    toaster( data.message,'success')
                }else if(data.error){
                    toaster( data.error,'danger')
                }
            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
            },

            complete: function() {

                $element.find('span').removeClass("d-none");
                $element.find('i').removeClass("d-none");
                $element.find('.spinner-border').remove();
                $element.css('cursor', 'pointer');

            },
        });

    });

    // Cart Qty update
    $(document).on('click','.quantitybutton',function(){

        var cartItemQuantity =  $(this).parents('.cart-item').find('#quantity').val();
        if($(this).hasClass('increment')){
            cartItemQuantity++;
        }else{
            if(cartItemQuantity > 0) {
                cartItemQuantity--;
            }else {
                $(".cart--minus").prop("disabled", true);
            }
        }
        cartItemUpdate($(this), cartItemQuantity);
        
    });


    function cartItemUpdate(object, quantity){

        var item  = object;
        var subtotal = $('#subtotalamount').text();

        var itemTotalPrice = item.parents('.cart-item').find('.item-product-amount').text();
        var itemPrice = item.parents('.cart-item').find('#quantity').data('price');

        var afterCalculationItemTotal = quantity * itemPrice;
        var difference = afterCalculationItemTotal - itemTotalPrice;
        var afterCalculationSubtotal = parseFloat(subtotal) + parseFloat(difference);
        var id = item.parents('.cart-item').find('#quantity').attr('data-id');
        var html = item.html();


        $.ajax({
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            url: "{{route('cart.update')}}",
            method:"POST",

            data: { id: id, quantity: quantity},

            beforeSend: function() {
                $('.quantitybutton').attr('disabled',true);
                object.html(`<div class="spinner-border " role="status">
                                            <span class="visually-hidden"></span>
                                </div>`);

            },

            success: function(response){

                
                if(response.success){
                    getCartItemData();
                    totalCartItem();
                    totalCartAmount()
                    fetchCartItem();
                    $('#subtotalamount').text(parseFloat(afterCalculationSubtotal).toFixed(2));
                    $('#totalamount').text(parseFloat(afterCalculationSubtotal).toFixed(2));
                    item.parents('.cart-item').find('.item-product-amount').text(parseFloat(afterCalculationItemTotal.toFixed(2)));
                    if(response.coupon){
                        $('.order-coupon-item').addClass('d-none');
                    }
                    $(item).parents('.cart-item').find('#quantity').val(quantity)
                    toaster(response.success,'success')

             

                 
                }else{
                    toaster( response.error,'danger')

                }
            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
            },
            complete: function() {
                item.html(html);

                $('.quantitybutton').attr('disabled',false);

            },
        });
    }


    function fetchCartItem(){

        
        $.ajax({

            url: "{{route('cart.view')}}",
            method:"get",
            beforeSend: function() {
                $('.cart-item-loader').removeClass('d-none');
            },
            dataType:'json',

            success: function(response){

                $('.cart-table').html(response.html)
            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
            },
            complete: function() {
              
                $('.cart-item-loader').addClass('d-none');
            },
        });

    }

    // Coupon apply
    $(document).on('click', '.apply-btn', function(){
        var code = $('input[name=coupon_code]').val();
        var subtotal = $('#subtotalamount').attr('data-sub');
        var shippingcost = $('#shipping_cost').text();

        var $element = $(this);

        var  html =  $element.html();



        $.ajax({
            url: "{{route('user.apply.coupon')}}",
            method:"POST",
            beforeSend: function() {


                $element.html(`<div class="spinner-border  cart-spinner" role="status">
                                            <span class="visually-hidden"></span>
                                </div>`);


            },

            data:{ _token:"{{ csrf_token()}}",code:code,subtotal:subtotal},
            success: function(response){
                if(response.success) {
                    $('#couponamount').text(response.amount);
                    $('#couponcode').text(response.code);
                    var afterdiscount = parseFloat(subtotal) - parseFloat(response.amount);
                    var totalcartamount = parseFloat(afterdiscount) + parseFloat(shippingcost);

                    switch("{{site_settings('currency_position',App\Enums\StatusEnum::true->status())}}"){
                        case "{{App\Enums\StatusEnum::true->status()}}":
                            totalcartamount = "{{show_currency()}}"+totalcartamount;
                            break;
                        case "{{App\Enums\StatusEnum::false->status()}}":
                           totalcartamount = totalcartamount+"{{show_currency()}}";
                            break;
                
                    }

                    $('#totalamount').text(((totalcartamount)));
                    $('.order-coupon-item').removeClass('d-none');
                    getCartItemData();

                    toaster(response.success,'success')

                }else if(response.error){
                    toaster(response.error,'danger')

                }else{
                    toaster('error','danger')
                }
                $('input[name=coupon_code]').val('');
            },

            error: function (error){
                if(error && error.responseJSON){
                    if(error.responseJSON.errors){
                        for (let i in error.responseJSON.errors) {
                            toaster(error.responseJSON.errors[i][0],'danger')
                        }
                    }
                    else{
                        if((error.responseJSON.message)){

                            toaster(error.responseJSON.message,'danger')
                        }
                        else{
                            toaster(error.responseJSON.error,'danger')
                        }
                    }
                }
                else{
                    toaster(error.message,'danger')
                }
            },
            complete: function() {

                $element.html(html);

           }
        });



    });

    // Shipping method
    $('input[type=radio][name=shipping_method]').on('change', function(){
        var couponamount = $('#couponamount').text();
        if(couponamount == ""){
            couponamount = 0;
        }
        var subtotalamount = $('#subtotalamount').attr('data-sub');
        var value = $(this).data('shipping_price');
        var totalamount = parseFloat(subtotalamount) + parseFloat(value);
        var aftertotalamount = parseFloat(totalamount) - parseFloat(couponamount);
        var aftertotalamount = aftertotalamount.toFixed(2);
        $('#shipping_cost').text(`${value}`);

        switch("{{site_settings('currency_position',App\Enums\StatusEnum::true->status())}}"){
            case "{{App\Enums\StatusEnum::true->status()}}":
                aftertotalamount = "{{show_currency()}}"+aftertotalamount;
                break;
            case "{{App\Enums\StatusEnum::false->status()}}":
                aftertotalamount = aftertotalamount+"{{show_currency()}}";
                break;
    
        }

        $('#totalamount').text((aftertotalamount));
        $('.order-shipping-cost').removeClass('d-none');
    });



    function getCartItemData() {
        $.ajax({
            url: "{{ route('cart.data.get') }}",
            method: "GET",
            beforeSend: function() {

                $('.cart-loader').removeClass('d-none');

            },
            success: function (response) {

                $('.cart--itemlist').html(response);
            },

            error: function (error){
                   
            },

            complete: function() {

                setTimeout(() => {
                    $('.cart-loader').addClass('d-none');
                }, 2000);
            },
        });
    }

    function totalCartItem() {
        $.ajax({
            url: "{{ route('cart.total.item') }}",
            method: "GET",
            success: function (response) {

                if(response!=0){
                  $('.cart-items-count').removeClass("d-none");
                  $('.cart-items-count').text(response);
                }
                else{
                    $('.cart-items-count').addClass("d-none");
                    $('.cart-items-count').text(' ');
                }
            }
        });
    }
    function totalCartAmount() {
        $.ajax({
            url: "{{ route('cart.total.amount') }}",
            method: "GET",
            success: function (response) {
                var res = "{{show_currency()}}"+response
                $('#total-cart-amount').html(res)

            }
        });
    }

    function wishlistItemCount() {
        $.ajax({
            url: "{{route('wish.total.item')}}",
            method: "GET",
            success: function (response) {
                if(response!=0){
                    $('.wishlist--itemcount').removeClass("d-none");
                    $('.wishlist--itemcount').text(response);
                }
                else{
                    $('.wishlist--itemcount').addClass("d-none");
                    $('.wishlist--itemcount').text(' ');
                }
            }
        });
    }

    function compareItemCount() {
        $.ajax({
            url: "{{route('compare.total.item')}}",
            method: "GET",
            success: function (response) {
                if(response!=0){
                  $('.compare--total--item').removeClass("d-none");
                  $('.compare--total--item').text(response);
                }
                else{
                    $('.compare--total--item').addClass("d-none");
                    $('.compare--total--item').text(' ');
                }
            }
        });
    }





    /**
     *  newly implemented srcipts
     *
     * */

    //live product search
    $(document).on('keyup','#search-text',function(e){
        const searchText =  $(this).val();
        const category =  $('.category_select').val();

        if(!searchText){
            $('#search-result').html('');
            $('.search-result-box').hide();
            $('.input-bar-outline').removeClass('input-bar-outline')
        }
        else{
            $('#search-result').html('');
            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                url: "{{route('product.live.search')}}",
                method:"get",
                data: {
                    searchData:searchText,
                    category:category,
                },
                dataType:"json",
                beforeSend: function () {
                    $('.live-search-loader').show();
                },
                success: function(response){
                    $('.search-result-box').removeClass('d-none')
                    $('.search-result-box').show();
                    $('#search-result').html('');
                    const products = response.products
                    if(!response.success){
                        $('#search-result').html(`
                        <div class="text-center no-products">
                                <div class="icon">
                                        <img src={{asset('assets/images/emptyData.png')}} alt="Empty Data">
                                </div>

                                <h3>
                                    No Data found
                                </h3>
                            </div>
                        `)
                    }
                    else{
                        var result = `<ul  class="live-search-data">`;
                        for(var index in products){
                            var url =  "{{route('product.details',[":slug",":id"])}}"
                            url = (url.replace(':slug', products[index].slug ? products[index].slug : convertSlug(products[index].name))).replace(':id', products[index].id);

                            result+=`
                                <li>
                                    <a href="${url}">
                                            <span> <img  src="${products[index].featured_image}" alt="feature.jpg"></span>
                                            <div>
                                                <h4 class="product-title">${products[index].name}</h4>
                                            </div>
                                    </a>
                                </li>`
                        }
                        result+=`</ul>`
                        $('#search-result').append(result)
                    }

                },
                complete: function () {
                    $('.live-search-loader').hide();
                },
            });
        }


    })



   //convert string to slug start
    function convertSlug(Text) {
        return Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    }

    // image preview start
    $(document).on('click','.preview-image-test',function(e){
        e.preventDefault()
        let src = $(this).attr('src');
        $('.test-src').attr('src',src)
    })

    //search faq
    $('#searchFaq').keyup(function(){
        var value = $(this).val().toLowerCase();
        $('.accordion-item').each(function(){
            var lcval = $(this).text().toLowerCase();
            if(lcval != 'Promotional Offer'){
                if(lcval.indexOf(value)>-1){
                    $(this).show();
                } else {
                    $(this).hide();
                }
            }
        });
    });


	/* Add to Cart Product */
	$(document).on('click', '.addtocartbtn', function (e) {

        e.preventDefault();
        var campaign_id = $(this).attr('data-campaign-id');
        var quantity = $('input[name="quantity"]').val();
        var form = $('.attribute-options-form')[0];

        var id = $(this).attr("data-product_id")

        if(id){
            form =  $(`.attribute-options-form-${id}`)[0];
        }

        
        var $element = $(this);
        var checkout = $element.attr('data-checkout'); 


        var formData = new FormData(form);

        if(checkout){
            formData.append('checkout', 1);
        }


        $.ajax({
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
            url: "{{route('cart.store')}}",

            beforeSend: function() {

                $element.off('click');
                $element.css('cursor', 'not-allowed');
                var margin = '';
                if ($element.find('i').length === 0) {
                    margin = 'mx-1';
                }
                $element.find('.spinner-border').remove();
                $element.prepend(`<div class="spinner-border ${margin} cart-spinner" role="status">
                                            <span class="visually-hidden"></span>
                                 </div>`);

                $element.find('span').addClass("d-none");
                $element.find('i').addClass("d-none");

            },
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
            
                setTimeout(() => {
                
                    if (response.success){
                        getCartItemData();
                        totalCartItem();
                        totalCartAmount();
                         var checkout = $element.attr('data-checkout'); 
                        if( checkout && checkout == 'yes'){
                            window.location.href = '{{route("user.checkout")}}'; 
                        }else{
                            toaster(response.success,'success')
                        }

                    }else if(response.error){
                        toaster(response.error,'danger')
                    }else{
                        $.each(response.validation, function (i, val) {
                            toaster(val,'danger')
                        });
                    }
                }, 1000);
            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
            },
            complete: function () {
                setTimeout(() => {
                    $element.find('span').removeClass("d-none");
                    $element.find('i').removeClass("d-none");
                    $element.find('.spinner-border').remove();
                    $element.css('cursor', 'pointer');

                }, 1000);

            },

        });

    });

    /** add to cart btn change method */


    /**wishlist qty update */
    $('.qty_update').on("click",function(){
        var cartItemQuantity =  $(this).parents('.wish-item').find('#quantity').val();
        if($(this).hasClass('increment')){
            cartItemQuantity++;
        }else{
            if(cartItemQuantity > 1) {
                cartItemQuantity--;
            }
        }
        $(this).parents('.wish-item').find('#quantity').val(cartItemQuantity);

    });

</script>
