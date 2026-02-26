<?php

declare(strict_types=1);

namespace Mortezaa97\Brands\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
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
