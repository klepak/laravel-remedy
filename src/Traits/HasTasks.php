<?php

namespace Klepak\RemedyApi\Traits;

use Klepak\RemedyApi\Models\Task;

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
        if($this->model === null)
            throw new \Exception("Model not initialized");

        $instanceId = $this->model->InstanceId;
        $caseNumber = $this->model->Case_Number;

        if(empty($instanceId))
            throw new \Exception("Model instance missing InstanceId");
        
        if(empty($caseNumber))
            throw new \Exception("Model instance missing Case_Number");

        $normalizedData["RootRequestInstanceID"] = $instanceId;
        $normalizedData["RootRequestName"] = $caseNumber;
        $normalizedData["RootRequestMode"] = "0";

        return (new Task)->api()->create($normalizedData);
    }
}