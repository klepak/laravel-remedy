<?php

namespace Klepak\RemedyApi\API;

use Klepak\RemedyApi\Traits\HasTasks;

class Change extends RemedyCase
{
    use HasTasks;
    
	protected static $interface = 'CHG:Change';

    protected static $createInterfaceFieldMap = [
        
    ];

    protected static $standardInterfaceFieldMap = [
		
    ];
}