<?php

namespace Klepak\RemedyApi\Models;

/**
 * Remedy model: TaskTemplate
 */
class TaskTemplate extends RemedyCase
{
    /**
     * Database table for model
     */
    protected $table = "TMS_TaskTemplate";

    /**
     * Variant field names to select in default model scope
     */
    protected static $dbSelectFields = [
        "Last_Modified_By",
        "Modified_Date",
        "Status",

        "Summary",
        "Invoice_Text",
        "Hours",
        "Minutes",
    ];

    /**
     * Maps type-specific field names in the database to normalized field names
     *
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $dbFieldMap = [];

    /**
     * Maps status int value from database to correct string representation
     *
     * Follows the format status_int => status_text
     */
    protected static $statusTextMap = [];
}
