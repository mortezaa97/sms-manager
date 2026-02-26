<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Mortezaa97\SmsManager\Http\Resources\SmsBlacklistResource;
use Mortezaa97\SmsManager\Models\SmsBlacklist;

class SmsBlacklistController
{
    public function index()
    {
        Gate::authorize('viewAny', SmsBlacklist::class);

        return SmsBlacklistResource::collection(SmsBlacklist::all());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', SmsBlacklist::class);

        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return new SmsBlacklistResource($smsBlacklist);
    }

    public function show(SmsBlacklist $smsBlacklist)
    {
        Gate::authorize('view', $smsBlacklist);

        return new SmsBlacklistResource($smsBlacklist);
    }

    public function update(Request $request, SmsBlacklist $smsBlacklist)
    {
        Gate::authorize('update', $smsBlacklist);

        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return new SmsBlacklistResource($smsBlacklist);
    }

    public function destroy(SmsBlacklist $smsBlacklist)
    {
        Gate::authorize('delete', $smsBlacklist);

        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return response()->json('با موفقیت حذف شد');
    }
}
