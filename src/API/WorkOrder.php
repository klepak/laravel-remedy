<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

class WorkOrder extends RemedyCase
{
    use HasTasks;
    
	protected static $interface = 'WOI:WorkOrder';

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "WorkOrder_ID",
        "Entry_ID" => "Request ID",
        
        "Assignee" => "Request Assignee",
        "Description" => "Summary"
    ];

    protected static $standardInterfaceFieldMap = [
		"Support_Organization" => "Assigned Support Organization",

		"Support_Group_ID" => "Assigned Group ID",
    ];
}