@extends('frontend.layouts.app')
@section('content')
   @php
     $promo_banner = frontend_section('promotional-offer');
   @endphp

<div class="breadcrumb-banner">
    <div class="breadcrumb-banner-img">
        <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
    </div>
    <div class="page-Breadcrumb">
        <div class="Container">
            <div class="breadcrumb-container">
                <h1 class="breadcrumb-title">{{($title)}}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">
                            {{translate('home')}}
                        </a></li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="pb-80">
    <div class="Container">
        <div class="row g-4">
            @include('user.partials.dashboard_sidebar')

            <div class="col-xl-9 col-lg-8">
                <div class="profile-user-right">
                    <a href="{{@frontend_section_data($promo_banner->value,'image','url')}}" class="d-block">
                        <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image'),@frontend_section_data($promo_banner->value,'image','size'))}}" alt="banner.jpg">
                    </a>

                    <div class="card mt-5">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Support Ticket")}}
                                        </h4>
                                    </div>

                                    <button type="button" class="address-btn wave-btn" data-bs-toggle="modal" data-bs-target="#supportTicketModal">
                                        {{translate("Create Ticket")}}
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-14">
                                            <th class="text-start">
                                                {{translate('Time')}}
                                            </th>
                                            <th class="text-start"> {{translate('Ticket Number')}}</th>
                                            <th class="text-center"> {{translate('Subject')}}</th>
                                            <th class="text-center"> {{translate('Priority')}}</th>
                                            <th class="text-center"> {{translate('status')}}</th>
                                            <th class="text-center"> {{translate('Last Reply')}}</th>
                                            <th class="text-end"> {{translate('Action')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody class="border-bottom-0">

                                        @forelse($supportTickets as $ticket)
                                            <tr class="fs-14 tr-item">
                                                <td class="text-start"> {{get_date_time($ticket->created_at)}}</td>

                                                <td class="text-center"> {{$ticket->ticket_number}}</td>

                                                <td class="text-center"> {{($ticket->subject)}}</td>

                                                <td class="text-center">
                                                    @if($ticket->priority == 1)
                                                        <span class="badge badge-soft-info">{{translate('Low')}}</span>
                                                    @elseif($ticket->priority == 2)
                                                        <span class="badge badge-soft-warning">{{translate('Medium ')}}</span>
                                                    @elseif($ticket->priority == 3)
                                                        <span class="badge badge-soft-danger">{{translate('High')}}</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if($ticket->status == 1)
                                                        <span class="badge badge-soft-warning">{{translate('Running')}}</span>
                                                    @elseif($ticket->status == 2)
                                                        <span class="badge badge-soft-success">{{translate('Answered')}}</span>
                                                    @elseif($ticket->status == 3)
                                                        <span class="badge badge-soft-info">{{translate('Replied')}}</span>
                                                    @elseif($ticket->status == 4)
                                                        <span class="badge badge-soft-danger">{{translate('Closed')}}</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                        @if(count($ticket->messages) >1)
                                                        {{ \Carbon\Carbon::parse($ticket->messages->first()->created_at)->diffForHumans() }}
                                                        @else
                                                        N/A
                                                        @endif
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex align-items-center gap-3 justify-content-end">
                                                        <a href="{{route('user.support.ticket.view', $ticket->ticket_number)}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-muted py-5 text-center" colspan="7"><p>{{translate('No Data Found')}}</p></td>
                                                </tr>
                                            @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex align-items-center justify-content-end">
                                    {{$supportTickets->links()}}
                            </div>
                        </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="supportTicketModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="supportTicketModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportTicketLabel">
                {{translate("Create Ticket")}}
                </h5>
               <button type="button" class="btn btn-danger fs-14 modal-closer rounded-circle" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{route('user.support.ticket.store')}}" method="post" enctype="multipart/form-data">
                 @csrf
                <div class="modal-body">
                    <div class="row g-4">

                            <input hidden type="text" name="name" class="form-control" value="{{auth_user("web")->name}}" id="ticketName" placeholder="{{translate('Enter name')}}" >

                            <input hidden type="email" class="form-control" name="email" value="{{auth_user("web")->email}}" id="ticketEmail" placeholder="{{translate('Enter email')}}" >



                            <div class="col-md-12">

                                <label for="ticketSubject" class="form-label">
                                    {{translate('Subject')}} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="subject" class="form-control" value="{{old('subject')}}" id="ticketSubject" placeholder="{{translate('Enter subject')}}" >

                            </div>

                            <div class="col-md-12">

                                <label for="message" class="form-label">
                                    {{translate('Your Message')}} <span class="text-danger">*</span>
                                </label>
                                    <textarea class="form-control" name="message" aria-label="With textarea" placeholder="{{translate('Your Message')}}" rows="5"></textarea>


                            </div>

                            <div class="col-md-12">

                                <label for="formFile" class="form-label">
                                    {{translate("Attachment")}}
                                </label>
                                <input type="file" multiple name="file[]" id="formFile" class="form-control @error('file') is-invalid @enderror">

                            </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-4">
                        <button type="button" class="btn btn-danger fs-12 px-3" data-bs-dismiss="modal">
                            {{translate("Cancel")}}
                        </button>
                        <button type="submit" class="btn btn-success fs-12 px-3">
                            {{translate('Submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection



