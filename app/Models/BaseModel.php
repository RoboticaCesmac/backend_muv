<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;
class BaseModel extends EloquentModel {

    protected function serializeDate(DateTimeInterface $date)
    {
        $date = Carbon::instance($date);
        return $date->setTimezone('America/Sao_Paulo')
                    ->format(DateTime::RFC3339_EXTENDED);
    }
    
}