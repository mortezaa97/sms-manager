<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Filament\Widgets;

use App\Enums\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mortezaa97\SmsManager\Models\SmsMessage;

class SmsManagerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Count overall totals
        $sentQuery = SmsMessage::where('status', Status::SENT->value);
        $total = SmsMessage::count();
        $sent = $sentQuery->count();
        $failed = SmsMessage::where('status', Status::FAILED->value)->count();

        // Calculate success rate
        $successRate = $total > 0 ? round(($sent / $total) * 100) : 0;

        // Placeholder: Fetch actual SMS credit from provider here if possible
        $remainingCredit = number_format(100000 - ($sentQuery->sum('cost')/10)) . ' تومان';

        // Generate past 7 days' dates (including today), oldest first
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->copy()->subDays($i)->toDateString());
        }

        $sentPerDay = SmsMessage::withoutGlobalScope('order')
            ->selectRaw("DATE(created_at) as day, count(*) as count")
            ->where('status', Status::SENT->value)
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day')
            ->pluck('count', 'day');

        $failedPerDay = SmsMessage::withoutGlobalScope('order')
            ->selectRaw("DATE(created_at) as day, count(*) as count")
            ->where('status', Status::FAILED->value)
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day')
            ->pluck('count', 'day');

        $totalPerDay = SmsMessage::withoutGlobalScope('order')
            ->selectRaw("DATE(created_at) as day, count(*) as count")
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->orderBy('day')
            ->pluck('count', 'day');

        $chartSent = [];
        $chartFailed = [];
        $chartTotal = [];
        foreach ($dates as $date) {
            $chartSent[] = $sentPerDay[$date] ?? 0;
            $chartFailed[] = $failedPerDay[$date] ?? 0;
            $chartTotal[] = $totalPerDay[$date] ?? 0;
        }

        $stats = [];

        // Checking for role in a more robust way to avoid possible lint warnings.
        $user = Auth::user();
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            $stats[] = Stat::make('کل پیامک ها', number_format($total))
                ->description('جمع کل پیامک‌های ارسال شده')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('primary')
                ->chart($chartTotal);

            $stats[] = Stat::make('پیامک موفق', number_format($sent))
                ->description("{$successRate}% موفقیت")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($chartSent);

            $stats[] = Stat::make('پیامک ناموفق', number_format($failed))
                ->description('تعداد ارسال ناموفق')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart($chartFailed);

            $stats[] = Stat::make('شارژ باقی‌مانده', $remainingCredit)
                ->description('موجودی پنل پیامک')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info');
        }

        return $stats;
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
