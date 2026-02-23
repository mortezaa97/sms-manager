<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Traits;

use App\Enums\Status;
use Mortezaa97\SmsManager\Models\SmsMessage;

trait SmsLogger
{
    /**
     * Log an SMS message to the database.
     *
     * @param string $message   The SMS message content or error.
     * @param string $receiver  Recipient of the SMS.
     * @param string|null $sender   Sender info (nullable).
     * @param float|int|null $cost  Cost of SMS (nullable, null or 0 meaning no cost/failed).
     * @param string|null $action   Action/context (e.g. 'otp').
     * @param int|null $driverId    Optional driver_id (uses default driver if null).
     * @param int|null $patternId   Optional pattern_id.
     *
     * If cost is present and nonzero, log as SENT; otherwise as FAILED.
     */
    public static function Log($message, $receiver, $sender = null, $cost = null, $action = null, $driverId = null, $patternId = null): void
    {
        $driverId = $driverId ?? \Mortezaa97\SmsManager\Models\SmsDriver::where('is_default', true)->first()?->id
            ?? \Mortezaa97\SmsManager\Models\SmsDriver::query()->first()?->id;

        $base = [
            'message' => $message,
            'receiver' => $receiver,
            'sender' => $sender,
            'cost' => $cost ?? 0,
            'action' => $action,
            'driver_id' => $driverId,
            'pattern_id' => $patternId,
        ];

        if ($cost !== null && $cost > 0) {
            SmsMessage::create(array_merge($base, ['status' => Status::SENT->value, 'cost' => $cost]));
        } else {
            SmsMessage::create(array_merge($base, ['status' => Status::FAILED->value]));
        }
    }
}
