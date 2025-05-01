<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPasswordReset extends Model
{
    use HasFactory;

 	protected $fillable = [
 		'email',
 		'verify_code',
		'uid'
 	];

	 protected static function booted()
	 {
		 static::creating(function ($sellerPasswordReset) {
			 $sellerPasswordReset->uid = str_unique();
		 });
	 }
}
