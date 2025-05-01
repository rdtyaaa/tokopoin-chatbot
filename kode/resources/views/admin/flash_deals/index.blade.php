@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{$title}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}

                    </a></li>
                    <li class="breadcrumb-item active">
                        {{$title}}
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
                                {{translate('Flash Deal List')}}
                            </h5>
                        </div>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.promote.flash.deals.create')}}" class="btn btn-success btn-sm add-btn w-100 waves ripple-light"><i
                                class="ri-add-line align-bottom me-1"></i>
                                {{translate('Add New')}}
                            </a>
                        </div>
                    </div>


                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.promote.flash.deals.index')}}" method="get">
                    <div class="row g-3">
                        <div class="col-lg-5">
                            <div class="search-box">
                                <input type="text" name="name" class="form-control search"
                                    placeholder="{{translate('Search by name')}}"  value="{{request()->input('name')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-4 col-6">
                            <div>
                                 <button type="submit" class="btn btn-primary w-100  waves ripple-light" > <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Filter')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-2 col-sm-4 col-6">
                            <div>
								<a href="{{route('admin.promote.flash.deals.index')}}" class="btn btn-danger add-btn waves w-100 ripple-light">
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
                                <th scope="col">
									{{translate('Total Products')}}
								</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
							</tr>
						</thead>

						<tbody>
							@forelse ($flash_deals as $flash_deal)
                                <tr>
                                    <td class="fw-medium">
                                        {{$loop->iteration}}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm object-fit-cover" src="{{show_image(file_path()['flash_deal']['path'].'/'.$flash_deal->banner_image,file_path()['flash_deal']['size'])}}" alt="{{$flash_deal->name}}"
                                                >
                                            </div>
                                            <div class="flex-grow-1">
                                              {{$flash_deal->name}}

                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{translate("Form")}}
                                        <span class=" text-muted" >{{  get_date_time(($flash_deal->start_time))}}
                                        </span>
                                        <br>
                                        {{translate("To")}}
                                        <span class=" text-muted" >{{
                                                get_date_time(($flash_deal->end_time))
                                                }}
                                        </span>

                                    </td>

                                    <td class="fw-medium">
                                        {{$flash_deal->products ? count($flash_deal->products) :0}}
                                    </td>

                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" value="{{$flash_deal->id}}"

                                            {{$flash_deal->status == 1 ? 'checked':""  }}
                                            data-route="{{ route('admin.promote.flash.deals.status.update') }}"
                                            class="status-update form-check-input"
                                            data-id="{{$flash_deal->id}}"
                                            id="{{$flash_deal->id}}" >
                                            <label class="form-check-label" for="{{$flash_deal->id}}"></label>
                                        </div>


                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            <a title="Update" class="link-warning fs-18"  href="{{route('admin.promote.flash.deals.edit', $flash_deal->id)}}"><i class="ri-pencil-fill"></i>
                                            </a>

                                            <a href="javascript:void(0);" data-href="{{route('admin.promote.flash.deals.delete',$flash_deal->id)}}" class="delete-item fs-18 link-danger">
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
			</div>

		</div>
	</div>

</div>
@include('admin.modal.delete_modal')
@endsection


