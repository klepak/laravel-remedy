<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

class Incident extends RemedyCase
{
    use HasTasks;
    
	protected static $interface = 'HPD:Incident';

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "Incident Number",
        "Entry_ID" => "Incident_Entry_ID",
        "Login_ID" => "Login_ID",
        "Service_Type" => "Service_Type",
        
        "Support_Organization" => "Assigned Support Organization",
        "Support_Company" => "Assigned Support Company",
    ];

    // Normalized field name => Value
    protected static $createInterfaceDefaultValues = [
        #"Impact" => 4000,
        #"Urgency" => 4000,
        #"Status" => "Assigned",
        #"Reported_Source" => "Systems Management",
        #"Service_Type" => "Infrastructure Event",
    ];

    protected static $standardInterfaceFieldMap = [
		
    ];
}