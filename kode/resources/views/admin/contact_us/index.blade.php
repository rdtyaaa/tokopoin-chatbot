@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />
@endpush
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
                        {{translate("Home")}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Contact Us")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-0">
				<div class="d-flex align-items-center">
					<h5 class="card-title mb-0 flex-grow-1">
						{{translate('Contact List')}}
					</h5>
				</div>
			</div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.contact.index')}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by name,email or subject')}}">
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
                                <a href="{{route('admin.contact.index')}}" class="btn btn-danger w-100 waves ripple-light"> <i
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
					<table class="table table-hover table-centered align-middle table-nowrap mb-0">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate("Name")}}
								</th>
								<th scope="col">
									{{translate("Email")}}
								</th>
								<th scope="col">
									{{translate("Subject")}}
								</th>

								<th scope="col">
									{{translate("Action")}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse($contacts as $contact)
								<tr>
									<td class="fw-medium">
									{{$loop->iteration}}
									</td>
									<td>
										{{$contact->name}}

									</td>
									<td>
										{{$contact->email}}
									</td>
									<td>
										{{ \Illuminate\Support\Str::limit($contact->subject, 20, $end='...') }}
									</td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('update_support'))
												<a  title="{{translate('Show')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0)"  data-messgae="{{$contact->message}}"    class="show-message fs-18 link-warning"><i class="ri-eye-line"></i></a>

												<a  title="{{translate('Send Mail')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0)"  data-id="{{$contact->id}}"    class="send-mail fs-18 link-success"><i class="ri-send-plane-line"></i></a>
											@endif

											@if(permission_check('delete_support'))
												<a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.contact.destory',$contact->id)}}" class="delete-item fs-18 link-danger">
													<i class="ri-delete-bin-line"></i></a>
											@endif

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
					{{$contacts->links()}}
				</div>
			</div>
		</div>
	</div>

</div>

<div class="modal fade" id="viewContact" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Message')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" ></button>
			</div>
			<div id="modalBody">
				<div class="modal-body">

					<div id="message">

					</div>
				</div>
				<div class="modal-footer">
					<div class="hstack gap-2 justify-content-end">
						<button type="button"
							class="btn btn-danger waves ripple-light"
							data-bs-dismiss="modal">

							 {{translate("Close")}}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="sendMail" tabindex="-1" aria-labelledby="sendMail" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Send Mail')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.contact.send.mail')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" id="id">
				<div class="modal-body">

					<div class="mb-3">
						<label for="subject" class="form-label">
							 {{translate('Subject')}} <span class="text-danger">*</span>
						</label>
						<input placeholder="{{translate('Enter subject')}}" type="text" name="subject"  id="subject" value="{{old('subject')}}" class="form-control">

					</div>

					<div class="text-editor-area">
						<label for="mail-composer" class="form-label">
							{{translate('Message')}} <span class="text-danger"  >*</span>
						</label>
						<textarea id="mail-composer" class=" form-control text-editor" name="message" rows="10" placeholder="{{translate('Enter message')}}" required="">{{old('message')}}</textarea>

						@if( $openAi->status == 1)
							<button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
								<span class="ai-icon btn-success waves ripple-light">
										<span class="spinner-border d-none" aria-hidden="true"></span>

										<i class="ri-robot-line"></i>
								</span>

								<span class="ai-text">
									{{translate('Generate With AI')}}
								</span>
							</button>
				     	@endif

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@include('admin.modal.delete_modal')

@endsection


@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush


@push('script-push')
<script>
    (function($){
        "use strict";

        $('.show-message').on('click', function(){
            var modal = $('#viewContact');
            const message = $(this).attr('data-messgae');
            $('#message').html(message)
            modal.modal('show');
        });

		$('.send-mail').on('click', function(){
            var modal = $('#sendMail');
            const id = $(this).attr('data-id');
            $('#id').val(id)
            modal.modal('show');
        });

    })(jQuery);
</script>
@endpush

