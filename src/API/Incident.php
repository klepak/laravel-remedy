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
    ];

    protected static $standardInterfaceFieldMap = [
		
    ];
}