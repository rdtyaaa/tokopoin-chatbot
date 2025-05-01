@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Support Tickets")}}
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Tickets")}}
                    </li>
                </ol>
            </div>

        </div>

		<div class="card">
			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Ticket List')}}
                        </h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<a href='{{route("seller.ticket.create")}}' class="btn btn-success btn-sm add-btn waves ripple-light">
								<i class="ri-add-line align-bottom me-1"></i> {{translate('Add New Ticket')}}
							</a>
						</div>
					</div>
				</div>
			</div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}"  class="form-control search"
                                    placeholder="{{translate('Search by ticket number ')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"> <i class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-nowrap align-middle mb-0">
						<thead class="text-muted table-light">
							<tr class="text-uppercase">
								<th>
									{{translate("Time")}}
								</th>
								<th> {{translate('Ticket Number')}}</th>

								<th>
									{{translate('Subject')}}
								</th>
								<th>
									{{translate('Priority')}}
								</th>
								<th>
									{{translate('Status')}}
								</th>
								<th >
									{{translate('Last Reply')}}
								</th>
								<th >
									{{translate('Action')}}
								</th>
							</tr>
						</thead>

						<tbody class="list form-check-all">
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td data-label="{{translate('Time')}}">
                                        <span class="fw-bold">{{diff_for_humans($ticket->created_at)}}</span><br>
                                        {{get_date_time($ticket->created_at)}}
                                    </td>

                                    <td data-label="{{translate('Ticket Number')}}">
                                        {{$ticket->ticket_number}}
                                    </td>

                                    <td data-label="{{translate('Subject')}}">
                                        {{($ticket->subject)}}
                                    </td>

                                    <td data-label="{{translate('Priority')}}">

                                        @if($ticket->priority == 1)
                                            <span class="badge badge-soft-info">{{translate('Low')}}</span>
                                        @elseif($ticket->priority == 2)
                                            <span class="badge badge-soft-primary">{{translate('Medium ')}}</span>
                                        @elseif($ticket->priority == 3)
                                            <span class="badge badge-soft-success">{{translate('High')}}</span>
                                        @endif

                                    </td>

                                    <td data-label="{{translate('Status')}}">
                                        @if($ticket->status == 1)
                                            <span class="badge badge-soft-info">{{translate('Running')}}</span>
                                        @elseif($ticket->status == 2)
                                            <span class="badge badge-soft-primary">{{translate('Answered')}}</span>
                                        @elseif($ticket->status == 3)
                                            <span class="badge badge-soft-warning">{{translate('Replied')}}</span>
                                        @elseif($ticket->status == 4)
                                            <span class="badge badge-soft-danger">{{translate('Closed')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Last Reply')}}">
                                        @if(count($ticket->messages) >1)
                                           {{ \Carbon\Carbon::parse($ticket->messages->first()->created_at)->diffForHumans() }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td data-label='{{translate("Action")}}'>
                                        <div class="hstack justify-content-center gap-3">
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Details')}}" href="{{route('seller.ticket.detail', $ticket->id)}}" class="link-info fs-18">
                                                <i class="ri-computer-line"></i>
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

				<div class="pagination-wrapper d-flex justify-content-end mt-4 ">
						{{$tickets->links()}}
				</div>
			</div>
		</div>
    </div>
</div>
@endsection

