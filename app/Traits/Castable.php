<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait Castable
{
    public static function fromModel(Model $model): mixed
    {
        $instance = new self();
        $instance->setRawAttributes($model->getAttributes(), true);
        $instance->setRelations($model->getRelations());

        if ($model->exists) {
            $instance->exists = true;
        }

        return $instance;
    }
}
