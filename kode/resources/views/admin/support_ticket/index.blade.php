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
                        {{translate("Tickets")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header  border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Ticket List')}}
                    </h5>
                </div>
            </div>
     

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}"  class="form-control search"
                                    placeholder="{{translate('Search by name , email , ticket number or subject')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <select class="form-select" name="type" id="">

                                     <option {{request()->input('type') ==  0 ? "selected" :""}} value="0">
                                         {{translate("All")}}
                                     </option>
                                     <option {{request()->input('type') ==  1 ? "selected" :""}} value="1">
                                         {{translate("User")}}
                                     </option>
                                     <option {{request()->input('type') ==  2 ? "selected" :""}} value="2">
                                         {{translate("Seller")}}
                                     </option>

                                     <option {{request()->input('type') ==  3 ? "selected" :""}} value="3">
                                        {{translate("Deliveryman")}}
                                    </option>
                       

                                </select>
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

            <div class="card-body pt-0">
                <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.support.ticket.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.support.ticket.index')}}" >
                            <i class="ri-question-answer-line me-1 align-bottom"></i>
                            {{translate('All
                            Tickets')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.support.ticket.running') ? 'active' :''}}  Placed py-3"  id="running"
                            href="{{route('admin.support.ticket.running')}}" >
                            <i class="ri-message-2-fill me-1 align-bottom"></i>
                            {{translate('Running Ticket')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.support.ticket.answered') ? 'active' :''}} Confirmed py-3"  id="Confirmed"
                            href="{{route('admin.support.ticket.answered')}}" >
                            <i class="ri-chat-check-line  me-1 align-bottom"></i>
                            {{translate("Answered Ticket")}}

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link replied {{request()->routeIs('admin.support.ticket.replied') ? 'active' :''}}   py-3"  id="replied"
                            href="{{route('admin.support.ticket.replied')}}" >
                            <i class="ri-chat-check-fill me-1 align-bottom"></i>
                            {{translate('Replied Ticket')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cancel {{request()->routeIs('admin.support.ticket.closed') ? 'active' :''}}   py-3"  id="cancel"
                            href="{{route('admin.support.ticket.closed')}}" >
                            <i class="ri-chat-off-fill me-1 align-bottom"></i>

                            {{translate('Closed Ticket')}}
                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card ">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Time"
                                    )}}
                                </th>
                                <th>
                                    {{translate('Subject')}} -    {{translate('Ticket Number')}}
                                </th>
                                <th  >{{translate('Submitted By')}}
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
                            @forelse($supportTickets as $supportTicket)
                                <tr>

                                    <td data-label="{{translate('Time')}}">
                                        <span class="fw-bold">{{diff_for_humans($supportTicket->created_at)}}</span><br>
                                        {{get_date_time($supportTicket->created_at)}}
                                    </td>

                                    <td data-label="{{translate('Subject')}}">
                                        <span class="fw-bold"><a href="{{route('admin.support.ticket.details', $supportTicket->id)}}">{{$supportTicket->subject}}</a></span>
                                        -
                                        <span class="fw-bold"><a href="{{route('admin.support.ticket.details', $supportTicket->id)}}">{{$supportTicket->ticket_number}}</a></span>

                                    </td>

                                    <td data-label="{{translate('Submitted By')}}">
                                        @if($supportTicket->user_id)
                                            <a href="{{route('admin.customer.details',@$supportTicket->user_id)}}" class="fw-bold text-dark">{{(@$supportTicket->user->email)}}</a>
                                        @elseif($supportTicket->seller_id)
                                            <a href="{{route('admin.seller.info.details', $supportTicket->seller_id)}}" class="fw-bold text-dark">{{(@$supportTicket->seller?@$supportTicket->seller->email :"N/A")}}</a>
                                        @elseif($supportTicket->deliveryman_id)
                                            <a href="{{route('admin.delivery-man.edit', $supportTicket->deliveryman_id)}}" class="fw-bold text-dark">{{(@$supportTicket->deliveryMan?@$supportTicket->deliveryMan->email :"N/A")}}</a>
                                        @else
                                            <span class="fw-bold">{{$supportTicket->name}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Priority')}}">
                                        @if($supportTicket->priority == 1)
                                            <span class="badge badge-soft-info">{{translate('Low')}}</span>
                                        @elseif($supportTicket->priority == 2)
                                            <span class="badge badge-soft-primary">{{translate('Medium')}}</span>
                                        @elseif($supportTicket->priority == 3)
                                            <span class="badge badge-soft-success">{{translate('High')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Status')}}">
                                        @if($supportTicket->status == 1)
                                            <span class="badge badge-soft-info">{{translate('Running')}}</span>
                                        @elseif($supportTicket->status == 2)
                                            <span class="badge badge-soft-primary">{{translate('Answered')}}</span>
                                        @elseif($supportTicket->status == 3)
                                            <span class="badge badge-soft-warning">{{translate('Replied')}}</span>
                                        @elseif($supportTicket->status == 4)
                                            <span class="badge badge-soft-danger">{{translate('Closed')}}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{translate('Last Reply')}}">
                                        @if(count($supportTicket->messages) >0 )
                                            {{ \Carbon\Carbon::parse($supportTicket->messages->first()->created_at)->diffForHumans() }}
                                        @else

                                            N/A
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Action')}}">
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Details')}}" href="{{route('admin.support.ticket.details', $supportTicket->id)}}" class="btn-primary fs-18"><i class="ri-eye-line"></i></a>
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
                        {{$supportTickets->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.modal.delete_modal')

@endsection
