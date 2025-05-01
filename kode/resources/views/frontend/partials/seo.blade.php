@php 
	$seo = null;
	if($seo_content){
		$seo = json_decode($seo_content->value, true);
		$seoImage       = show_image(file_path()['seo_image']['path'].'/'.$seo['seo_image']);
		$seoSocialImage = show_image(file_path()['seo_image']['path'].'/'.$seo['social_image']);

		$metaKeywords     =  Arr::get($seo,"meta_keywords",[]);
		$socialTitle      =  $seo['social_title'];
		$metaDescription  =  $seo['meta_description'];
		$social_description  =  $seo['social_description'];

	}

	if((@$product  && request()->routeIs("product.details")) || 
	(@$digital_product  && request()->routeIs("digital.product.details"))){

		if(@$digital_product)@$product = @$digital_product;
		
		$seoSocialImage = show_image(file_path()['product']['featured']['path'].'/'.@$product->featured_image,file_path()['product']['featured']['size']);
		$seoImage       = $seoSocialImage;
		$metaKeywords     =  @$product->meta_keywords ?? [];
		$socialTitle      =  @$product->meta_title;
		$metaDescription  =  @$product->meta_description;
		$social_description  = $metaDescription;
	}

	



@endphp

@if($seo)
	<meta name="title" content="{{$socialTitle}}">
	<meta name="description" content="{{$metaDescription}}">
	<meta name="robots" content="index,follow">
	<meta itemprop="image" content="{{$seoImage}}">
	<meta property="og:url" content="{{url()->current()}}">
	<meta property="og:type" content="website">
	<meta property="og:title" content="{{$socialTitle}}">
	<meta property="og:description" content="{{$social_description}}">
	<meta property="og:image" content="{{$seoSocialImage}}">
	<meta name='keywords' content='{{implode(",",Arr::get($seo,"meta_keywords",""))}}'>

@endif