<?php

namespace App\Models;

use DateTime;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel {

    protected function serializeDate(DateTimeInterface $date)
    {
        $dateTime = new DateTime($date->format('Y-m-d H:i:s.u'));
        $dateTime->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
        return $dateTime->format(DateTime::RFC3339_EXTENDED);
    }
}