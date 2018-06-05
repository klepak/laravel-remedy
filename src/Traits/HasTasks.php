<?php

namespace Klepak\RemedyApi\Traits;

/**
 * Provides simplified functionality for handling related Tasks
 */
trait HasTasks
{
    /**
     * Creates a Task related to the current instance
     * 
     * @param array $normalizedData The normalized data to use for creation
     */
    public function createTask($normalizedData)
    {
        // check if model is proper instance

        $normalizedData["RootRequestInstanceID"] = $this->model->InstanceId;
        $normalizedData["RootRequestName"] = $this->model->Case_Number;

        return (new Task)->api()->create($normalizedData);
    }
}