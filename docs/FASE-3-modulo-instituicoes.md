# Fase 3 — Módulo Institutions (CRUD completo)

Objectivo: replicar, em Laravel, o que o KwanzaFolha já tem funcional e
testado no módulo `empresas` (listar, criar, editar, config fiscal,
contas bancárias), agora com Policies em vez do middleware
`verificarAcessoEmpresa`, com identificadores em inglês, e com
Scramble a gerar a documentação automaticamente.

## 1. Policy: `InstitutionPolicy`

```bash
php artisan make:policy InstitutionPolicy --model=Institution
```

```php
<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Institution $institution): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->institutions()
            ->where('institutions.id', $institution->id)
            ->wherePivot('active', true)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Institution $institution): bool
    {
        return $user->hasRole('super_admin')
            || ($this->view($user, $institution) && $user->hasRole('institution_manager'));
    }
}
```

Registar em `app/Providers/AppServiceProvider.php`:

```php
use App\Models\Institution;
use App\Policies\InstitutionPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Institution::class, InstitutionPolicy::class);
}
```

## 2. Form Requests

`app/Http/Requests/CreateInstitutionRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Institution::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:200'],
            'commercial_name' => ['nullable', 'string', 'max:200'],
            'tax_id' => ['nullable', 'string', 'max:20', 'unique:institutions,tax_id'],
            'institution_type' => [Rule::in(['LDA', 'SA', 'ENI', 'ONG', 'EP', 'OUTRO'])],
            'founding_date' => ['nullable', 'date_format:Y-m-d'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'address' => ['nullable', 'string', 'max:250'],
            'neighborhood' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
        ];
    }
}
```

`UpdateInstitutionRequest` é igual, mas com todas as regras `sometimes`
em vez de `required`, e `authorize()` a chamar `can('update', ...)`.

`UpdateTaxConfigurationRequest`:

```php
public function rules(): array
{
    return [
        'employee_social_security_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        'employer_social_security_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
        'meal_allowance' => ['sometimes', 'numeric', 'min:0'],
        'transport_allowance' => ['sometimes', 'numeric', 'min:0'],
        'currency_id' => ['sometimes', 'integer'],
        'tax_regime' => ['sometimes', 'string', 'max:50'],
    ];
}
```

As mensagens de validação por omissão do Laravel já saem em português
se `APP_LOCALE=pt` estiver definido no `.env` (o pacote de traduções
`lang/pt` precisa de ser publicado — `php artisan lang:publish` — isto
fica como tarefa desta fase, não é automático de fábrica).

## 3. Actions

`app/Actions/Institutions/CreateInstitutionAction.php`:

```php
<?php

namespace App\Actions\Institutions;

use App\Models\Institution;

class CreateInstitutionAction
{
    public function execute(array $data): Institution
    {
        return Institution::create($data);
    }
}
```

`app/Actions/Institutions/UpdateTaxConfigurationAction.php`:

```php
<?php

namespace App\Actions\Institutions;

use App\Models\Institution;
use App\Models\InstitutionTaxConfiguration;

class UpdateTaxConfigurationAction
{
    public function execute(Institution $institution, array $data): InstitutionTaxConfiguration
    {
        return $institution->taxConfiguration()->updateOrCreate(
            ['institution_id' => $institution->id],
            $data,
        );
    }
}
```

`updateOrCreate` substitui directamente o `ON CONFLICT (empresa_id) DO
UPDATE` do KwanzaFolha — mesmo comportamento de upsert, via API do
Eloquent em vez de SQL cru. `UpdateInstitutionAction` e
`CreateBankAccountAction` seguem o mesmo padrão de uma classe por
operação.

## 4. API Resources

`app/Http/Resources/InstitutionResource.php`:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'commercial_name' => $this->commercial_name,
            'tax_id' => $this->tax_id,
            'institution_type' => $this->institution_type,
            'city' => $this->city,
            'active' => $this->active,
        ];
    }
}
```

`InstitutionTaxConfigurationResource` e `InstitutionBankAccountResource`
seguem o mesmo espírito — nunca `id` interno, sempre `uuid` onde a
tabela tiver um.

## 5. Controller fino

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Institutions\CreateInstitutionAction;
use App\Actions\Institutions\UpdateTaxConfigurationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInstitutionRequest;
use App\Http\Requests\UpdateTaxConfigurationRequest;
use App\Http\Resources\InstitutionResource;
use App\Http\Resources\InstitutionTaxConfigurationResource;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Institution::class);

        return InstitutionResource::collection(
            Institution::orderBy('name')->paginate(20)
        );
    }

    public function store(CreateInstitutionRequest $request, CreateInstitutionAction $action)
    {
        $institution = $action->execute($request->validated());

        return (new InstitutionResource($institution))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Institution $institution)
    {
        $this->authorize('view', $institution);

        return new InstitutionResource($institution);
    }

    public function updateTaxConfiguration(
        UpdateTaxConfigurationRequest $request,
        Institution $institution,
        UpdateTaxConfigurationAction $action,
    ) {
        $this->authorize('update', $institution);

        $config = $action->execute($institution, $request->validated());

        return new InstitutionTaxConfigurationResource($config);
    }
}
```

Paginação obrigatória (`->paginate(20)`) já desde o primeiro endpoint de
listagem — o roadmap técnico pede isto explicitamente, e é uma melhoria
deliberada em relação ao KwanzaFolha original (que devolvia tudo de
uma vez).

## 6. Rotas

```php
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('institutions', InstitutionController::class)
        ->only(['index', 'store', 'show', 'update']);

    Route::get('/institutions/{institution}/tax-configuration', [InstitutionController::class, 'showTaxConfiguration']);
    Route::put('/institutions/{institution}/tax-configuration', [InstitutionController::class, 'updateTaxConfiguration']);

    Route::get('/institutions/{institution}/bank-accounts', [InstitutionController::class, 'listBankAccounts']);
    Route::post('/institutions/{institution}/bank-accounts', [InstitutionController::class, 'createBankAccount']);
});
```

Como `Institution` usa `HasUuid` com `getRouteKeyName()` a devolver
`uuid`, o `{institution}` na rota já resolve automaticamente pelo UUID.

## 7. Documentação automática (Scramble)

Instalado na Fase 0. Não precisa de nenhuma anotação manual nas rotas.
Confirmar em `config/scramble.php` que `api_path` aponta para `api` e
aceder a `/docs/api` no browser depois de `php artisan serve`.

## 8. Testes Pest

```bash
php artisan pest --init
```

`tests/Feature/InstitutionTest.php`:

```php
<?php

use App\Models\Institution;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('permite ao super admin criar uma instituição', function () {
    Role::firstOrCreate(['name' => 'super_admin']);
    $admin = User::factory()->create()->assignRole('super_admin');

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/institutions', [
        'name' => 'Kwanza Tech Lda',
        'tax_id' => '5417896321',
    ]);

    $response->assertCreated();
    expect(Institution::where('tax_id', '5417896321')->exists())->toBeTrue();
});

it('recusa criar instituição sem permissão', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/institutions', [
        'name' => 'Tentativa Não Autorizada',
    ]);

    $response->assertForbidden();
});
```

Como continuamos a usar a `UserFactory` nativa (não foi preciso criar
uma nova, ao contrário do que a versão anterior deste plano previa),
este teste corre sem nenhuma alteração adicional de setup.

## Checkpoint

```bash
php artisan test
php artisan pint --test
./vendor/bin/phpstan analyse
```

Todos os testes de `InstitutionTest` devem passar, Pint sem ficheiros
por formatar, PHPStan sem erros.
