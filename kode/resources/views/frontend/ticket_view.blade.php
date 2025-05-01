@extends('frontend.layouts.app')
@section('content')

    <section class="pt-80 pb-80">
        <div class="Container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <h4 class="card-title">
                                {{translate($title)}}
                            </h4>
                        </div>


                          @if($ticket->status != 4)
                            <div class="close-btn">
                                <a href="{{route('user.closed.ticket', encrypt($ticket->ticket_number))}}" class="close-ticket">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                        {{translate('Close')}}
                                </a>
                            </div>
                          @endif

                    </div>
                </div>

                <div class="card-body">
                    <div class="replay-card-top">
                        <div class="replay-card-top-left">
                            <span class="customer-reply">
                                @if($ticket->status == 1)
                                    {{translate('Running')}}
                                @elseif($ticket->status == 2)
                                    {{translate('Answered')}}
                                @elseif($ticket->status == 3)
                                    {{translate('Replied')}}
                                @elseif($ticket->status == 4)
                                    {{translate('Closed')}}
                                @endif
                             </span>
                            <p>[{{translate("Ticket ID")}}]</p>
                            <small>{{$ticket->ticket_number}}</small>
                        </div>
                    </div>

                    @if($ticket->status != 4)
                        <form action="{{route('user.ticket.reply', $ticket->id)}}" method="POST" class="ticket-form" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <textarea class="form-control" name="message" aria-label="With textarea" placeholder="{{translate('Your Message')}}" rows="5"></textarea>
                            </div>
                            <div class="attachment">
                                <p>
                                    {{translate("Attachment")}}
                                </p>
                                <div class="attachment-container">
                                    <div class="input-group">
                                        <input type="file" name="file" class="form-control">

                                        <span class="input-group-text fs-14" >
                                            {{translate('Upload')}}
                                        </span>
                                    </div>

                                </div>
                            </div>
                            <button class="reply-submit">
                                <i class="fa-solid fa-reply"></i>
                                {{translate("Reply")}}
                            </button>

                        </form>
                    @endif

                    <div class="replay-card-bottom">
                        @foreach($ticket->messages as $meg)
                            @if(!$meg->admin_id)
                                <div class="replay-card-bottom-item">
                                    <div class="bottom-item-left">
                                        <h5 class="comment-user-name">{{$ticket->name}}</h5>
                                        <a href="javascript:void(0)" class="comment-user-nick">{{translate('@Customer')}}</a>
                                        <a href="{{route('user.support.message.delete', encrypt($meg->id))}}" class="delete-message-btn badge-soft-danger"><i class="fa-solid fa-trash-can"></i>{{translate('Delete')}}</a>
                                    </div>
                                    <div class="bottom-item-right">
                                        <span class="comment-time">
                                            {{ get_date_time($meg->created_at) }}
                                        </span>
                                        <p class="comment-message">
                                            {{($meg->message)}}
                                        </p>
                                        @if(count($meg->supportfiles) > 0)
                                            @foreach($meg->supportfiles as $file)
                                                <a href="{{route('user.ticket.file.download',encrypt($file->id))}}" class="comment-attchment badge-soft-warning"><i class="fa-solid fa-file"></i> {{translate('Attachment')}}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="replay-card-bottom-item">
                                    <div class="bottom-item-left">
                                        <h5 class="comment-user-name">{{translate('Admin Reply')}}</h5>
                                        <a href="javascript:void(0)" class="comment-user-nick">{{translate('Admin')}}</a>
                                    </div>
                                    <div class="bottom-item-right">
                                        <span class="comment-time">
                                        {{ get_date_time($meg->created_at) }}
                                        </span>
                                        <p class="comment-message">{{($meg->message)}}</p>

                                        <div class="d-flex align-items-center">
                                            @if(count( $meg->supportfiles)> 0)
                                                @foreach($meg->supportfiles as $file)
                                                    <a href="{{route('seller.ticket.file.download',encrypt($file->id))}}" class="comment-attchment badge-soft-warning fs-12"><i class="fa-solid fa-file"></i> {{translate('Attachment')}}</a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
