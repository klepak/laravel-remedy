<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

/**
 * Remedy API: WorkOrder
 */
class WorkOrder extends RemedyCase
{
    use HasTasks;

    /**
     * Base name of API interface for this case type
     */
	protected static $interface = 'WOI:WorkOrderInterface';

    /**
     * Maps field names on the API create interface to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $createInterfaceFieldMap = [
        "Case_Number" => "WorkOrder_ID",
        "Entry_ID" => "Request ID",

        "Assignee" => "Request Assignee",
        "Description" => "Summary"
    ];

    /**
     * Maps field names on the API standard interface to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $standardInterfaceFieldMap = [
		"Support_Organization" => "Assigned Support Organization",

		"Support_Group_ID" => "Assigned Group ID",
    ];

    /**
     * Root request form name for Task owner
     *
     * Identifies the case type of the Task owner
     */
    protected static $rootRequestFormName = 'WOI:WorkOrder';
}
