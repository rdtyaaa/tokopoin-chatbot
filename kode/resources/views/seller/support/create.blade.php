@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Support Ticket Create")}}
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('seller.ticket.index')}}">
                        {{translate('Tickets')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Crate")}}
                    </li>
                </ol>
            </div>

        </div>

		<div class="card mt-3">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Create Ticket')}}
                        </h5>
                    </div>
                </div>
            </div>

			<div class="card-body">

                <form action="{{route('seller.ticket.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <input type="text" value="{{old('subject')}}" name="subject" class="form-control" placeholder="{{translate('Enter subject')}}" required>
                        </div>

                        <div class="col-lg-6">
                            <select class="form-select" name="priority" required>
                                <option value="">{{translate('Select Priority')}}</option>
                                <option {{old('priority') == 1 ? 'selected' : ''}} value="1">{{translate('Low')}}</option>
                                <option {{old('priority') == 2 ? 'selected' : ''}} value="2">{{translate('Medium')}}</option>
                                <option {{old('priority') == 3 ? 'selected' : ''}} value="3">{{translate('High')}}</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <textarea class="form-control" rows="5" name="message" placeholder="{{translate('Enter message')}}" required>{{old('message')}}</textarea>
                        </div>

                        <div class="col-lg-10 col-md-9">
                            <input type="file" name="file[]" class="form-control">
                            <div class="text-danger pt-1">{{translate('Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')}}</div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <button type="button" class="btn btn-primary waves ripple-light w-100 addnewfile">{{translate('Add New')}}</button>
                        </div>

                        <div class="col-12">
                            <div class="addnewdata">
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
                        </div>
                    </div>
                </form>
			</div>
		</div>
    </div>
</div>
@endsection

@push('script-push')
<script>
    "use strict"

	$('.addnewfile').on('click', function () {
        var html = `

        <div class="row mb-2 newdata">
    		<div class="col-sm-10">
    			<input type="file" name="file[]" class="form-control" required>
			</div>

    		<div class="col-sm-2 text-right">
                <span class="input-group-btn">
                    <button class="btn btn-danger waves ripple-light removeBtn" type="button">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                </span>
            </div>
        </div>`;
        $('.addnewdata').append(html);
	    $(".removeBtn").on('click', function(){
	        $(this).closest('.newdata').remove();
	    });
    });
</script>
@endpush
