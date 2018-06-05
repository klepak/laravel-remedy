<?php

namespace Klepak\RemedyApi\Models;

/**
 * Remedy model: Incident
 */
class Incident extends RemedyCase
{
	/**
     * Database table for model
     */
	protected $table = "HPD_HELP_DESK";

	/**
     * Associated worklog DB table
     */
	protected $worklogTable = "HPD_WorkLog";

	/**
     * Variant field names to select in default model scope
     */
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

    /**
     * Maps type-specific field names in the database to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
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

	/**
     * Maps status int value from database to correct string representation
     * 
     * Follows the format status_int => status_text
     */
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
