<footer class="footer">
    <div class="Container">
        <div class="footer-top">
             <div class="footer-top-items">
                    <div class="footer-top-item">
                        <div class="footer-logo">
                            <a href="{{route('home')}}">
                                <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="site_logo.jpg">
                            </a>
                        </div>
                        <p class="footer-descrition">{{@frontend_section_data($footer_text->value,'heading')}}</p>

                        <div class="footer-middle">
                            <div class="footer-contact">
                                <div class="footer-contact-optino">
                                    <div class="footer-contact-icon"><svg  version="1.1"   width="18" height="18" x="0" y="0" viewBox="0 0 473.806 473.806"   xml:space="preserve" ><g><path d="M374.456 293.506c-9.7-10.1-21.4-15.5-33.8-15.5-12.3 0-24.1 5.3-34.2 15.4l-31.6 31.5c-2.6-1.4-5.2-2.7-7.7-4-3.6-1.8-7-3.5-9.9-5.3-29.6-18.8-56.5-43.3-82.3-75-12.5-15.8-20.9-29.1-27-42.6 8.2-7.5 15.8-15.3 23.2-22.8 2.8-2.8 5.6-5.7 8.4-8.5 21-21 21-48.2 0-69.2l-27.3-27.3c-3.1-3.1-6.3-6.3-9.3-9.5-6-6.2-12.3-12.6-18.8-18.6-9.7-9.6-21.3-14.7-33.5-14.7s-24 5.1-34 14.7l-.2.2-34 34.3c-12.8 12.8-20.1 28.4-21.7 46.5-2.4 29.2 6.2 56.4 12.8 74.2 16.2 43.7 40.4 84.2 76.5 127.6 43.8 52.3 96.5 93.6 156.7 122.7 23 10.9 53.7 23.8 88 26 2.1.1 4.3.2 6.3.2 23.1 0 42.5-8.3 57.7-24.8.1-.2.3-.3.4-.5 5.2-6.3 11.2-12 17.5-18.1 4.3-4.1 8.7-8.4 13-12.9 9.9-10.3 15.1-22.3 15.1-34.6 0-12.4-5.3-24.3-15.4-34.3l-54.9-55.1zm35.8 105.3c-.1 0-.1.1 0 0-3.9 4.2-7.9 8-12.2 12.2-6.5 6.2-13.1 12.7-19.3 20-10.1 10.8-22 15.9-37.6 15.9-1.5 0-3.1 0-4.6-.1-29.7-1.9-57.3-13.5-78-23.4-56.6-27.4-106.3-66.3-147.6-115.6-34.1-41.1-56.9-79.1-72-119.9-9.3-24.9-12.7-44.3-11.2-62.6 1-11.7 5.5-21.4 13.8-29.7l34.1-34.1c4.9-4.6 10.1-7.1 15.2-7.1 6.3 0 11.4 3.8 14.6 7l.3.3c6.1 5.7 11.9 11.6 18 17.9 3.1 3.2 6.3 6.4 9.5 9.7l27.3 27.3c10.6 10.6 10.6 20.4 0 31-2.9 2.9-5.7 5.8-8.6 8.6-8.4 8.6-16.4 16.6-25.1 24.4-.2.2-.4.3-.5.5-8.6 8.6-7 17-5.2 22.7l.3.9c7.1 17.2 17.1 33.4 32.3 52.7l.1.1c27.6 34 56.7 60.5 88.8 80.8 4.1 2.6 8.3 4.7 12.3 6.7 3.6 1.8 7 3.5 9.9 5.3.4.2.8.5 1.2.7 3.4 1.7 6.6 2.5 9.9 2.5 8.3 0 13.5-5.2 15.2-6.9l34.2-34.2c3.4-3.4 8.8-7.5 15.1-7.5 6.2 0 11.3 3.9 14.4 7.3l.2.2 55.1 55.1c10.3 10.2 10.3 20.7.1 31.3zM256.056 112.706c26.2 4.4 50 16.8 69 35.8s31.3 42.8 35.8 69c1.1 6.6 6.8 11.2 13.3 11.2.8 0 1.5-.1 2.3-.2 7.4-1.2 12.3-8.2 11.1-15.6-5.4-31.7-20.4-60.6-43.3-83.5s-51.8-37.9-83.5-43.3c-7.4-1.2-14.3 3.7-15.6 11s3.5 14.4 10.9 15.6zM473.256 209.006c-8.9-52.2-33.5-99.7-71.3-137.5s-85.3-62.4-137.5-71.3c-7.3-1.3-14.2 3.7-15.5 11-1.2 7.4 3.7 14.3 11.1 15.6 46.6 7.9 89.1 30 122.9 63.7 33.8 33.8 55.8 76.3 63.7 122.9 1.1 6.6 6.8 11.2 13.3 11.2.8 0 1.5-.1 2.3-.2 7.3-1.1 12.3-8.1 11-15.4z"  data-original="#000000" ></path></g></svg>

                                    </div>
                                    <a href="tel:{{site_settings('phone')}}" class="footer-contact-link">{{site_settings('phone')}}</a>

                                </div>

                                <div class="footer-contact-optino">
                                    <div class="footer-contact-icon">
                                        <svg  version="1.1"   width="18" height="18" x="0" y="0" viewBox="0 0 512 512"   xml:space="preserve" ><g><path d="M467 76H45C20.137 76 0 96.262 0 121v270c0 24.885 20.285 45 45 45h422c24.655 0 45-20.03 45-45V121c0-24.694-20.057-45-45-45zm-6.302 30L287.82 277.967c-8.5 8.5-19.8 13.18-31.82 13.18s-23.32-4.681-31.848-13.208L51.302 106h409.396zM30 384.894V127.125L159.638 256.08 30 384.894zM51.321 406l129.587-128.763 22.059 21.943c14.166 14.166 33 21.967 53.033 21.967s38.867-7.801 53.005-21.939l22.087-21.971L460.679 406H51.321zM482 384.894 352.362 256.08 482 127.125v257.769z"  data-original="#000000" ></path></g></svg>

                                    </div>
                                    <a href="mailto:{{site_settings('mail_from')}}" class="footer-contact-link">{{site_settings('mail_from')}}</a>
                                </div>

                                <div class="footer-contact-optino">
                                    <div class="footer-contact-icon">
                                        <svg  version="1.1"   width="18" height="18" x="0" y="0" viewBox="0 0 682.667 682.667" xml:space="preserve" ><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z"  data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c-60 90-165 212-165 317 0 90.981 74.019 165 165 165s165-74.019 165-165C165 212 60 90 0 0Z" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(256 15)" fill="none"  stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" ></path><path d="M0 0c-41.353 0-75 33.647-75 75s33.647 75 75 75 75-33.647 75-75S41.353 0 0 0Z" style="stroke-width:30;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(256 257)" fill="none"  stroke-width="30" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" ></path></g></g></svg>
                                    </div>
                                     <a 
                                     href="https://maps.google.com/maps?q={{site_settings('latitude')}},{{site_settings('longitude')}}" target="_blank" class="footer-contact-link">{{site_settings('address')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-top-item">
                        <h4 class="footer-top-item-title">
                            {{translate("IMPORTANT LINKS")}}
                        </h4>
                        <ul class="footer-links">
                            @foreach($menus as $menu)
                                <li><a href="{{url($menu->url)}}">{{$menu->name}}</a></li>
                            @endforeach
                        </ul>

                    </div>

                    <div class="footer-top-item">
                        <h4 class="footer-top-item-title">
                            {{translate("QUICK LINKS")}}
                        </h4>
                        <ul class="footer-links">
                            @foreach($pageSetups as $page)
                                <li><a href="{{route('pages',[make_slug($page->name),$page->id])}}">{{$page->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="footer-top-item">
                        <div class="d-flex flex-column gap-4">
                            @if($apps_section->status == 1)
                             <div>
                                 <h4 class="footer-top-item-title">{{@frontend_section_data($apps_section->value,'heading')}}</h4>
                                 <div class="download-our-app">
                                     <p>{{@frontend_section_data($apps_section->value,'sub_heading')}}</p>
                                     <div class="apps">
                                         <a href="{{@frontend_section_data($apps_section->value,'play_store_link')}}" class="app-store">
                                             <div class="app-icon">
                                                 <i class="fa-brands fa-google-play"></i>
                                             </div>
                                             <div class="app-title">
                                                 <small>
                                                    {{translate("Download on the")}}
                                                 </small>
                                                 <span>
                                                    {{translate("Google Play")}}
                                                 </span>
                                             </div>
                                         </a>
                                         <a href="{{@frontend_section_data($apps_section->value,'apple_store_link')}}" class="app-store">
                                             <div class="app-icon">
                                                 <i class="fa-brands fa-apple"></i>
                                             </div>
                                             <div class="app-title">
                                                 <small>
                                                    {{translate("Download on the")}}
                                                 </small>
                                                 <span>
                                                    {{translate("Apple Store")}}
                                                 </span>
                                             </div>
                                         </a>
                                     </div>
                                 </div>
                             </div>
                            @endif

                            @if($social_icon->status == 1)

                                <div class="footer-social-content">
                                    <h6>{{@frontend_section_data($social_icon->value,'heading')}}</h6>
                                    <p>{{@frontend_section_data($social_icon->value,'sub_heading')}}</p>
                                    <ul class="footer-social">
                                       @php
                                            $social_data=json_decode($social_icon->value,true);
                                       @endphp
                                       @foreach(@$social_data as $key=>$social)
                                            @if($key=='facebook' || $key=='google' || $key=='linkedin' || $key=='whatsapp')
                                                <li>
                                                    <a  href="{{$social['url']}}">
                                                        @php echo $social['icon'] @endphp
                                                    </a>
                                                </li>
                                            @endif
                                       @endforeach
                                    </ul>
                                </div>

                            @endif
                       </div>
                    </div>
             </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>                            {{site_settings('copyright_text')}}</p>
                <div class="payment-img">
                    <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($paymnet_image->value,'image'),@frontend_section_data($paymnet_image->value,'image','size'))}}" alt="payment.jpg">
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="quickView" tabindex="-1" data-bs-backdrop="static" aria-labelledby="quickView" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewLabel">
                    {{translate("Quick View")}}
                </h5>
                <button type="button" class="btn btn-light fs-14 modal-closer rounded-circle" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body quick-view-modal-body postion-relative">
            </div>
        </div>
    </div>
</div>
