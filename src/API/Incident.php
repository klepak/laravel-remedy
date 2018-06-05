<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

/**
 * Remedy API: Incident
 */
class Incident extends RemedyCase
{
    use HasTasks;
    
    /**
     * Base name of API interface for this case type
     */
	protected static $interface = 'HPD:Incident';

    /**
     * Maps field names on the API create interface to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $createInterfaceFieldMap = [
        "Case_Number" => "Incident Number",
        "Entry_ID" => "Incident_Entry_ID",
        "Login_ID" => "Login_ID",
        "Service_Type" => "Service_Type",
        
        "Support_Organization" => "Assigned Support Organization",
        "Support_Company" => "Assigned Support Company",
    ];

    /**
     * Contains default values of some fields on the create interface
     * 
     * Follows the format Normalized Field Name => Value
     */
    protected static $createInterfaceDefaultValues = [
        #"Impact" => 4000,
        #"Urgency" => 4000,
        #"Status" => "Assigned",
        #"Reported_Source" => "Systems Management",
        #"Service_Type" => "Infrastructure Event",
    ];

    /**
     * Maps field names on the API standard interface to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $standardInterfaceFieldMap = [
		
    ];
}