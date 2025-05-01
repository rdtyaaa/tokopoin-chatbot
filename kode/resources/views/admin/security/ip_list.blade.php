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
                            {{translate('Visitors')}}
                        </li>
                    </ol>
                </div>
            </div>

			<div class="card">
				<div class="card-header border-0">
					<div class="row g-4 align-items-center">
						<div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{translate('IP List')}}
                            </h5>
						</div>
						<div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
								<button type="button" class="btn btn-success add-btn w-100 waves ripple-light"
								data-bs-toggle="modal" data-bs-target="#addIp"><i
									class="ri-add-line align-bottom me-1"></i>
									{{translate('Add New')}}
								</button>
                            </div>
                        </div>
					</div>
				</div>

				<div class="card-body border border-dashed border-end-0 border-start-0">
					<form action="{{route(Route::currentRouteName())}}" method="get">
						<div class="row g-3">
							<div class="col-xl-4 col-lg-5">
								<div class="search-box flex-lg-grow-0 flex-grow-1">
									<input type="text" name="ip_address" value="{{request()->input('ip_address')}}" class="form-control search" placeholder="{{translate('Filter by IP')}}">
									<i class="ri-search-line search-icon"></i>
								</div>
							</div>

							<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
								<div>
									<button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
											class="ri-equalizer-fill me-1 align-bottom"></i>
										{{translate('Search')}}
									</button>
								</div>
							</div>

							<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
								<div>
									<a href="{{route(Route::currentRouteName())}}" class="btn btn-danger w-100 waves ripple-light"> <i
											class="ri-refresh-line me-1 align-bottom"></i>
										{{translate('Reset')}}
									</a>
								</div>
							</div>


						</div>
					</form>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card">
						<table class="table table-centered align-middle table-nowrap mb-1">
							<thead class="text-muted table-light">
								<tr>
									<th scope="col">#</th>
									<th scope="col">
										{{translate('IP')}}
									</th>

									<th scope="col">
										{{translate('Blocked')}}
									</th>

									<th scope="col">
										{{translate('Last Visited')}}
									</th>
									<th scope="col">
										{{translate('Options')}}
									</th>
								</tr>
							</thead>

							<tbody>
								@forelse ($ip_lists as $ip)

									<tr>
										<td class="fw-medium">
											{{$loop->iteration}}
										</td>

										<td>
											{{$ip->ip_address}}
										</td>

										<td>
											<div class="form-check form-switch">
												<input id="status-{{$ip->id}}" type="checkbox" class="status-update form-check-input"
													data-column="is_blocked"
													data-route="{{ route('admin.security.ip.update.status') }}"
													data-model="Visitor"
													data-status="{{ $ip->is_blocked == App\Enums\StatusEnum::true->status() ? App\Enums\StatusEnum::false->status():App\Enums\StatusEnum::true->status()}}"
													data-id="{{$ip->id}}" {{$ip->is_blocked == App\Enums\StatusEnum::true->status() ? 'checked' : ''}} >
												<label class="form-check-label" for="status-{{$ip->id}}"></label>
											</div>

										</td>

										<td>
											{{diff_for_humans($ip->updated_at)}}
									    </td>

										<td>
											<div class="hstack justify-content-center gap-3">
												<a href="javascript:void(0);" data-href="{{route('admin.security.ip.destroy',$ip->id)}}" class="delete-item fs-18 link-danger">
												<i class="ri-delete-bin-line"></i></a>

												<a href="javascript:void(0)" data-agent ="{{collect($ip->agent_info)}}" id="showInfo" class=" fs-18 link-success">
													<i class="ri-eye-line"></i>
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

					<div class="pagination-wrapper d-flex justify-content-end mt-4">
							{{$ip_lists->links()}}
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="addIp"  data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-light p-3">
					<h5 class="modal-title">{{translate('Add IP')}}
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"
						aria-label="Close" ></button>
				</div>
				<form action="{{route('admin.security.ip.store')}}" method="post">
					@csrf
					<div class="modal-body">
						<div class="row g-3">
							<div class="col-12">
								<label for="ip_address" class="form-label">
									{{translate('IP Address')}}  <span  class="text-danger"> *</span>
								</label>

								<input type="text" name="ip_address" id="ip_address" class="form-control"  placeholder="{{translate('Enter IP')}}"
								value="{{old('ip_address')}}" required>

							</div>
						</div>
						<div class="modal-footer px-0 pb-0 pt-3">
							<div class="hstack gap-2 justify-content-end">
								<button type="button"
									class="btn btn-danger waves ripple-light"
									data-bs-dismiss="modal">
									{{translate('Close
									')}}
								</button>
								<button type="submit"
									class="btn btn-success waves ripple-light"
									id="add-btn">
									{{translate('Add')}}
								</button>
							</div>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="agentInfo"  data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-light p-3">
					<h5 class="modal-title">{{translate('Visitor Agent Info')}}
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"
						aria-label="Close" ></button>
				</div>

				<div class="modal-body">
					<div class="row g-3">
						<div class="col-12">
							<ul class="list-group agent-info-list">

							</ul>

						</div>
					</div>
					<div class="modal-footer px-0 pb-0 pt-3">
						<div class="hstack gap-2 justify-content-end">
							<button type="button"
								class="btn btn-danger waves ripple-light"
								data-bs-dismiss="modal">
								{{translate('Close
								')}}
							</button>

						</div>
					</div>

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


		$(document).on('click','#showInfo',function(e){


			var agents = $(this).attr('data-agent');
			var agents =  JSON.parse(agents);

				var list =  "" ;
				for(var i in agents){

					if(agents[i] != ''){
						var outputString = i.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
							list +=`<li class="list-group-item d-flex justify-content-between align-items-center">
									${outputString} : <span class="badge bg-success">${agents[i]}</span>
								</li>`;

					}

				}


			$('.agent-info-list').html(list)

			$('#agentInfo').modal('show');


			e.preventDefault()
		})

	})(jQuery);
</script>
@endpush

