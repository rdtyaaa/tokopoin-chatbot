@extends('frontend.layouts.app')
@section('content')

<section class="payment-success pt-80 pb-80 bg--primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
               <div class="payment-wrapper">
                  <div class="text-center mb-5">
                        <div class="icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                <circle class="path circle" fill="none" stroke="#FF0303" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                <line class="path line" fill="none" stroke="#FF0303" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                <line class="path line" fill="none" stroke="#FF0303" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                            </svg> 
                        </div>
                        <div class="title">
                            <h4 class="failed--title">
                                {{translate('Payment Failed')}}
                            </h4>
                        </div>
                  </div>

                 <div class="payment-note">

                     <p>
                       <span>{{translate('Payment Failed:')}}</span> {{translate('Take immediate action to update payment information. Contact support for assistance. Failure to resolve may lead to service disruption or account limitations. Your prompt attention is appreciated to avoid any inconvenience')}}
                     </p>
                     
                 </div>
     
                <div class="buttons-group d-flex justify-content-center gap-4">
                    <a href="{{route('home')}}" class="dark--btn"><i class="las la-home me-3"></i>
                         {{translate("Back To Home")}}
                    </a>
                </div>
               </div>         
            </div>
        </div>
    </div>
</section>

@endsection