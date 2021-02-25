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
    
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }
    
    public function links()
    {
        return $this->hasMany('App\Link');
    }
}
