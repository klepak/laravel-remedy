<?php

namespace Klepak\RemedyApi\API;

class WorkOrder extends RemedyCase
{
	protected static $interface = 'WOI:WorkOrder';

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "WorkOrder_ID",
		"Entry_ID" => "Request ID"
    ];

    protected static $standardInterfaceFieldMap = [
		"Customer_Company" => "Assigned Support Company",
		"Support_Organization" => "Assigned Support Organization",

		"Support_Group_ID" => "Assigned Group ID",
    ];
}