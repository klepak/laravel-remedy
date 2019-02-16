<?php

namespace Klepak\RemedyApi\Traits;

use Klepak\RemedyApi\Models\Task;
use Klepak\RemedyApi\Models\TaskTemplate;
use Exception;

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
    public function createTask($createTaskData)
    {
        if($this->model === null)
            throw new Exception("Model not initialized");

		$createTaskData = array_merge($createTaskData, [
			"RootRequestMode" => "0",
			"RootRequestFormName"  => static::$rootRequestFormName,
        ]);

		foreach($this->model->getTaskOwnerCreateInterfaceFieldMap() as $taskField => $caseField)
		{
			if(isset($this->model->{$caseField}) && !empty($this->model->{$caseField}) && !isset($createTaskData[$taskField]))
				$createTaskData[$taskField] = $this->model->{$caseField};
        }

        // account for differences in prod and dev
        if(env('REMEDY_TEST'))
        {
            if(isset($createTaskData['Min']))
            {
                $createTaskData['Minutes'] = $createTaskData['Min'];
                unset($createTaskData['Min']);
            }
        }

        return (new Task)->api()->create($createTaskData);
    }

    public function createTaskFromTemplate($templateId, $taskData)
	{
        $template = TaskTemplate::find($templateId);

		$keepTemplateKeys = [
				'Summary',
				['Material_ID', 'Material ID'],
				'Notes',
				'TaskType',
				'Category',
				'TaskName',
				['Service_Categorization_Tier_1', 'Service Cat Tier 1'],
				['Service_Categorization_Tier_2', 'Service Cat Tier 2'],
				['Service_Categorization_Tier_3', 'Service Cat Tier 3'],
				'Hours',
				['Min_x', 'Min'],
				['Invoice_Text', 'Invoice Text'],
        ];

		foreach($keepTemplateKeys as $key)
		{
			if(is_array($key))
			{
				$src = $key[0];
				$dst = $key[1];
			}
			else
			{
				$src = $key;
				$dst = $key;
			}

			if(!isset($taskData[$dst]) && isset($template->{$src}))
				$taskData[$dst] = $template->{$src};
		}

        // TODO: causes specified fields to be overridden with template data
        #$taskData['TemplateID'] = $template->InstanceId;

		return $this->createTask($taskData);
	}

}
