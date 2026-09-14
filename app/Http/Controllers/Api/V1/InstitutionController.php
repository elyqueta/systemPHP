<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Institutions\CreateBankAccountAction;
use App\Actions\Institutions\CreateInstitutionAction;
use App\Actions\Institutions\UpdateInstitutionAction;
use App\Actions\Institutions\UpdateTaxConfigurationAction;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBankAccountRequest;
use App\Http\Requests\CreateInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Http\Requests\UpdateTaxConfigurationRequest;
use App\Http\Resources\InstitutionBankAccountCollection;
use App\Http\Resources\InstitutionBankAccountResource;
use App\Http\Resources\InstitutionCollection;
use App\Http\Resources\InstitutionResource;
use App\Http\Resources\InstitutionTaxConfigurationResource;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Institution::class);

        return $this->success(
            (new InstitutionCollection(
                Institution::orderBy('name')->paginate(20)
            ))->toArray($request)
        );
    }

    public function store(CreateInstitutionRequest $request, CreateInstitutionAction $action)
    {
        $institution = $action->execute($request->validated());

        return $this->success(
            (new InstitutionResource($institution))->toArray($request),
            201
        );
    }

    public function show(Request $request, Institution $institution)
    {
        $this->authorize('view', $institution);

        return $this->success(
            (new InstitutionResource($institution))->toArray($request)
        );
    }

    public function update(UpdateInstitutionRequest $request, Institution $institution, UpdateInstitutionAction $action)
    {
        $this->authorize('update', $institution);

        $institution = $action->execute($institution, $request->validated());

        return $this->success(
            (new InstitutionResource($institution))->toArray($request)
        );
    }

    public function showTaxConfiguration(Request $request, Institution $institution)
    {
        $this->authorize('view', $institution);

        $config = $institution->taxConfiguration()->first();

        if (! $config) {
            return $this->success([
                'employee_social_security_rate' => null,
                'employer_social_security_rate' => null,
                'meal_allowance' => null,
                'transport_allowance' => null,
                'currency_id' => null,
                'tax_regime' => null,
            ]);
        }

        return $this->success(
            (new InstitutionTaxConfigurationResource($config))->toArray($request)
        );
    }

    public function updateTaxConfiguration(
        UpdateTaxConfigurationRequest $request,
        Institution $institution,
        UpdateTaxConfigurationAction $action,
    ) {
        $this->authorize('update', $institution);

        $config = $action->execute($institution, $request->validated());

        return $this->success(
            (new InstitutionTaxConfigurationResource($config))->toArray($request)
        );
    }

    public function listBankAccounts(Request $request, Institution $institution)
    {
        $this->authorize('view', $institution);

        return $this->success(
            (new InstitutionBankAccountCollection(
                $institution->bankAccounts()->paginate(20)
            ))->toArray($request)
        );
    }

    public function createBankAccount(
        CreateBankAccountRequest $request,
        Institution $institution,
        CreateBankAccountAction $action,
    ) {
        $this->authorize('update', $institution);

        $account = $action->execute($institution, $request->validated());

        return $this->success(
            (new InstitutionBankAccountResource($account))->toArray($request),
            201
        );
    }

    public function activate(Request $request, Institution $institution)
    {
        $this->authorize('activate', $institution);

        $institution->update(['active' => true]);

        return $this->success([
            'active' => true,
            'message' => 'Instituição ativada com sucesso.',
        ]);
    }

    public function deactivate(Request $request, Institution $institution)
    {
        $this->authorize('deactivate', $institution);

        $institution->update(['active' => false]);

        return $this->success([
            'active' => false,
            'message' => 'Instituição desativada com sucesso.',
        ]);
    }
}
