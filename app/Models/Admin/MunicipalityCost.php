<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MunicipalityCost extends Model
{
    protected $fillable = ['name', 'municipality', 'cost'];
}
