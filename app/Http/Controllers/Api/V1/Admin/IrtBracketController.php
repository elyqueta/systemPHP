<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreIrtBracketRequest;
use App\Http\Requests\Admin\UpdateIrtBracketRequest;
use App\Http\Resources\Admin\IrtBracketResource;
use App\Models\TaxIrtBracket;
use App\Traits\LogsAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class IrtBracketController extends Controller
{
    use ApiResponse, LogsAudit;

    public function index(): JsonResponse
    {
        $brackets = TaxIrtBracket::orderBy('effective_from')
            ->orderBy('bracket_order')
            ->get();

        return $this->success(
            IrtBracketResource::collection($brackets)->toArray(request())
        );
    }

    public function store(StoreIrtBracketRequest $request): JsonResponse
    {
        $data = $request->validated();

        $bracket = TaxIrtBracket::create($data);

        $this->logAudit('create', $bracket, null, $bracket->toArray(), $request);

        return $this->success(
            (new IrtBracketResource($bracket))->toArray($request),
            201
        );
    }

    public function show(TaxIrtBracket $irtBracket): JsonResponse
    {
        return $this->success(
            (new IrtBracketResource($irtBracket))->toArray(request())
        );
    }

    public function update(UpdateIrtBracketRequest $request, TaxIrtBracket $irtBracket): JsonResponse
    {
        $oldValues = $irtBracket->toArray();

        $irtBracket->update($request->validated());

        $this->logAudit('update', $irtBracket, $oldValues, $irtBracket->toArray(), $request);

        return $this->success(
            (new IrtBracketResource($irtBracket))->toArray($request)
        );
    }

    public function destroy(TaxIrtBracket $irtBracket): JsonResponse
    {
        $oldValues = $irtBracket->toArray();

        $irtBracket->delete();

        $this->logAudit('delete', $irtBracket, $oldValues, null, request());

        return $this->success(null, 204);
    }
}
