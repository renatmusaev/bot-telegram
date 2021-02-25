<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    public function keyboards()
    {
        return $this->hasMany('App\Keyboard');
    }
}
