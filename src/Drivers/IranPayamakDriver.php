<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Drivers;

use Illuminate\Support\Facades\Http;
use Mortezaa97\SmsManager\Contracts\SmsDriverInterface;

/**
 * Iran Payamak (FarazSMS) driver using Public REST API.
 *
 * @see https://docs.iranpayamak.com/
 */
class IranPayamakDriver implements SmsDriverInterface
{
    protected string $apiKey;

    protected string $username;

    protected string $password;

    /** Default sender (line number). */
    protected ?string $defaultSender;

    protected string $baseUrl = 'https://api.iranpayamak.com/ws/v1';

    public function __construct(string $apiKey, string $username, string $password, ?string $defaultSender = null)
    {
        $this->apiKey = $apiKey;
        $this->username = $username;
        $this->password = $password;
        $this->defaultSender = $defaultSender;
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
        $from = $sender ?? $this->defaultSender ?? '';
        if (count($mobiles) === 1) {
            return $this->sendSimple($mobiles[0], $from, $message);
        }
        $results = $this->sendBulk($mobiles, $from, $message);
        if (count($results) === 0) {
            return ['status' => 0, 'statustext' => 'خطا در ارسال', 'cost' => 0, 'receptor' => $receptor];
        }
        $first = $results[0];
        $first['receptor'] = $receptor;
        $first['cost'] = (int) array_sum(array_column($results, 'cost'));

        return $first;
    }

    /**
     * {@inheritdoc}
     */
    public function sendToMany(array $receptors, string $message, ?string $sender = null): array
    {
        $receptors = array_values(array_filter(array_map(function ($r) {
            return preg_replace('/\s+/', '', (string) $r);
        }, $receptors)));
        if (count($receptors) === 0) {
            return [];
        }
        $from = $sender ?? $this->defaultSender ?? '';

        return $this->sendBulk($receptors, $from, $message);
    }

    /**
     * Build HTTP client with Api-Key and optional auth token (from login).
     */
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Send a single simple SMS.
     *
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}
     */
    private function sendSimple(string $to, string $from, string $text): array
    {
        $body = [
            'to' => [$to],
            'from' => $from,
            'text' => $text,
        ];
        $response = $this->client()->post("{$this->baseUrl}/send/simple", $body);
        $json = $response->json() ?? [];

        return $this->parseSendResponse($json, $to, $from);
    }

    /**
     * Send bulk simple SMS (one request with multiple recipients).
     *
     * @param  array<int, string>  $to
     * @return array<int, array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}>
     */
    private function sendBulk(array $to, string $from, string $text): array
    {
        $body = [
            'to' => $to,
            'from' => $from,
            'text' => $text,
        ];
        $response = $this->client()->post("{$this->baseUrl}/send/simple", $body);
        $json = $response->json() ?? [];
        $entries = $json['entries'] ?? $json['data']['entries'] ?? null;
        if (is_array($entries) && count($entries) > 0) {
            $result = [];
            foreach ($entries as $i => $entry) {
                $result[] = $this->normalizeEntry($entry, $to[$i] ?? '', $from);
            }

            return $result;
        }
        $status = (int) ($json['status'] ?? $json['data']['status'] ?? 0);
        $statustext = (string) ($json['message'] ?? $json['statustext'] ?? $response->body());
        $one = [
            'status' => $status,
            'statustext' => $statustext,
            'sender' => $from,
            'cost' => 0,
        ];

        return array_map(function ($receptor) use ($one) {
            return array_merge($one, ['receptor' => $receptor]);
        }, $to);
    }

    /**
     * Parse response for single send.
     *
     * @param  array<string, mixed>  $json
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}
     */
    private function parseSendResponse(array $json, string $receptor, string $from): array
    {
        $entries = $json['entries'] ?? $json['data']['entries'] ?? null;
        if (is_array($entries) && isset($entries[0])) {
            return $this->normalizeEntry($entries[0], $receptor, $from);
        }
        if (is_array($entries) && isset($entries['messageid'])) {
            return $this->normalizeEntry($entries, $receptor, $from);
        }
        $status = (int) ($json['status'] ?? $json['data']['status'] ?? 0);
        $message = (string) ($json['message'] ?? $json['statustext'] ?? '');
        $messageId = (int) ($json['messageId'] ?? $json['data']['messageId'] ?? 0);
        $cost = (int) ($json['cost'] ?? $json['data']['cost'] ?? 0);
        $ok = $status === 1 || $status === 200;

        return [
            'messageid' => $messageId,
            'cost' => $cost,
            'status' => $ok ? 1 : $status,
            'statustext' => $message ?: ($ok ? 'موفق' : 'خطا'),
            'sender' => $from,
            'receptor' => $receptor,
        ];
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}
     */
    private function normalizeEntry(array $entry, string $receptor, string $from): array
    {
        return [
            'messageid' => (int) ($entry['messageid'] ?? $entry['messageId'] ?? 0),
            'cost' => (int) ($entry['cost'] ?? 0),
            'status' => (int) ($entry['status'] ?? 1),
            'statustext' => (string) ($entry['statustext'] ?? $entry['message'] ?? 'موفق'),
            'sender' => (string) ($entry['sender'] ?? $from),
            'receptor' => (string) ($entry['receptor'] ?? $receptor),
        ];
    }
}
