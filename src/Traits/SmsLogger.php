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
     * 
     * If cost is present and nonzero, log as SENT; otherwise as FAILED.
     */
    public static function Log($message, $receiver, $sender = null, $cost = null, $action = null): void
    {
        // If cost is not null and is greater than 0, log as SENT; otherwise assume fail
        if ($cost !== null && $cost > 0) {
            SmsMessage::create([
                'message' => $message,
                'receiver' => $receiver,
                'cost' => $cost,
                'sender' => $sender,
                'action' => $action,
                'status' => Status::SENT->value,
            ]);
        } else {
            SmsMessage::create([
                'message' => $message,
                'receiver' => $receiver,
                'sender' => $sender,
                'cost' => $cost ?? 0,
                'action' => $action,
                'status' => Status::FAILED->value,
            ]);
        }
    }
}
