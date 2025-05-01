@php
  $authUser = auth_user('web');
  $flag=1;
@endphp

@if($authUser && in_array($authUser->email,$subscribers->toArray()))
    @php
       $flag=0;
    @endphp
@endif

@if($flag==1)
    <div class="modal newsLetterModal fade" id="newsletterModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="newsletterModal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content body-rounded">
                <div class="modal-body p-0 position-relative">
                    <div class="newsletter-modal">
                        <div class="newsletter-modal-right">
                            <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($news_latter->value,'image'),@frontend_section_data($news_latter->value,'image','size'))}}" alt="newsletter.jpg">
                        </div>

                        <div class="newsletter-modal-left">
                            <div class="news-content">
                                <span>{{@frontend_section_data($news_latter->value,'heading')}}</span>
                                <p>{{@frontend_section_data($news_latter->value,'sub_heading')}}</p>
                            </div>
                            <form action="{{ route('newslatter.subscribe') }}" method="post" class="news-form">
                                 @csrf
                                <div class="input-group-news">
                                  <input type="email" placeholder="{{translate('Put your email address')}}" name="email" class="newsletter-email">
                                  <span class="input-group-btn">
                                      <button type="submit" class="btn"><span>{{translate('Get News')}}</span></button>
                                    </span>
                                </div>
                                <div class="form-check mt-3 ms-2">
                                    <input class="form-check-input" type="checkbox" id="dont_show" name="dont_show" value="dont_show">
                                    <label class="form-check-label text-muted" for="dont_show">
                                        {{translate('Do not show this popup again')}}
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                    <span class="flotting-modal-closer"> <button type="button" class="btn btn-light fs-14 modal-closer rounded-circle ms-auto" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button></span>
                </div>
            </div>
        </div>
    </div>
@endif
