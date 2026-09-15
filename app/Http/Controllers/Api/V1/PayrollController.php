<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Payroll\CalculateNetSalaryAction;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculatePayrollRequest;
use App\Http\Resources\PayrollCalculationResource;
use App\Models\Institution;

class PayrollController extends Controller
{
    use ApiResponse;

    public function calculate(
        CalculatePayrollRequest $request,
        Institution $institution,
        CalculateNetSalaryAction $action,
    ) {
        $this->authorize('view', $institution);

        $result = $action->execute(
            $institution,
            (float) $request->validated('gross_salary'),
            $request->validated('reference_date'),
        );

        return $this->success(
            (new PayrollCalculationResource($result))->toArray($request),
        );
    }
}
