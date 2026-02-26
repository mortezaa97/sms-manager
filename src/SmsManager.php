<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager;

use InvalidArgumentException;
use Mortezaa97\SmsManager\Contracts\SmsDriverInterface;
use Mortezaa97\SmsManager\Models\SmsDriver;
use Mortezaa97\SmsManager\Traits\SmsLogger;

class SmsManager
{
    use SmsLogger;

    protected ?SmsDriverInterface $driver = null;

    protected function resolveDriver(string $name, ?SmsDriver $model = null): SmsDriverInterface
    {
        $drivers = config('sms-manager.drivers', []);
        $config = $drivers[$name] ?? [];
        if ($model !== null) {
            $config = array_merge($config, array_filter([
                'username' => $model->username,
                'password' => $model->password ?: $model->api,
                'sender' => $model->sender,
                'api_key' => $model->api,
            ], fn ($v) => $v !== null && $v !== ''));
        }
        if ($name === 'kavenegar') {
            return new Drivers\KavenegarDriver(
                (string) ($config['api_key'] ?? ''),
                $config['sender'] ?? null
            );
        }
        if ($name === 'smsir') {
            return new Drivers\SmsIrDriver(
                (string) ($config['api_key'] ?? ''),
                (string) ($config['line_number'] ?? '')
            );
        }
        if ($name === 'iranpayamak') {
            return new Drivers\IranPayamakDriver(
                (string) ($config['api_key'] ?? ''),
                (string) ($config['username'] ?? ''),
                (string) ($config['password'] ?? ''),
                isset($config['sender']) ? (string) $config['sender'] : null
            );
        }
        if ($name === 'farapayamak') {
            return new Drivers\FarapayamakDriver(
                (string) ($config['username'] ?? ''),
                (string) ($config['password'] ?? $config['api_key'] ?? ''),
                isset($config['sender']) ? (string) $config['sender'] : null
            );
        }
        throw new InvalidArgumentException("SMS driver [{$name}] is not supported.");
    }

    /**
     * @param  array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}  $result
     */
    protected function logResult(array $result, string $receptor, string $message, string $action, ?int $driverId = null, ?int $patternId = null): void
    {
        $cost = $result['cost'] ?? 0;
        $sender = $result['sender'] ?? null;
        $status = (int) ($result['status'] ?? 0);
        $log = $result['statustext'] ?? null;
        $success = $status === 200 || $status === 1 || $status === 4 || $status === 5 || $status === 10;
        self::Log($message, $receptor, $sender, $success ? (int) $cost : 0, $action, $driverId, $patternId,$log);
    }

    public function driver(?string $name = null): SmsDriverInterface
    {
        if ($name !== null) {
            return $this->resolveDriver($name);
        }
        if ($this->driver === null) {
            $this->driver = $this->resolveDriver(config('sms-manager.default', 'kavenegar'));
        }

        return $this->driver;
    }

    public function send(string $receptor, string $message, ?string $sender = null, string $action = 'manual', ?int $driverId = null, ?int $patternId = null, ?string $log = null): array
    {
        $driver = SmsDriver::where('id', $driverId)->first();
        if ($driver) {
            $this->driver = $this->resolveDriver($driver->title, $driver);
        }
        $result = $this->driver()->send($receptor, $message, $sender);
        $this->logResult($result, $receptor, $message, $action, $driverId, $patternId);

        return $result;
    }

    /**
     * @param  array<int, string>  $receptors
     * @return array<int, array>
     */
    public function sendToMany(array $receptors, string $message, ?string $sender = null, string $action = 'manual', ?int $driverId = null, ?int $patternId = null): array
    {
        $receptors = array_values(array_filter(array_map(function ($r) {
            return preg_replace('/\s+/', '', (string) $r);
        }, $receptors)));
        if (count($receptors) === 0) {
            return [];
        }
        $driver = SmsDriver::where('id', $driverId)->first();
        if ($driver) {
            $this->driver = $this->resolveDriver($driver->title, $driver);
        }
        $results = $this->driver()->sendToMany($receptors, $message, $sender);
        foreach ($results as $idx => $result) {
            $arr = is_array($result) ? $result : (array) $result;
            $receptor = $arr['receptor'] ?? $receptors[$idx] ?? '';
            $this->logResult($arr, $receptor, $message, $action, $driverId, $patternId);
        }

        return $results;
    }

    /**
     * Send verification/lookup SMS (OTP-style) via Kavenegar Verify Lookup API.
     * Uses Kavenegar driver; if app uses kavenegar/laravel you can still use this for consistency.
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
        $driver = $this->driver();
        if (! $driver instanceof Drivers\KavenegarDriver) {
            return ['status' => 402, 'statustext' => 'Verify lookup only supported by Kavenegar driver'];
        }

        return $driver->verifyLookup($receptor, $template, $token, $token2, $token3, $token10, $token20);
    }
}
