<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Mortezaa97\SmsManager\Http\Resources\SmsMessageResource;
use Mortezaa97\SmsManager\Models\SmsMessage;

class SmsMessageController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', SmsMessage::class);

        return SmsMessageResource::collection(SmsMessage::all());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', SmsMessage::class);
        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return new SmsMessageResource($smsMessage);
    }

    public function show(SmsMessage $smsMessage)
    {
        Gate::authorize('view', $smsMessage);

        return new SmsMessageResource($smsMessage);
    }

    public function update(Request $request, SmsMessage $smsMessage)
    {
        Gate::authorize('update', $smsMessage);
        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return new SmsMessageResource($smsMessage);
    }

    public function destroy(SmsMessage $smsMessage)
    {
        Gate::authorize('delete', $smsMessage);
        try {
            DB::beginTransaction();
            DB::commit();
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 419);
        }

        return response()->json('با موفقیت حذف شد');
    }
}
