<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id', 'id');
    }

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($blog) {
            $blog->uid = str_unique();
        });
    }

    public function scopeSearch($q)
    {
        return $q->when(request()->input('search'),function($q){
            $searchBy = '%'. request()->input('search').'%';
            return $q->where('post','like',$searchBy)
            ->orWhereHas('category',function($q) use($searchBy){
                $locale = session()->get('locale','en');
                return $q->where('name->'.$locale,'like',$searchBy); 
            });

            });
    }
}
