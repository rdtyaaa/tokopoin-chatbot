@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate($title)}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Reviews')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Review list')}}
                        </h5>
					</div>
					
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-centered align-middle table-nowrap mb-0">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate("Customer name")}}
								</th>

                                <th scope="col">
                                            {{translate('Rating')}}
                                </th>
						
								<th scope="col">
									{{translate("Action")}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse($reviews as $review)
								<tr>
									<td class="fw-medium">
								     	{{$loop->iteration}}
									</td>

                                    <td data-label="{{translate('Customer Info')}}" class="text-align-left">
                                        <span>{{translate("Name")}}: {{@$review->customer->name}}</span><br>
                                       
                                        @if($review->customer)
                                            <a href="{{route('admin.customer.details', $review->customer->id)}}" class="fw-bold text-dark">
                                                <span>
                                                    {{translate('Email')}}
                                                    : {{@$review->customer->email ?? "N\A"}}</span>
                                            </a>
                                        @else
                                        <span>
        
                                             {{"N\A"}}</span>
                                        @endif
                                    
                                       
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                                            {{round($review->rating)}}<i class="ri-star-s-fill"></i>
                                        </span>
                                    </td>

								

								
									<td>
										<div class="hstack justify-content-center gap-3">

                                            <a title="{{translate('Show')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-review="{{$review->review}}"  href="javascript:void(0);" class="fs-18 link-success show-review">
                                                <i class="ri-eye-line"></i></a>
									
                                            <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top"  href="javascript:void(0);" data-href="{{route('admin.product.review.delete',$review->id)}}"class="delete-item fs-18 link-danger">
                                                <i class="ri-delete-bin-line"></i></a>
											
										</div>
									</td>
								</tr>
							@empty
									<tr>
										<td class="border-bottom-0" colspan="100">
											@include('admin.partials.not_found')
										</td>
									</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="pagination-wrapper d-flex justify-content-end mt-4">
					{{$reviews->appends(request()->all())->links()}}
				</div>
			</div>
		</div>
	</div>

</div>


<div class="modal fade" id="productReview" tabindex="-1" aria-labelledby="productReview" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Review')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>

            <div class="modal-body">
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Cancel')}}</button>
            
            </div>
	        
        </div>
    </div>
</div>



@include('admin.modal.delete_modal')


@endsection

@push('script-push')
<script>
	(function($){

       	"use strict";
		$('.show-review').on('click', function(){
			var modal = $('#productReview');

            var review = $(this).attr('data-review');
            modal.find('.modal-body').html(review)

			modal.modal('show');
		});

       
	})(jQuery);
</script>
@endpush
