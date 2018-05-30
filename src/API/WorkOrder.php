<?php

namespace Klepak\RemedyApi\API;

class WorkOrder extends RemedyCase
{

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "WorkOrder_ID",
		"Entry_ID" => "Request ID"
    ];

    protected static $standardInterfaceFieldMap = [
        "Summary" => "Description",
		"Request Assignee" => "Assignee",

		"Support Company" => "Assigned Support Company",
		"Support Organization" => "Assigned Support Organization",
		"Support Group" => "Assigned Support Group Name",

		"Support Group Name" => "Assigned Group",

		"Support Group ID" => "Assigned Group ID",
    ];
}