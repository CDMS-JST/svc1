<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $connection = 'mysql_2';
    
    protected $fillable = [
        'drug_id_qr', 'drug_id_yj9', 'drug_name_qr'
    ];

}
