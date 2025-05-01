@extends('seller.layouts.app')

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
                        </div>
                    </div> 
                </div>
            </div>

        </div>
    </div>
@endsection


