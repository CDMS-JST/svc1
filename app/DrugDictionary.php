<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrugDictionary extends Model
{
    protected $fillable = [
        'code_yk', 'code_hot7', 'code_hot9', 'name_notified', 'unit', 'company', 'em_rank', 'em_category'
    ];
}
