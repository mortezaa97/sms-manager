<?php

namespace Mortezaa97\SmsManager\Http\Controllers;

use Mortezaa97\SmsManager\Models\SmsMessage;
use Mortezaa97\SmsManager\Http\Resources\SmsMessageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(),419);
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
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(),419);
        }
        return new SmsMessageResource($smsMessage);
    }

    public function destroy(SmsMessage $smsMessage)
    {
        Gate::authorize('delete', $smsMessage);
        try {
            DB::beginTransaction();
            DB::commit();
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(),419);
        }
        return response()->json("با موفقیت حذف شد");
    }
}

