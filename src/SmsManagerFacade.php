<?php

namespace Mortezaa97\SmsManager;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mortezaa97\SmsManager\Skeleton\SkeletonClass
 */
class SmsManagerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sms-manager';
    }
}
