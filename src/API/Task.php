<?php

namespace Klepak\RemedyApi\API;

class Task extends RemedyCase
{
	protected static $interface = 'TMS:Task';

    protected static $createInterfaceFieldMap = [

    ];

    protected static $standardInterfaceFieldMap = [
		
    ];

    public function getCreateInterface()
    {
        return $this->getStandardInterface();
    }
}