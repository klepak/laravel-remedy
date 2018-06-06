<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

/**
 * Remedy API: Change
 */
class Change extends RemedyCase
{
    use HasTasks;
    
    /**
     * Base name of API interface for this case type
     */
	protected static $interface = 'CHG:ChangeInterface';

    /**
     * Maps field names on the API create interface to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $createInterfaceFieldMap = [
        
    ];

    /**
     * Maps field names on the API standard interface to normalized field names
     * 
     * Follows the format Normalized Field Name => Type-Specific Variant Field Name
     */
    protected static $standardInterfaceFieldMap = [
		
    ];
}