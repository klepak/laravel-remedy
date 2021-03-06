<?php

namespace Klepak\RemedyApi\Models;

/**
 * Remedy model: Task
 */
class Task extends RemedyCase
{
    /**
     * Database table for model
     */
    protected $table = "TMS_Task";

    /**
     * Variant field names to select in default model scope
     */
    protected static $dbSelectFields = [
        "Task_ID",
        "Submitter",
        "Create_Date",

        "Assigned_To",
        "Assignee_Group",
        "Assignee",

        "Last_Modified_By",
        "Modified_Date",
        "Status",

        "Summary",
        "TaskName",

        "InstanceId",
        "Invoice_Text",
        "Customer_Company",
        "Customer_First_Name",
        "Customer_Last_Name",

        "Hours",
        #"Min_x",

        "RootRequestInstanceID",
        "RootRequestName",
        "RootRequestFormName",
        "RootRequestID",
    ];

    /**
     * Maps type-specific field names in the database to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $dbFieldMap = [
        "Assignee_Login_ID" => "Assigned_To",
        "Assigned_Group" => "Assignee_Group",

        "Last_Modified_Date" => "Modified_Date",
        "Case_Number" => "Task_ID",
        "Description" => "Summary",
        "Submit_Date" => "Create_Date",
        "Last_Resolved_Date" => "Actual_End_Date",
    ];

    const STATUS_STAGED = 1000;
    const STATUS_ASSIGNED = 2000;
    const STATUS_PENDING = 3000;
    const STATUS_WIP = 4000;
    const STATUS_WAITING = 5000;
    const STATUS_CLOSED = 6000;
    const STATUS_BYPASSED = 7000;

    const MAX_ACTIVE_STATUS = self::STATUS_WAITING;

    /**
     * Maps status int value from database to correct string representation
     *
     * Follows the format status_int => status_text
     */
    protected static $statusTextMap = [
        self::STATUS_STAGED => "Staged",
        self::STATUS_ASSIGNED => "Assigned",
        self::STATUS_PENDING => "Pending",
        self::STATUS_WIP => "Work In Progress",
        self::STATUS_WAITING => "Waiting",
        self::STATUS_CLOSED => "Closed",
        self::STATUS_BYPASSED => "Bypassed"
    ];
}
