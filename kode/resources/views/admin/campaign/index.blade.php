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
                        {{translate('Campaigns')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Campaign List')}}
                            </h5>
                        </div>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.campaign.create')}}" class="btn btn-success btn-sm add-btn w-100 waves ripple-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                  {{translate('Add New')}}
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.campaign.index')}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by name')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light" > <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
								<a href="{{route('admin.campaign.index')}}" class="btn btn-danger add-btn waves w-100 ripple-light">
                                    <i class="ri-refresh-line align-bottom me-1"></i>
                                    {{translate('Reset')}}
							    </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-centered align-middle table-nowrap">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate('Name')}}
								</th>
                                <th>{{translate('Start Time')}} -{{translate('End Time')}}</th>

                           
                                <th>{{translate('Home Page')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
							</tr>
						</thead>

						<tbody>
							@forelse ($campaigns as $campaign)
                                <tr>
                                    <td class="fw-medium">
                                       {{$loop->iteration}}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm object-fit-cover" src="{{show_image(file_path()['campaign_banner']['path'].'/'.$campaign->banner_image ,file_path()['campaign_banner']['size'])}}" alt="{{$campaign->name}}"
                                                >
                                            </div>
                                            <div class="flex-grow-1">
                                               {{$campaign->name}}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                      {{translate("Form")}}
                                       <span class=" text-muted" >{{  date('l jS \of F Y h:i:s A',strtotime($campaign->start_time))}}</span>
                                       <br>
                                      {{translate("To")}}
                                        <span class=" text-muted" >{{
                                            date('l jS \of F Y h:i:s A',strtotime($campaign->end_time))
                                            }}
                                        </span>
                                    </td>
                                  
                                    <td>
                                        <span class="badge {{$campaign->show_home_page == '1'? 'badge-outline-success' : 'badge-outline-danger'}}" >  {{$campaign->show_home_page == '1'? translate('Yes') : "No"}}  </span>
                                    </td>
                                    <td>
                                        <span class="badge {{$campaign->status =='1'? 'badge-soft-success' :'badge-soft-danger'}}">
                                            {{$campaign->status == "1" ? translate('Active') : translate('Inactive')}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            <a title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="link-warning fs-18"  href="{{route('admin.campaign.edit', $campaign->id)}}"><i class="ri-pencil-fill"></i>
                                            </a>
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
			</div>
		</div>
	</div>

</div>

@endsection


