<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class BaseModel extends EloquentModel {
        /**
     * @param \DateTimeInterface|\Carbon\Carbon $date
     * @return string
     */
    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('Y-m-d\TH:i:s.uP');
    }
    
}