<div class="js-cookie-consent cookie-consent  cookies">
    <h5>{{@frontend_section_data($cookie->value,'heading')}}</h5>
        <p>
            <span class="cookie-icon"><i class="fa-solid fa-cookie-bite"></i></span> {{@frontend_section_data($cookie->value,'sub_heading')}}
        </p>
        
        <div class="cookies-action">
            <button class="js-cookie-consent-agree cookie-consent__agree cursor-pointer accept-cookie cookie-control">
                {{translate("Accept & Continue")}}
            </button>
        </div>
</div>

