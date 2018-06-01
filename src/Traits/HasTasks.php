<?php

namespace Klepak\RemedyApi\Traits;

trait HasTasks
{
    public function createTask($normalizedData)
    {
        // check if model is proper instance

        $normalizedData["RootRequestInstanceID"] = $this->model->InstanceId;
        $normalizedData["RootRequestName"] = $this->model->Case_Number;

        return (new Task)->api()->create($normalizedData);
    }
}