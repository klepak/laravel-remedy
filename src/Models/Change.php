<?php

namespace Klepak\RemedyApi\Models;

class Change extends RemedyCase
{
	protected $table = "CHG_Infrastructure_Change";
	protected $worklogTable = "CHG_WorkLog";

    protected static $dbSelectFields = [
        "InstanceId",
        "Customer_Login_ID",
        "Description",
        "Detailed_Description",
        "Company",
        "Infrastructure_Change_ID",
        "Submitter",
        "Support_Group_Name",
        "ASGRP",
        "ASCHG", # assignee
        "ASLOGID",
        "Customer_Company",
		"Customer_First_Name",
		"Customer_Last_Name",
		"Customer_Internet_E_mail",
        "Change_Request_Status",
        "Submit_Date",
        "Entry_ID"
    ];

    protected static $dbDefaultFields = [
        "Request_ID",
        "Description",
        "Company",
        "Infrastructure_Change_ID",
        "Customer_Company",

        # generated properties #
        "Status", 
        "Assignee",
        "Assigned_Group",
    ];

    // Standardized Field Name => Type-Specific Variant Field Name
    protected static $dbFieldMap = [
        "Status" => "Change_Request_Status",
        "Assigned_Group" => "ASGRP",
        "Assignee" => "ASCHG",
        "Assignee_Login_ID" => "ASLOGID",
        "Case_Number" => "Infrastructure_Change_ID",
        "Last_Resolved_Date" => "Completed_Date",
    ];

    const STATUS_DRAFT = 0;
    const STATUS_REQ_AUTH = 1;
    const STATUS_REQ_CHG = 2;
    const STATUS_PLAN_PROG = 3;
    const STATUS_SCH_REV = 4;
    const STATUS_SCH_APP = 5;
    const STATUS_SCH = 6;
    const STATUS_IMP_IP = 7;
    const STATUS_PENDING = 8;
    const STATUS_REJECTED = 9;
    const STATUS_COMPLETED = 10;
    const STATUS_CLOSED = 11;
    const STATUS_CANCELLED = 12;

    const MAX_ACTIVE_STATUS = self::STATUS_PENDING;

    protected static $statusTextMap = [
        self::STATUS_DRAFT => "Draft", 
		self::STATUS_REQ_AUTH => "Request For Authorization", 
		self::STATUS_REQ_CHG => "Request For Change", 
		self::STATUS_PLAN_PROG => "Planning In Progress", 
		self::STATUS_SCH_REV => "Scheduled For Review", 
		self::STATUS_SCH_APP => "Scheduled For Approval", 
		self::STATUS_SCH => "Scheduled", 
		self::STATUS_IMP_IP => "Implementation In Progress", 
		self::STATUS_PENDING => "Pending", 
		self::STATUS_REJECTED => "Rejected", 
		self::STATUS_COMPLETED => "Completed", 
		self::STATUS_CLOSED => "Closed", 
		self::STATUS_CANCELLED => "Cancelled", 
    ];
}