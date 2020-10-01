<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;
    protected $fillable=['title','time','description'];

    public function users(){
        return $this->belongsToMany(User::class);
    }
}
