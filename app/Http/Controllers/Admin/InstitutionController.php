<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function overview()
    {
        $this->authorize('viewAny', Institution::class);

        $institutions = Institution::withCount('bankAccounts')
            ->with('taxConfiguration')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.overview', [
            'institutions' => $institutions,
            'stats' => [
                'institutions' => Institution::count(),
                'bank_accounts' => \App\Models\InstitutionBankAccount::count(),
                'tax_configs' => \App\Models\InstitutionTaxConfiguration::count(),
            ],
        ]);
    }

    public function index()
    {
        $this->authorize('viewAny', Institution::class);

        return view('admin.institutions.index', [
            'institutions' => Institution::orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Institution::class);

        return view('admin.institutions.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Institution::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'commercial_name' => ['nullable', 'string', 'max:200'],
            'tax_id' => ['required', 'string', 'max:20', 'unique:institutions,tax_id'],
            'institution_type' => ['required', 'in:LDA,SA,ENI,ONG,EP,OUTRO'],
            'founding_date' => ['nullable', 'date_format:Y-m-d'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'address' => ['nullable', 'string', 'max:250'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'province_id' => ['nullable', 'integer'],
            'municipality_id' => ['nullable', 'integer'],
        ]);

        Institution::create($validated);

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Instituição criada com sucesso.');
    }

    public function activate(Request $request, Institution $institution)
    {
        $this->authorize('activate', $institution);

        $institution->update(['active' => true]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Instituição ativada com sucesso.',
                'active' => true,
            ]);
        }

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Instituição ativada com sucesso.');
    }

    public function deactivate(Request $request, Institution $institution)
    {
        $this->authorize('deactivate', $institution);

        $institution->update(['active' => false]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Instituição desativada com sucesso.',
                'active' => false,
            ]);
        }

        return redirect()->route('admin.institutions.index')
            ->with('success', 'Instituição desativada com sucesso.');
    }

    public function show(Request $request, Institution $institution)
    {
        $this->authorize('view', $institution);

        $institution->load(['taxConfiguration', 'bankAccounts', 'users']);

        return view('admin.institutions.show', [
            'institution' => $institution,
        ]);
    }
}
