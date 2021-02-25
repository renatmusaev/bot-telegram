<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyboard extends Model
{
    public function parent()
    {
        return $this->belongsTo('App\Keyboard', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Keyboard', 'parent_id');
    }
    
    public function messages()
    {
        return $this->hasMany('App\Message');
    }
}
