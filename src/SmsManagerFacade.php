<?php

namespace Mortezaa97\SmsManager;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Mortezaa97\SmsManager\Contracts\SmsDriverInterface driver(?string $name = null)
 * @method static array send(string $receptor, string $message, ?string $sender = null, string $action = 'manual')
 * @method static array sendToMany(array $receptors, string $message, ?string $sender = null, string $action = 'manual')
 * @method static array verifyLookup(string $receptor, string $template, ?string $token = null, ?string $token2 = null, ?string $token3 = null, ?string $token10 = null, ?string $token20 = null)
 *
 * @see \Mortezaa97\SmsManager\SmsManager
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
