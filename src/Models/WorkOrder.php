<?php

namespace Klepak\RemedyApi\Models;

class WorkOrder extends RemedyCase
{
    protected $table = "WOI_WorkOrder";

    protected static $dbSelectFields = [
        "InstanceId",
		"Request_ID",
		"Status",
		"Submitter",
		"Support_Group_Name",
		"Request_Assignee",
		"Customer_Company",
		"Support_Group_ID",
		"Work_Order_ID",
		"Summary",
		"Detailed_Description",
		"Customer_First_Name",
		"Customer_Last_Name",
		"Customer_Internet_E_mail",
		"ASLOGID", # assignee login id
		"ASGRP", # assigned group,

		"Last_Modified_By",
		"Last_Modified_Date",
		"Submit_Date",
    ];

    protected static $dbDefaultFields = [
        "Status",
		"Customer_Company",
		"Work_Order_ID",
		"Description",

		# generated properties #
		"Assignee",
		"Assigned_Group",
    ];

    // Standardized Field Name => Type-Specific Actual Field Name
    protected static $dbFieldMap = [
        "Assigned_Group" => "ASGRP",
		"Assignee" => "Request_Assignee",
		"Assignee_Login_ID" => "ASLOGID",
		"Case_Number" => "Work_Order_ID",
		"Description" => "Summary",
		"Last_Resolved_Date" => "Completed_Date",
		"Entry_ID" => "Request_ID",
    ];

    const STATUS_ASSIGNED = 0;
	const STATUS_PENDING = 1;
	const STATUS_WAITING_APPROVAL = 2;
	const STATUS_PLANNING = 3;
	const STATUS_IN_PROGRESS = 4;
	const STATUS_COMPLETED = 5;
	const STATUS_REJECTED = 6;
	const STATUS_CANCELLED = 7;
    const STATUS_CLOSED = 8;

    const MAX_ACTIVE_STATUS = self::STATUS_IN_PROGRESS;

    private static $statusTextMap = [
        self::STATUS_ASSIGNED => "Assigned", 
        self::STATUS_PENDING => "Pending", 
        self::STATUS_WAITING_APPROVAL => "Waiting Approval", 
        self::STATUS_PLANNING => "Planning", 
        self::STATUS_IN_PROGRESS => "In Progress", 
        self::STATUS_COMPLETED => "Completed", 
        self::STATUS_REJECTED => "Rejected", 
        self::STATUS_CANCELLED => "Cancelled", 
        self::STATUS_CLOSED => "Closed", 
    ];

    
}
