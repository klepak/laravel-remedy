<?php

namespace Klepak\RemedyApi\API;

/**
 * Remedy API: Task
 */
class Task extends RemedyCase
{
    /**
     * Base name of API interface for this case type
     */
	protected static $interface = 'TMS:Task';

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

    public function getCreateInterface()
    {
        return $this->getStandardInterface();
    }
}