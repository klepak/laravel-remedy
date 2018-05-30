<?php

namespace Klepak\RemedyApi\API;

class WorkOrder extends RemedyCase
{
	protected static $interface = 'HPD:Incident';

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "WorkOrder_ID",
		"Entry_ID" => "Request ID"
    ];

    protected static $standardInterfaceFieldMap = [
        "Description" => "Summary",
		"Assignee" => "Request Assignee",

		"Assigned Support Company" => "Support Company",
		"Assigned Support Organization" => "Support Organization",
		"Assigned Support Group Name" => "Support Group",

		"Assigned Group" => "Support Group Name",

		"Assigned Group ID" => "Support Group ID",
    ];
}