<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Drivers;

use Illuminate\Support\Facades\Http;
use Mortezaa97\SmsManager\Contracts\SmsDriverInterface;

/**
 * SMS.ir driver using REST API.
 *
 * @see https://sms.ir/rest-api/
 */
class SmsIrDriver implements SmsDriverInterface
{
    protected string $apiKey;

    /** Line number (sender) - required for bulk send. */
    protected string $lineNumber;

    protected string $baseUrl = 'https://api.sms.ir/v1';

    public function __construct(string $apiKey, string $lineNumber)
    {
        $this->apiKey = $apiKey;
        $this->lineNumber = $lineNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $receptor, string $message, ?string $sender = null): array
    {
        $mobiles = array_map('trim', explode(',', $receptor));
        $mobiles = array_values(array_filter($mobiles));
        if (count($mobiles) === 0) {
            return ['status' => 0, 'statustext' => 'شماره گیرنده معتبر نیست', 'cost' => 0];
        }
        $lineNumber = $sender ?? $this->lineNumber;
        $result = $this->callBulk($lineNumber, $mobiles, $message);
        if (isset($result['entries']) && is_array($result['entries'])) {
            if (count($result['entries']) === 1) {
                return $result['entries'][0];
            }
            // Multiple recipients: return first entry with total cost for single log line
            $first = $result['entries'][0];
            $first['cost'] = (int) ($result['total_cost'] ?? array_sum(array_column($result['entries'], 'cost')));
            $first['receptor'] = $receptor;

            return $first;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * SMS.ir bulk API allows max 100 mobiles per request; we chunk and merge results.
     */
    public function sendToMany(array $receptors, string $message, ?string $sender = null): array
    {
        $receptors = array_values(array_filter(array_map(function ($r) {
            return preg_replace('/\s+/', '', (string) $r);
        }, $receptors)));
        if (count($receptors) === 0) {
            return [];
        }
        $lineNumber = $sender ?? $this->lineNumber;
        $chunks = array_chunk($receptors, 100);
        $allEntries = [];
        foreach ($chunks as $chunk) {
            $result = $this->callBulk($lineNumber, $chunk, $message);
            if (isset($result['entries']) && is_array($result['entries'])) {
                $allEntries = array_merge($allEntries, $result['entries']);
            }
        }

        return $allEntries;
    }

    /**
     * Call SMS.ir bulk send API.
     *
     * @param  array<int, string>  $mobiles
     * @return array{status?: int, statustext?: string, total_cost?: int, entries?: array<int, array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}>}
     */
    private function callBulk(string $lineNumber, array $mobiles, string $messageText): array
    {
        $url = "{$this->baseUrl}/send/bulk";
        $body = [
            'lineNumber' => (int) $lineNumber,
            'messageText' => $messageText,
            'mobiles' => $mobiles,
        ];

        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, $body);

        $json = $response->json() ?? [];
        $status = (int) ($json['status'] ?? 0);
        $message = (string) ($json['message'] ?? '');
        $data = $json['data'] ?? [];

        if ($status !== 1) {
            return [
                'status' => $status,
                'statustext' => $message,
                'sender' => $lineNumber,
                'cost' => 0,
            ];
        }

        $messageIds = $data['messageIds'] ?? [];
        $totalCost = (float) ($data['cost'] ?? 0);
        $count = count($mobiles);
        $costPerMessage = $count > 0 ? (int) round($totalCost / $count) : 0;

        $entries = [];
        foreach ($mobiles as $i => $mobile) {
            $messageId = isset($messageIds[$i]) && $messageIds[$i] !== null ? (int) $messageIds[$i] : 0;
            $entries[] = [
                'messageid' => $messageId,
                'cost' => $costPerMessage,
                'status' => 1,
                'statustext' => 'موفق',
                'sender' => $lineNumber,
                'receptor' => $mobile,
            ];
        }

        return [
            'status' => 1,
            'statustext' => $message,
            'total_cost' => (int) round($totalCost),
            'entries' => $entries,
        ];
    }
}
