<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementPosition extends Model
{
    protected $table = 'cms_advertisement_position';

    protected $fillable = ['ad_id', 'display_position'];

}
