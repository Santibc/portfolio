<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'url_path',
        'page_type',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    public function seo()
    {
        return $this->hasOne(Seo::class);
    }
}
