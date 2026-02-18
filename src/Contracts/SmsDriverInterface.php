<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Contracts;

interface SmsDriverInterface
{
    /**
     * Send a single SMS to one or more recipients (comma-separated receptor string).
     *
     * @param  string  $receptor  Recipient(s) phone number(s), comma-separated for multiple
     * @param  string  $message  Message text
     * @param  string|null  $sender  Optional sender line
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}
     */
    public function send(string $receptor, string $message, ?string $sender = null): array;

    /**
     * Send the same message to multiple recipients.
     *
     * @param  array<int, string>  $receptors  List of phone numbers
     * @param  string  $message  Message text
     * @param  string|null  $sender  Optional sender line
     * @return array<int, array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}>
     */
    public function sendToMany(array $receptors, string $message, ?string $sender = null): array;
}
