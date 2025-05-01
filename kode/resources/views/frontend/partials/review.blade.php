@foreach($reviews as $product_rating)
    <div class="previous-review">
        <div class="previous-review-content">
            <div class="previous-review-user">
                <img src="{{show_image(file_path()['profile']['user']['path'].'/'.$product_rating->customer->image,file_path()['profile']['user']['size'])}}" alt="{{@$product_rating->customer->image}}" />
            </div>
            <div class="flex-grow-1">
                <div class="d-flex w-100 align-items-start justify-content-between gap-3">
                    <div class="previous-review-info w-100">
                        <h4>{{$product_rating->customer->name}}</h4>
                        <small>{{get_date_time($product_rating->created_at, 'M d, Y')}}</small>
                    </div>
                    <div class="ratting ">
                        @php echo show_ratings(($product_rating->rating)) @endphp
                    </div>
                </div>
                <p class="comment-text">
                    {{($product_rating->review)}}
                </p>
            </div>
        </div>
    </div>
@endforeach