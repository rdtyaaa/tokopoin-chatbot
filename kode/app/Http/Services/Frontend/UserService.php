<?php

namespace App\Http\Services\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\WishList;

class UserService extends Controller
{

  /** wishlist items */
  public function wishlistItems(){
    $user = auth_user('web');
    return  WishList::with(['product'=>function($q){
        return $q->with(['brand','stock','review']);
    }])->where('customer_id', $user->id)->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
  }
  /** cart items */
  public function carts(){
    $user = auth_user('web');
    return  Cart::with(['product'=>function($q){
        return $q->with(['brand','stock','review']);
    }])->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(site_settings('pagination_number',10));
  }

}