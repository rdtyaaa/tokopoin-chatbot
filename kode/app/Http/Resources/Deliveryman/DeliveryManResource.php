<?php

namespace App\Http\Resources\Deliveryman;

use App\Enums\StatusEnum;
use App\Http\Resources\CountryResource;
use App\Http\Resources\DeliverymanConversationResource;
use App\Http\Resources\DeliveryManRatingCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $currentTime = now();
        $data = [
            
            'id'                => $this->id,
            "fcm_token"         => $this->fcm_token,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'username'          => $this->username ,
            'referral_code'     => $this->referral_code ,
            'phone'             => $this->phone,
            'point'             => (int)$this->point,
            'phone_code'        => $this->phone_code,
            'registered_at'        => diff_for_humans($this->created_at),
            'country_id'        => $this->country_id,
            'balance'           => (double)api_short_amount($this->balance),
            'order_balance'           => (double)api_short_amount($this->order_balance),
            'is_banned'         => $this->status == StatusEnum::false->status() ? true : false,
            'image'             => show_image(file_path()['profile']['delivery_man']['path']."/".$this->image,file_path()['profile']['delivery_man']['size']),  
            'address'             => $this->address,
            'is_kyc_verified'           => $this->is_kyc_verified == 1,
            'is_online_status_active'   => $this->is_online == 1,
            'enable_push_notification'  => $this->enable_push_notification == 1,
            'last_online_time'          => $this->last_login_time  ? diff_for_humans($this->last_login_time) : null,
            'is_online'                 => $this->last_login_time  ? 
                                                          $this->last_login_time->diffInMinutes($currentTime) <= 2 
                                                          : false
        ];


        if($this->relationLoaded('refferedBy') && @$this->refferedBy ){
            $data['reffered_by'] = new DeliveryManResource(@$this->refferedBy);
        }
        
        if($this->relationLoaded('latestConversation') && @$this->latestConversation ){
            $data['latest_conversation'] = new DeliverymanConversationResource(@$this->latestConversation);
        }

        if($this->distance_in_words)   $data['distance_in_words'] = $this->distance_in_words;
        if($this->distance)   $data['distance'] = $this->distance;


        if($this->latest_deliveryman_message)  $data['latest_deliveryman_message'] = new DeliveryManChatResource(@$this->latest_deliveryman_message);


        if($this->relationLoaded('ratings') && @$this->ratings ){
            $data['reviews'] = new DeliveryManRatingCollection(@$this->ratings()->paginate(paginate_number()));
            $data['avg_ratings'] = @$this->ratings->avg('rating')?? 0;
        }
        return $data;
    }
}
