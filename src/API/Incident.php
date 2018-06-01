<?php

namespace Klepak\RemedyApi\API;

class Incident extends RemedyCase
{
	protected static $interface = 'HPD:Incident';

    protected static $createInterfaceFieldMap = [
        "Case_Number" => "Incident Number",
        "Entry_ID" => "Incident_Entry_ID",
    ];

    protected static $standardInterfaceFieldMap = [
		
    ];
}