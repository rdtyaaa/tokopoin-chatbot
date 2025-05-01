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
                            {{translate('FAQ')}}
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
                                    {{translate('Faq List')}}
                                </h5>
                            </div>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                  <button type="button" class="btn btn-success btn-sm add-btn waves ripple-light"
                                                 data-bs-toggle="modal" id="create-btn" data-bs-target="#addFaq"><i
                                    class="ri-add-line align-bottom me-1"></i>{{translate("Create
                                    Faqs")}}</button>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{route('admin.faq.index')}}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                        placeholder="{{translate('Search by category , name or question')}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <a href="{{route('admin.faq.index')}}" class="btn btn-danger w-100 waves ripple-light">
                                        <i class="ri-refresh-line me-1 align-bottom"></i>
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
                                        {{translate("Category Name")}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Question')}}
                                    </th>
                                    <th scope="col">
                                        {{translate("Status")}}
                                    </th>
                                    <th scope="col">
                                        {{translate("Action")}}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($faqs as $faq)
                                    <tr>
                                        <td class="fw-medium">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            {{ucfirst($faq->support_category)}}
                                        </td>
                                        <td>
                                            {{ \Illuminate\Support\Str::limit($faq->question, 20, $end='...') }}
                                        </td>
                                        <td>
                                            @if($faq->status == '1')
                                               <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                            @else
                                                <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="hstack justify-content-center gap-3">
                                                @if(permission_check('update_support'))
                                                   <a href="javascript:void(0)" title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"  data-id="{{$faq->id}}" data-category="{{$faq->support_category}}"    data-answer="{{$faq->answer}}"    data-question="{{$faq->question}}" data-status="{{$faq->status}}" class="edit-faq fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
                                                @endif
                                                @if(permission_check('delete_support'))
                                                    <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.faq.delete',$faq->id)}}" class="delete-item fs-18 link-danger">
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
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addFaq" tabindex="-1" aria-labelledby="addFaq" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{translate('Add New Faq')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close" ></button>
                </div>
                <form action="{{route('admin.faq.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="p-2">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{translate('Choose Categroy')}} <span class="text-danger">*</span></label>
                                <select class="form-select" name="support_category" id="status" required>
                                    <option {{old('support_category') == 'Information Center' ?'selected':'' }}  value="Information Center">{{translate('Information Center')}}</option>
                                    <option {{old('support_category') == 'Pricing & Plans' ?'selected':'' }}  value="Pricing & Plans">{{translate('Pricing & Plans')}}</option>
                                    <option {{old('support_category') == 'Sales And Question' ?'selected':'' }}  value="Sales And Question">{{translate('Sales And Question')}}</option>
                                    <option {{old('support_category') == 'Usage Guide' ?'selected':'' }}  value="Usage Guide">{{translate('Usage Guide')}}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="question" class="form-label">{{translate('Question')}} <span class="text-danger">*</span></label>
                                <textarea placeholder="{{translate('Enter question')}}" class="form-control" name="question" id="question" cols="30" rows="3" required>{{old('question')}}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="answer" class="form-label">{{translate('Answer')}}</label>
                                <textarea placeholder="{{translate('Enter answer')}}" class="form-control" name="answer" id="answer" cols="30" rows="6">{{old('answer')}}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="statusTwo" class="form-label">{{translate('Status')}}<span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="statusTwo" required>
                                    <option {{old('status') == 'Active' ?'selected':''}} value="1">{{translate('Active')}}</option>
                                    <option {{old('status') == 'DeActive' ?'selected':''}} value="0">{{translate('DeActive')}}</option>
                                </select>
                            </div>
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

    <div class="modal fade" id="updateFaq" tabindex="-1" aria-labelledby="updateFaq" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" >{{translate('Update FAQ')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close" id="close-modal"></button>
                </div>
                <form action="{{route('admin.faq.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div>
                            <div id="faq-update" class="p-2">


                            </div>
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
@push('script-push')
<script>
    (function($){

        "use strict";
        $('.edit-faq').on('click', function(){
            var modal = $('#updateFaq');
            const status = $(this).attr('data-status');
            const id = $(this).attr('data-id');
            const question = $(this).attr('data-question');
            const answer = $(this).attr('data-answer');
            const category = $(this).attr('data-category');
            $('#faq-update').html('');
            $('#faq-update').append(`
            <div class="mb-3">
                <input type="hidden" name="id" value='${id}'>
                <label for="ustatus" class="form-label">{{translate('Choose Categroy')}} <span class="text-danger">*</span></label>
                <select class="form-select" name="support_category" id="ustatus" required>
                    <option ${category == "Information Center" ? "selected": ""}     value="Information Center">{{translate('Information Center')}}</option>
                    <option ${category == "Pricing & Plans" ? "selected": ""}   value="Pricing & Plans">{{translate('Pricing & Plans')}}</option>
                    <option ${category == "Sales And Question" ? "selected": ""} value="Sales And Question">{{translate('Sales And Question')}}</option>
                    <option  ${category == "Usage Guide" ? "selected": ""}    value="Usage Guide">{{translate('Usage Guide')}}</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="uquestion" class="form-label">{{translate('Question')}} <span class="text-danger">*</span></label>
                <textarea placeholder="{{translate('Enter question')}}" class="form-control" name="question" id="uquestion" cols="30" rows="3" required>${question}</textarea>
           </div>

            <div class="mb-3">
                <label for="uanswer" class="form-label">{{translate('Answer')}} </label>
                <textarea placeholder="{{translate('Enter answer')}}" class="form-control" name="answer" id="uanswer" cols="30" rows="6" >${answer}</textarea>
            </div>

            <div class="mb-3">
                <label for="ustatus" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
                <select class="form-select" name="status" id="ustatus" required>
                    <option ${status == "1" ? "selected": ""}   value="1">{{translate('Active')}}</option>
                    <option  ${status == "0" ? "selected": ""}   value="0">{{translate('DeActive')}}</option>
                </select>
            </div>
            `);
            modal.modal('show');
        });
        $(".faqDelete").on("click", function(){
            var modal = $("#faqDelete");
            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush


