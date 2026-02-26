<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Drivers;

use Illuminate\Support\Facades\Http;
use Mortezaa97\SmsManager\Contracts\SmsDriverInterface;

class KavenegarDriver implements SmsDriverInterface
{
    protected string $apiKey;

    protected ?string $defaultSender;

    protected string $baseUrl = 'https://api.kavenegar.com/v1';

    public function __construct(string $apiKey, ?string $defaultSender = null)
    {
        $this->apiKey = $apiKey;
        $this->defaultSender = $defaultSender;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $receptor, string $message, ?string $sender = null): array
    {
        $sender = $sender ?? $this->defaultSender;
        $url = "{$this->baseUrl}/{$this->apiKey}/sms/send.json";
        $params = [
            'receptor' => $receptor,
            'message' => $message,
        ];
        if ($sender !== null && $sender !== '') {
            $params['sender'] = $sender;
        }

        $response = Http::asForm()->post($url, $params);

        return $this->parseResponse($response->json(), $receptor, $message, $sender);
    }

    /**
     * {@inheritdoc}
     */
    public function sendToMany(array $receptors, string $message, ?string $sender = null): array
    {
        if (count($receptors) === 0) {
            return [];
        }
        $receptor = implode(',', array_map(function ($r) {
            return preg_replace('/\s+/', '', (string) $r);
        }, $receptors));
        $result = $this->send($receptor, $message, $sender);
        if (isset($result['entries']) && is_array($result['entries'])) {
            return $result['entries'];
        }

        return [$result];
    }

    /**
     * Kavenegar Verify Lookup (OTP/template) - see https://kavenegar.com/rest.html#Lookup.
     */
    public function verifyLookup(
        string $receptor,
        string $template,
        ?string $token = null,
        ?string $token2 = null,
        ?string $token3 = null,
        ?string $token10 = null,
        ?string $token20 = null
    ): array {
        $url = "{$this->baseUrl}/{$this->apiKey}/verify/lookup.json";
        $params = [
            'receptor' => $receptor,
            'template' => $template,
            'token' => $token ?? '',
        ];
        if ($token2 !== null) {
            $params['token2'] = $token2;
        }
        if ($token3 !== null) {
            $params['token3'] = $token3;
        }
        if ($token10 !== null) {
            $params['token10'] = $token10;
        }
        if ($token20 !== null) {
            $params['token20'] = $token20;
        }
        $response = Http::asForm()->post($url, $params);
        $json = $response->json() ?? [];
        $return = $json['return'] ?? [];
        $status = (int) ($return['status'] ?? 0);
        $entries = $json['entries'] ?? [];
        $first = is_array($entries) && isset($entries[0]) ? $entries[0] : $entries;

        return [
            'status' => $status,
            'messageid' => (int) ($first['messageid'] ?? 0),
            'cost' => (int) ($first['cost'] ?? 0),
            'statustext' => (string) ($first['statustext'] ?? $return['message'] ?? ''),
            'sender' => (string) ($first['sender'] ?? $this->defaultSender ?? ''),
            'receptor' => $receptor,
        ];
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string, entries?: array}
     */
    private function parseResponse(array $json, string $receptor, string $message, ?string $sender): array
    {
        $return = $json['return'] ?? [];
        $status = (int) ($return['status'] ?? 0);
        $entries = $json['entries'] ?? null;

        if ($status !== 200) {
            return [
                'status' => $status,
                'statustext' => $return['message'] ?? 'Unknown error',
                'receptor' => $receptor,
                'sender' => $sender,
                'cost' => 0,
            ];
        }

        if (is_array($entries) && isset($entries[0])) {
            $first = $entries[0];

            return [
                'messageid' => (int) ($first['messageid'] ?? 0),
                'cost' => (int) ($first['cost'] ?? 0),
                'status' => (int) ($first['status'] ?? 1),
                'statustext' => (string) ($first['statustext'] ?? ''),
                'sender' => (string) ($first['sender'] ?? $sender ?? ''),
                'receptor' => (string) ($first['receptor'] ?? $receptor),
                'entries' => $entries,
            ];
        }

        if (is_array($entries) && isset($entries['messageid'])) {
            return [
                'messageid' => (int) ($entries['messageid'] ?? 0),
                'cost' => (int) ($entries['cost'] ?? 0),
                'status' => (int) ($entries['status'] ?? 1),
                'statustext' => (string) ($entries['statustext'] ?? ''),
                'sender' => (string) ($entries['sender'] ?? $sender ?? ''),
                'receptor' => (string) ($entries['receptor'] ?? $receptor),
            ];
        }

        return [
            'status' => 1,
            'statustext' => 'در صف ارسال',
            'receptor' => $receptor,
            'sender' => $sender ?? '',
            'cost' => 0,
        ];
    }
}
