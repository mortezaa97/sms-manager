<?php

namespace Mortezaa97\Brands\Http\Controllers;

use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Mortezaa97\Brands\Http\Resources\BrandResource;
use Mortezaa97\Brands\Http\Resources\BrandSimpleResource;
use Mortezaa97\Brands\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Brand::class);
        return BrandSimpleResource::collection(Brand::all());
    }

    public function show(Brand $brand)
    {
        Gate::authorize('view', $brand);
        return new BrandResource($brand);
    }
}
