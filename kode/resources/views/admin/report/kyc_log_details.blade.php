@extends('admin.layouts.app')
@push('style-push')

   <style>
        .kyc-img{
            max-width: 120px;
            width: 100%;
            aspect-ratio: 1/1;
            >img{
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        }
   </style>

@endpush
@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate('KYC Details') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">
                                {{ translate('Home') }}
                            </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('seller.kyc.log.list') }}">
                                {{ translate('KYC Logs') }}
                            </a></li>

                        <li class="breadcrumb-item active">
                            {{ translate('KYC Details') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0">
                                    {{ translate('KYC Details') }}
                                </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush">

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">
                                            @if(@$report->seller)
                                               {{ translate('Seller') }} :
                                            @else
                                                {{ translate('Deliveryman') }} :
                                            @endif
                                        </span>
                                        <span>
                                            @if(@$report->seller)
                                                <span>{{@$report->seller->name}}</span><br>
                                                <a href="{{route('admin.seller.info.details', $report->seller_id)}}" class="fw-bold text-dark">{{(@$report->seller->email)}}</a>
                                            @elseif(@$report->deliveryMan)
                                                    <span>{{@$report->deliveryMan->first_name}}</span><br>
                                                    <a href="{{route('admin.delivery-man.edit', @$report->deliveryMan->id)}}" class="fw-bold text-dark">{{(@$report->deliveryMan->email)}}</a>
                                            @endif
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Date') }} :
                                        </span>
                                        <span>
                                            {{ diff_for_humans($report->created_at) }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <span class="fw-semibold text-break">{{ translate('Status') }} :
                                            
                                            </span>
                                            @if ($report->status == App\Enums\KYCStatus::APPROVED->value)
                                                <span class="badge badge badge-soft-success">{{ translate('Approved') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::REQUESTED->value)
                                                <span class="badge badge-soft-warning">{{ translate('Requested') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::HOLD->value)
                                                <span
                                                    class="badge badge-soft-info">{{ translate('Hold') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::REJECTED->value)
                                                <span class="badge badge-soft-danger">{{ translate('Rejected') }}</span> 
                                            @endif
                                    </li>

                                    @if($report->custom_data)

                                       @foreach ($report->custom_data as $key => $data)

                                        @if($key != 'files')
                                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                    <span class="fw-semibold text-break">{{ k2t( $key) }} :
                                                    </span>
                                                    <span>
                                                        {{ $data }}
                                                    </span>
                                                </li>
                                        @elseif($key == 'files')
                        
                                            @foreach ( $data as $imgKey => $file )
                                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                    <span class="fw-semibold text-break">{{ k2t( $imgKey) }} :
                                                    </span>
                                                    <span class="kyc-img">
                                                          <a target='_blank' href="{{show_image(file_path()['seller_kyc']['path'] ."/".$file)}}">

                                                              <img  src="{{show_image(file_path()['seller_kyc']['path'] ."/".$file)}}" alt="{{ k2t( $imgKey) }}" class="img-fluid img-thumbnail">
                                                          </a>
                                                    </span>
                                                </li>    
                                            @endforeach   
                                        @endif   
                                       @endforeach
                                    
                                    @endif

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Feedback') }} :
                                        </span>
                                        <span>
                                            {{ $report->feedback ?  $report->feedback  :'N/A' }}
                                        </span>
                                    </li>
                                
                            </ul>
                            

                            @if($report->status == 2 && ( $report->seller || $report->deliveryMan ))
                                <div class="text-center mt-3">
                                    
                                    <div class="gap-2 d-flex align-items-center w-">
                                        <button data-id='{{$report->id}}' data-status ="1"  class=" update-report btn btn-success btn-sm w-100 waves ripple-light">
              
                                            <i class="ri-check-double-fill me-1 align-bottom"></i>
                                            {{translate('Accept')}}
                                        </button>
                                        <button data-id='{{$report->id}}'   data-status ="4" class="update-report  btn btn-danger btn-sm w-100 waves ripple-light">
                                            <i class="ri-close-line  me-1 align-bottom"></i>
                                            {{translate('Reject')}}
                                        </button>
                                    </div>

                                </div>
                            @endif

                        </div>
                    </div> 
                </div>
            </div>

        </div>
    </div>




    <div class="modal fade" id="reportUpdate" tabindex="-1" aria-labelledby="reportUpdate" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.report.kyc.log.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="reportId">
                    <input type="hidden" name="status" id="reportStatus">
                    <div class="modal-body">

                         <div>
                             <label for="feedback">
                                 {{translate('Feedback')}} <span class="text-danger" >*</span>
                             </label>
                             <textarea placeholder="{{translate('Type here ....')}}" required class="form-control" name="feedback" id="feedback" cols="30" rows="10"></textarea>
                         </div>
                      
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                        <button type="submit" class="btn btn-danger">{{translate('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('script-push')
<script>
	(function($){
       	"use strict";

		$(".update-report").on("click", function(){
			var modal = $("#reportUpdate");
			modal.find('input[name=id]').val($(this).attr('data-id'));
			modal.find('input[name=status]').val($(this).attr('data-status'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush