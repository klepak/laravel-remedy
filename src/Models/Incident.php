<?php

namespace Klepak\RemedyApi\Models;

class Incident extends RemedyCase
{
	protected $table = "HPD_HELP_DESK";
	protected $worklogTable = "HPD_WorkLog";

    protected static $dbSelectFields = [
        "Incident_Number",
		"Status",
		"Description",
		"Company",
		"Assignee",
		"Assignee_Login_ID",
		"Last_Modified_By",
		"Assigned_Group",
		"Submitter",
		"Submit_Date",
		"Last_Modified_Date",
		"Entry_ID",
		"InstanceId",
		"Detailed_Decription",
		"First_Name",
		"Last_Name",
		"Status_Reason"
    ];

    protected static $dbDefaultFields = [
        "Incident_Number",
		"Status",
		"Description",
		"Company",
		"Assignee",
		"Assigned_Group",
		"Latest_Worklog"
    ];

    // Standardized Field Name => Type-Specific Variant Field Name
    protected static $dbFieldMap = [
        "Detailed_Description" => "Detailed_Decription",
		"Customer_Company" => "Company",
		"Case_Number" => "Incident_Number",
    ];

    const STATUS_NEW = 0;
	const STATUS_ASSIGNED = 1;
	const STATUS_IN_PROGRESS = 2;
	const STATUS_PENDING = 3;
	const STATUS_RESOLVED = 4;
	const STATUS_CLOSED = 5;
	const STATUS_CANCELLED = 6;

    const MAX_ACTIVE_STATUS = self::STATUS_PENDING;

    protected static $statusTextMap = [
        self::STATUS_NEW => "New", 
		self::STATUS_ASSIGNED => "Assigned", 
		self::STATUS_IN_PROGRESS => "In Progress", 
		self::STATUS_PENDING => "Pending", 
		self::STATUS_RESOLVED => "Resolved", 
		self::STATUS_CLOSED => "Closed", 
		self::STATUS_CANCELLED => "Cancelled"
    ];
}
