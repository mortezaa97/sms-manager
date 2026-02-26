<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Drivers;

use Illuminate\Support\Facades\Http;
use Mortezaa97\SmsManager\Contracts\SmsDriverInterface;

/**
 * Farapayamak (Payamak Panel) driver using REST API.
 *
 * @see https://rest.payamak-panel.com/
 * API docs: SendSMS, GetDeliveries2, GetMessages, GetCredit, etc.
 */
class FarapayamakDriver implements SmsDriverInterface
{
    protected string $username;

    protected string $password;

    protected ?string $defaultSender;

    protected string $baseUrl = 'https://rest.payamak-panel.com/api/SendSMS';

    public function __construct(string $username, string $password, ?string $defaultSender = null)
    {
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
        $mobiles = array_values(array_filter(array_map(function ($r) {
            return preg_replace('/\s+/', '', (string) $r);
        }, $mobiles)));
        if (count($mobiles) === 0) {
            return ['status' => 0, 'statustext' => 'شماره گیرنده معتبر نیست', 'cost' => 0];
        }
        $from = $sender ?? $this->defaultSender ?? '';
        if (empty($from)) {
            return ['status' => 0, 'statustext' => 'شماره فرستنده اجباری است', 'cost' => 0];
        }
        $to = implode(',', $mobiles);
        $response = $this->requestSendSms($to, $from, $message);
        return $this->parseSendResponse($response, $mobiles[0], $from);
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
        if (empty($from)) {
            $err = ['status' => 0, 'statustext' => 'شماره فرستنده اجباری است', 'cost' => 0];
            return array_map(fn ($r) => array_merge($err, ['receptor' => $r]), $receptors);
        }
        $results = [];
        $chunks = array_chunk($receptors, 100);
        foreach ($chunks as $chunk) {
            $to = implode(',', $chunk);
            $response = $this->requestSendSms($to, $from, $message);
            $parsed = $this->parseBulkResponse($response, $chunk, $from);
            $results = array_merge($results, $parsed);
        }
        return $results;
    }

    /**
     * Send SMS via Payamak Panel SendSMS API.
     */
    private function requestSendSms(string $to, string $from, string $text): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}/SendSMS", [
            'username' => $this->username,
            'password' => $this->password,
            'to' => $to,
            'from' => $from,
            'text' => $text,
        ]);
        return $response->json() ?? [];
    }

    /**
     * Parse SendSMS response for single recipient.
     *
     * @return array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}
     */
    private function parseSendResponse(array $json, string $receptor, string $from): array
    {
        $retStatus = (int) ($json['RetStatus'] ?? 0);
        $value = $json['Value'] ?? '';
        $strRetStatus = (string) ($json['StrRetStatus'] ?? '');
        $success = $retStatus === 1 && ($strRetStatus === 'Ok' || $strRetStatus === 'ok');
        $messageId = $success && is_numeric((string) $value) ? (int) $value : 0;
        $statustext = $this->resolveStatusText($value, $strRetStatus, $success);
        return [
            'messageid' => $messageId,
            'cost' => 0,
            'status' => $success ? 1 : (int) $value,
            'statustext' => $statustext,
            'sender' => $from,
            'receptor' => $receptor,
        ];
    }

    /**
     * Parse SendSMS response for multiple recipients (comma-separated Value = recIDs).
     *
     * @param  array<int, string>  $receptors
     * @return array<int, array{messageid?: int, cost?: int, status?: int, statustext?: string, sender?: string, receptor?: string}>
     */
    private function parseBulkResponse(array $json, array $receptors, string $from): array
    {
        $retStatus = (int) ($json['RetStatus'] ?? 0);
        $value = (string) ($json['Value'] ?? '');
        $strRetStatus = (string) ($json['StrRetStatus'] ?? '');
        $success = $retStatus === 1 && ($strRetStatus === 'Ok' || $strRetStatus === 'ok');
        $recIds = $success ? array_map('trim', explode(',', $value)) : [];
        $statustext = $this->resolveStatusText($value, $strRetStatus, $success);
        $results = [];
        foreach ($receptors as $i => $receptor) {
            $recId = $recIds[$i] ?? null;
            $results[] = [
                'messageid' => $recId !== null && is_numeric($recId) ? (int) $recId : 0,
                'cost' => 0,
                'status' => $success ? 1 : (int) $value,
                'statustext' => $statustext,
                'sender' => $from,
                'receptor' => $receptor,
            ];
        }
        return $results;
    }

    /**
     * Map Payamak Panel error codes to Persian messages.
     */
    private function resolveStatusText(mixed $value, string $strRetStatus, bool $success): string
    {
        if ($success) {
            return 'در صف ارسال';
        }
        $codes = [
            -111 => 'IP درخواست کننده نامعتبر است',
            -110 => 'الزام استفاده از ApiKey به جای رمز عبور',
            -109 => 'الزام تنظیم IP مجاز برای استفاده از API',
            -108 => 'مسدود شدن IP به دلیل تلاش ناموفق استفاده از API',
            0 => 'نام کاربری یا رمز عبور اشتباه می باشد',
            1 => 'درخواست با موفقیت انجام شد',
            2 => 'اعتبار کافی نمی باشد',
            3 => 'محدودیت در ارسال روزانه',
            4 => 'محدودیت در حجم ارسال',
            5 => 'شماره فرستنده معتبر نمی باشد',
            6 => 'سامانه در حال بروزرسانی می باشد',
            7 => 'متن حاوی کلمه فیلتر شده می باشد',
            9 => 'ارسال از خطوط عمومی از طریق وب سرویس امکان پذیر نمی باشد',
            10 => 'کاربر مورد نظر فعال نمی باشد',
            11 => 'ارسال نشده',
            12 => 'مدارک کاربر کامل نمی باشد',
            14 => 'متن حاوی لینک می باشد',
            15 => 'عدم وجود لغو11 در انتهای متن پیامک',
            16 => 'شماره گیرنده ای یافت نشد',
            17 => 'متن پیامک خالی می باشد',
            18 => 'شماره موبایل معتبر نمی باشد',
        ];
        $code = is_numeric((string) $value) ? (int) $value : 0;
        return $codes[$code] ?? $strRetStatus ?: 'خطای ناشناخته';
    }
}
