@extends('admin.layouts.app')

@push('style-push')
    <style>


        .table--wrapper{
            overflow-x: auto;
            max-width: 100%;
        }

        table{
            width: 100%;
        }

        td{
            min-width: 160px;
            padding: 8px;
        }

    </style>
@endpush

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate($title) }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                {{ translate('Home') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate($title) }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{ translate($title) }}
                        </h5>
                    </div>
                </div>

                <div class="card-body">


                    <div class="p-3">
                        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">
                
                            @csrf

                            <div class="row g-4 mb-4">
                    
                                <div class="col-xl-6">

                                    <label for="delivery_man_module" class="form-label">
                                        {{translate('Deliveryman module')}} <span class="text-danger" >*</span>

                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled, the Delivery Man module will be activated.')}}"></i>

                                    </label>

                                    <select name="site_settings[delivery_man_module]" id="delivery_man_module"  class="form-select">
                                         <option {{site_settings('delivery_man_module' ,App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('delivery_man_module', App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                                <div class="col-xl-6">

                                    <label for="chat_with_customer" class="form-label">
                                        {{translate('Chat with customer')}} <span class="text-danger" >*</span>

                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled, chat with customer feature will be activated.')}}"></i>

                                    </label>

                                    <select name="site_settings[chat_with_customer]" id="chat_with_customer"  class="form-select">
                                         <option {{site_settings('chat_with_customer' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('chat_with_customer' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                                <div class="col-xl-6">

                                    <label for="chat_with_deliveryman" class="form-label">
                                        {{translate('Chat with deliveryman')}} <span class="text-danger" >*</span>

                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled, chat with other deliveryman feature will be activated.')}}"></i>

                                    </label>

                                    <select name="site_settings[chat_with_deliveryman]" id="chat_with_deliveryman"  class="form-select">
                                         <option {{site_settings('chat_with_deliveryman' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('chat_with_deliveryman' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                                <div class="col-xl-6">

                                    <label for="order_assign" class="form-label">
                                        {{translate('Deliveryman order assign')}} <span class="text-danger" >*</span>

                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled, the Delivery Man will have the ability to assign orders to other delivery personnel.')}}"></i>

                                    </label>

                                    <select name="site_settings[order_assign]" id="order_assign"  class="form-select">
                                         <option {{site_settings('order_assign' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('order_assign' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                                
                               


                                <div class="col-xl-6">

                                    <label for="deliveryman_registration" class="form-label">
                                        {{translate('Deliveryman registration')}} <span class="text-danger" >*</span>
                                  
                                    </label>

                                    <select name="site_settings[deliveryman_registration]" id="deliveryman_registration"  class="form-select">
                                         <option {{site_settings('deliveryman_registration' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('deliveryman_registration' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>



                                <div class="col-xl-6">

                                    <label for="deliveryman_kyc_verification" class="form-label">
                                        {{translate('KYC Verification')}} <span class="text-danger" >*</span>
                                    </label>

                                    <select name="site_settings[deliveryman_kyc_verification]" id="deliveryman_kyc_verification"  class="form-select">
                                         <option {{site_settings('deliveryman_kyc_verification' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('deliveryman_kyc_verification' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                               

                                <div class="col-xl-6">

                                    <label for="deliveryman_assign_cancel" class="form-label">
                                        {{translate('Deliveryman decline assign request')}} <span class="text-danger" >*</span>
                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled, the Delivery Man can assign order to other deliverymen')}}"></i>
                                    </label>

                                    <select name="site_settings[deliveryman_assign_cancel]" id="deliveryman_assign_cancel"  class="form-select">
                                         <option {{site_settings('deliveryman_assign_cancel' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('deliveryman_assign_cancel' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>



                                <div class="col-xl-6">

                                    <label for="order_verification" class="form-label">
                                        {{translate('Order verification')}} <span class="text-danger" >*</span>
                                        <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If this option is enabled,order need to be verified by order verification code')}}"></i>
                                    </label>

                                    <select name="site_settings[order_verification]" id="order_verification"  class="form-select">
                                         <option {{site_settings('order_verification' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('order_verification' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>



                                <div class="col-xl-6">

                                    <label for="deliveryman_referral_system" class="form-label">
                                        {{translate('Referral system')}} <span class="text-danger" >*</span>
                                      
                                    </label>

                                    <select name="site_settings[deliveryman_referral_system]" id="deliveryman_referral_system"  class="form-select">
                                         <option {{site_settings('deliveryman_referral_system' ,App\Enums\StatusEnum::false->status() ) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                                  {{ translate('Active') }}
                                        </option>
                                         <option {{site_settings('deliveryman_referral_system' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                    </select>
                             
                                </div>


                                <div class="col-xl-6">

                                    <label for="deliveryman_referral_reward_point" class="form-label">
                                        {{translate('Referral reward point')}} <span class="text-danger" >*</span>
                                      
                                    </label>

                                    <input class="form-control" type="number" name="site_settings[deliveryman_referral_reward_point]" id="deliveryman_referral_reward_point" value="{{site_settings('deliveryman_referral_reward_point',0) }}">


                                </div>


                            </div>
                
                            <div class="text-start">
                                <button type="submit"
                                    class="btn btn-success waves ripple-light"
                                    id="add-btn">
                                    {{translate('Submit')}}
                                </button>
                            </div>
                
                        </form>
                    </div>


                </div>
            </div>


            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{ translate('Reward point system') }}
                        </h5>
                    </div>
                </div>

                @php
                        $rewardPointConfigurations = !is_array(site_settings('deliveryman_reward_point_configuration',[])) 
                                                            ? json_decode(site_settings('deliveryman_reward_point_configuration',[]),true) 
                                                            : [];
            
            
            
                        $rewardPointConfigurations  = collect($rewardPointConfigurations);




                        $rewardAmountConfigurations = !is_array(site_settings('deliveryman_reward_amount_configuration',[])) 
                                                            ? json_decode(site_settings('deliveryman_reward_amount_configuration',[]),true) 
                                                            : [];
            
            
            
                        $rewardAmountConfigurations  = collect($rewardAmountConfigurations);


                        
            
            
                @endphp

                <div class="card-body">

                    <div class="p-3">
                        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">
                
                            @csrf

                            <div class="row g-4 mb-4">

                                <div class="col-xl-6">
                                    <div>
                                        <label for="deliveryman_club_point_system" class="form-label">
                                            {{translate('Reward point system')}}
                                            <span class="text-danger" >* </span>
                                        </label>

                                        <select name="site_settings[deliveryman_club_point_system]" id="deliveryman_club_point_system"  class="form-select">
                                            <option {{site_settings('deliveryman_club_point_system' ,0) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                            {{translate('Active')}}
                                        </option>
                                            <option {{site_settings('deliveryman_club_point_system',0) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                            {{translate('Inactive')}}
                                        </option>
                                      </select>

                                    </div>
                                </div>

                               
               
                                <div class="col-6">
                                    <div>
                                        <label for="deliveryman_default_reward_point" class="form-label">{{translate('Default reward point amount')}} <span  class="text-danger">*</span>
                                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Set default reward point that deliveryman will get for based on order amount')}}"></i>
                                        </label>


                                        <input type="number" step="1" min="0" class="form-control" id="deliveryman_default_reward_point" name="site_settings[deliveryman_default_reward_point]" value="{{site_settings('deliveryman_default_reward_point',0)}}" placeholder="{{translate('Enter amount')}}">
                                    </div>
                                </div>



                              

                                <div class="col-lg-12 shipping-type">
                
                                    <div class="card bg-light">

                                        <div class="card-header border-bottom-dashed bg-light">

                                            <div class="row g-4 align-items-center">
                                                <div class="col-sm">
                                                    <div>
                                                        <h5 class="card-title mb-0">
                                                            {{translate('Order amount based reward point')}}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="tab-content text-muted">
                                                <div  id="product-wise-shipping">

                                                    <div class="text-start">
                                                        <a href="javascript:void(0)" class="btn btn-sm text-end btn-success add-price-btn  waves ripple-light"><i
                                                            class="ri-add-line align-bottom me-1"></i>
                                                        {{translate('Add New')}}
                                                    </a>
                                                    </div>

                                                    <div class="table--wrapper">

                                                        <table class="mt-4">
                                                            <thead>
                                                                <th></th>
                                                            </thead>
                                                            <tbody  class="add-price-row">


                                                                @if($rewardPointConfigurations->count() > 0)

                                                                    @foreach ($rewardPointConfigurations as $rewardPointConfiguration )

                                                                        <tr>
                                                                            <td>
                                                                                <p class="mb-0"> {{translate('Applicable if order amount is greter than')}}</p>
                                                                                <div class="input-group ">
                                                                                    <span class="input-group-text">
                                                                                        {{default_currency()->symbol}}
                                                                                    </span>
                                                                                    <input placeholder="{{translate('Enter order amount')}}" type="number" name="delivery_order_wise[greater_than][]" value="{{ Arr::get($rewardPointConfiguration ,'min_amount',0 )}}" class="form-control">
                                                                                </div>
                                                                            </td>

                                                                            <td>
                                                                                <p class="mb-0"> {{translate('Applicable if price is less than or equal')}}</p>
                                                                                <div class="input-group ">
                                                                                    <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                                    <input placeholder="{{translate('Enter order amount')}}" type="number" name="delivery_order_wise[less_than_eq][]" value="{{ Arr::get($rewardPointConfiguration ,'less_than_eq',0 )}}" class="form-control">
                                                                                </div>
                                                                            </td>

                                                                            <td>
                                                                                <p class="mb-0">
                                                                                    {{translate('Point')}}
                                                                                </p>

                                                                                <input step="1" min="0" placeholder="{{translate('Enter Point')}}"  name="delivery_order_wise[point][]" value="{{ Arr::get($rewardPointConfiguration ,'point',0 )}}"  type="number" class="form-control">
                                                                            </td>

                                                                            <td>
                                                                                <button type="button" class="btn btn-sm btn-danger delete-row-btn">
                                                                                     {{translate('Delete')}}
                                                                                </button>
                                                                            </td>


                                                                        </tr>
                                                                        
                                                                    @endforeach

                                                                @endif
                                                        
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            

                                            </div>
                                        </div>
                                        
                                    </div>


                                </div>





                                <div class="col-lg-12 point-based-reward">
                
                                    <div class="card bg-light">

                                        <div class="card-header border-bottom-dashed bg-light">

                                            <div class="row g-4 align-items-center">
                                                <div class="col-sm">
                                                    <div>
                                                        <h5 class="card-title mb-0">
                                                            {{translate('Point based reward')}}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="tab-content text-muted">
                                                <div  id="product-wise-shipping">

                                                    <div class="text-start">
                                                        <a href="javascript:void(0)" class="btn btn-sm text-end btn-success add-reward-btn  waves ripple-light"><i
                                                            class="ri-add-line align-bottom me-1"></i>
                                                        {{translate('Add New')}}
                                                    </a>
                                                    </div>

                                                    <div class="table--wrapper">

                                                        <table class="mt-4">
                                                            <thead>
                                                                <th></th>
                                                            </thead>
                                                            <tbody  class="add-reward-row">

                                                                @if($rewardAmountConfigurations->count() > 0)

                                                                    @foreach ($rewardAmountConfigurations as $rewardAmountConfiguration )

                                                                    

                                                                        <tr>

                                                                            <td>
                                                                                <p class="mb-0"> {{translate('Reward name')}}</p>
                                                                               
                                                                                <input placeholder="{{translate('Enter name')}}" type="text" name="delivery_reward_point[name][]" value="{{ Arr::get($rewardAmountConfiguration ,'name' )}}" class="form-control">
                                                                            </td>
                                                                            <td>
                                                                                <p class="mb-0"> {{translate('Reward point is greter than')}}</p>
                                                                               

                                                                                <input placeholder="{{translate('Enter number')}}" type="number" name="delivery_reward_point[greater_than][]" value="{{Arr::get($rewardAmountConfiguration,'min_amount',0 )}}" class="form-control">
                                                                            </td>

                                                                            <td>
                                                                                <p class="mb-0"> {{translate('Reward point is less than or equal')}}</p>
                                                                                <input placeholder="{{translate('Enter number')}}" type="number" name="delivery_reward_point[less_than_eq][]" value="{{ Arr::get($rewardAmountConfiguration ,'less_than_eq',0 )}}" class="form-control">
                                                                            </td>

                                                                            <td>
                                                                                <p class="mb-0">
                                                                                    {{translate('Reward amount')}}
                                                                                </p>
                                                                                <div class="input-group ">
                                                                                    <span class="input-group-text">
                                                                                        {{default_currency()->symbol}}
                                                                                    </span>
                                                                                      <input step="1" min="0" placeholder="{{translate('Enter amount')}}"  name="delivery_reward_point[amount][]" value="{{ Arr::get($rewardAmountConfiguration ,'amount',0 )}}"  type="number" class="form-control">
                                                                                    
                                                                                </div>
                                                                            </td>

                                                                            <td>
                                                                                <button type="button" class="btn btn-sm btn-danger delete-row-btn">
                                                                                     {{translate('Delete')}}
                                                                                </button>
                                                                            </td>


                                                                        </tr>
                                                                        
                                                                    @endforeach

                                                                @endif
                                                        
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            

                                            </div>
                                        </div>
                                        
                                    </div>


                                </div>

                            </div>
                
                            <div class="text-start">
                                <button type="submit"
                                    class="btn btn-success waves ripple-light"
                                    id="add-btn">
                                    {{translate('Submit')}}
                                </button>
                            </div>
                
                        </form>
                    </div>

                </div>
            </div>



            <div class="card">
            
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('FAQ')}}
                                </h5>
                            </div>
                        </div>
    
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="javascript:void(0)" class=" add-more btn btn-success btn-sm add-btn waves ripple-light"
                                ><i class="ri-add-line align-bottom me-1"></i>
                                        {{translate('Add More')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">


                    <div class="p-3">
                        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">
                
                            @csrf

                                 @php
            
                                    $faqs  = site_settings('delivery_faq',null) 
                                                   ? json_decode(site_settings('delivery_faq',null),true) 
                                                   : [];

                    
                                @endphp

                            <div class="row g-4 mb-4" id="appendChild">
                    
                                @foreach($faqs as $key => $faq)
                                    <div class="col-xl-6 options" >
                                        <div class="border rounded">
                                                <div class="card-header border-bottom-dashed px-3 py-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <h5 class="mb-0 fs-14 key">
                                                            {{ translate("FAQ")}}- {{$loop->iteration}}
                                                        </h5>
        
                                                        <div class="d-flex align-items-center gap-2">
                                                                <a href="javascript:void(0)" class="link-danger delete-option">
                                                                    <i class="fs-20 ri-delete-bin-6-line"></i>
                                                                </a>
                                                        </div>
        
                                                    </div>
                                                </div>
        
                                                <div class="p-3 frontend-scrollbar" data-simplebar="init">
                                                    <div class="row g-4">
                                                        <div class="col-xxl-12">
                                                            <label for="{{$key}}-question" class="form-label">
                                                                    {{translate("Question")}}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input id="{{$key}}-question" required placeholder ="{{translate('Enter question')}}"  type="text" name="site_settings[delivery_faq][{{$key}}][question]" value="{{$faq['question']}}" class="form-control">
                                                        </div>
        
                                                 
        
                                                        <div class="col-12">
                                                            <label  for="{{$key}}-answer" class="form-label">
                                                                    {{translate('Answer')}}
                                                                <span class="text-danger">*</span>
                                                            </label>
        
                                                            <textarea required  name="site_settings[delivery_faq][{{$key}}][answer]"  placeholder="{{translate('Type Here')}}" class="form-control" id="{{$key}}-answer" cols="4" rows="4">{{$faq['answer']}}</textarea>
        
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                @endforeach


                            
                            </div>
                
                            <div class="text-start">
                                <button type="submit"
                                    class="btn btn-success waves ripple-light"
                                    id="add-btn">
                                    {{translate('Submit')}}
                                </button>
                            </div>
                
                        </form>
                    </div>


                </div>
            </div>




        </div>
    </div>



@endsection

@push('script-include')



    <script>


        $(document).on('submit','.settingsForm',function(e){
        
            e.preventDefault();
            var data =   new FormData(this)
            var route = $(this).attr('data-route')
            var submitButton = $(e.originalEvent.submitter);
            $.ajax({
            method:'post',
            url: route,
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: function() {
                    submitButton.find(".note-btn-spinner").remove();

                    submitButton.append(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                            <span class="visually-hidden"></span>
                        </div>`);
            },
            success: function(response){
                var className = 'success';
                if(!response.status){
                    className = 'danger';
                }
                toaster( response.message,className)

                if(response.status && response.preview_html){
                    $('.wp-preview-section').html(response.preview_html)
                }
                
            },
            error: function (error){
                if(error && error.responseJSON){
                    if(error.responseJSON.errors){
                        for (let i in error.responseJSON.errors) {
                            toaster(error.responseJSON.errors[i][0],'danger')
                        }
                    }
                    else{
                        if((error.responseJSON.message)){
                            toaster(error.responseJSON.message,'danger')
                        }
                        else{
                            toaster( error.responseJSON.error,'danger')
                        }
                    }
                }
                else{
                    toaster(error.message,'danger')
                }
            },
            complete: function() {
                submitButton.find(".note-btn-spinner").remove();
            },
            })

        });



        var i = 0;
        $(document).on('click', '.add-more',function(e){

           var keyV = $('.key:last').html()
           if(keyV){
             i = parseInt(keyV.match(/\d+/)[0]);
           }
           i++;
            $('#appendChild').append(
                    `
                    <div class="col-lg-6 options" >
                        <div class="border rounded">
                                <div class="card-header border-bottom-dashed px-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="mb-0 fs-14 key">
                                           FAQ-${i}
                                        </h5>

                                        <div class="d-flex align-items-center gap-2">
                                                <a href="javascript:void(0)" class="link-danger delete-option">
                                                   <i class="fs-20 ri-delete-bin-6-line"></i>
                                                </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 frontend-scrollbar" data-simplebar="init">
                                    <div class="row g-4">

                                        <div class="col-md-12">
                                            <label for="question" class="form-label">
                                                    {{translate("Question")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input id="question" required placeholder ="{{translate('Enter question')}}" type="text" name="site_settings[delivery_faq][delivery_faq_${i}][question]" class="form-control">
                                        </div>
                                       
                                        <div class="col-md-12">
                                            <label for="answer" class="form-label">
                                                    {{translate('Answer')}}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea id="answer" required  name="site_settings[delivery_faq][delivery_faq_${i}][answer]"  placeholder="{{translate('Type Here')}}" class="form-control"  cols="4" rows="4"></textarea>

                                        </div>

                                    </div>
                                </div>
                        </div>
                    </div>

                    `
            )
            e.preventDefault()
        })

        //delete element


        $(document).on('click', '.delete-option',function(e){
            i--
            $(this).closest('.options').remove();
        });




        $(document).on('click','.add-price-btn',function() {
        var isFilled = true;
        $('.add-price-row input[type="number"]').each(function(man,$v) {


            if ($(this).val().trim() === '') {
                isFilled = false;
            }
        });

        if (!isFilled) {
            alert('Please fill all the fields before adding a new row.');
            return;
        }

        // Get the value of the last 'less than or equal' input and set the new 'greater than' input value accordingly
        var lastLessThanEqValue = parseInt($('input[name="delivery_order_wise[less_than_eq][]"]').last().val());
        var newGreaterThanValue = isNaN(lastLessThanEqValue) ? 0 : lastLessThanEqValue + 1;

        var newRow = `
            <tr>
                <td>
                    <p class="mb-0">{{translate('Applicable if order amount is greater than')}}</p>
                    <div class="input-group">
                        <span class="input-group-text">{{default_currency()->symbol}}</span>
                        <input placeholder="{{translate('Enter order amount')}}" type="number" name="delivery_order_wise[greater_than][]" class="form-control" value="${newGreaterThanValue}">
                    </div>
                </td>
                <td>
                    <p class="mb-0">{{translate('Applicable if  order amount is less than or equal')}}</p>
                    <div class="input-group">
                        <span class="input-group-text">{{default_currency()->symbol}}</span>
                        <input placeholder="{{translate('Enter order amount')}}" type="number" name="delivery_order_wise[less_than_eq][]" class="form-control">
                    </div>
                </td>

                    <td>
                    <p class="mb-0">
                        {{translate('Point')}}
                    </p>

                    <input step="1" min="0" value='0' placeholder="{{translate('Enter Point')}}"  name="delivery_order_wise[point][]"  type="number" class="form-control">
                </td>

    
                <td>
                    <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                </td>
            </tr>
        `;

        $('.add-price-row').append(newRow);
        });

        $(document).on('click', '.delete-row-btn', function() {
            $(this).closest('tr').remove();
        });


        // reward point configuration


        $(document).on('click','.add-reward-btn',function() {
        var isFilled = true;
        $('.add-reward-row input').each(function(man,$v) {


            if ($(this).val().trim() === '') {
                isFilled = false;
            }
        });

        if (!isFilled) {
            alert('Please fill all the fields before adding a new row.');
            return;
        }

        // Get the value of the last 'less than or equal' input and set the new 'greater than' input value accordingly
        var lastLessThanEqValue = parseInt($('input[name="delivery_reward_point[less_than_eq][]"]').last().val());
        var newGreaterThanValue = isNaN(lastLessThanEqValue) ? 0 : lastLessThanEqValue + 1;

        var newRow = `
            <tr>

                 <td>
                    <p class="mb-0"> {{translate('Reward name')}}</p>
                    
                    <input placeholder="{{translate('Enter name')}}" type="text" name="delivery_reward_point[name][]" value="" class="form-control">
                </td>

                <td>
                     <p class="mb-0"> {{translate('reward point is greter than')}}</p>

                    
                     <input placeholder="{{translate('Enter number')}}" type="number" name="delivery_reward_point[greater_than][]" value="${newGreaterThanValue}" class="form-control">
                </td>
                <td>
                     <p class="mb-0"> {{translate('reward point is less than or equal')}}</p>


                    <input placeholder="{{translate('Enter number')}}" type="number" name="delivery_reward_point[less_than_eq][]" value="" class="form-control">

                </td>

                <td>
                    <p class="mb-0">
                        {{translate('Reward amount')}}
                    </p>
                    <div class="input-group ">
                        <span class="input-group-text">
                            {{default_currency()->symbol}}
                        </span>
                            <input step="1" min="0" placeholder="{{translate('Enter amount')}}"  name="delivery_reward_point[amount][]" value="0"  type="number" class="form-control">
                        
                    </div>
                </td>

    
                <td>
                    <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                </td>
            </tr>
        `;

        $('.add-reward-row').append(newRow);
        });



    </script>
@endpush
