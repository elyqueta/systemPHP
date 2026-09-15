# Fase 5 — Módulo de Payroll (Cálculo de IRT e INSS)

Objectivo: calcular o salário líquido de um colaborador a partir do
salário bruto, aplicando primeiro o desconto de INSS (taxas já
existentes em `institution_tax_configurations`) e depois o IRT
(tabela de escalões progressivos), expondo o resultado via um
endpoint da API.

Este documento resolve o bloqueio registado em
`docs/00-decisoes-revisadas.md`, secção 7. Os três pontos pendentes
foram confirmados:

1. **Tabela de escalões** — confirmada byte a byte contra o Anexo I
   (referente ao art. 21.º) da Lei n.º 14/25, de 30 de Dezembro de
   2025 (OGE 2026). A aparente inconsistência entre o 1.º e o 2.º
   escalão **não é erro de transcrição** — é assim na própria lei; a
   partir do 3.º escalão a fórmula cumulativa fecha exactamente.
2. **Data de vigência** — 1 de Janeiro de 2026 (ano fiscal do OGE 2026).
3. **Taxas de INSS** — confirmado que se mantêm 3% (trabalhador) / 8%
   (entidade), os mesmos valores já usados como default em
   `institution_tax_configurations`.

Decisões tomadas para esta fase (confirmadas pelo Ely):

- O cálculo fica exposto num endpoint da API
  (`POST /institutions/{institution}/payroll/calculate`), não fica só
  como Action interna.
- A tabela de escalões do IRT **não fica fixa em código** — fica numa
  tabela própria na base de dados, com estrutura pronta para no
  futuro o Admin da Plataforma a poder gerir (criar uma nova versão
  quando a lei mudar, sem apagar o histórico). Nesta fase criamos a
  tabela, o seed com os valores actuais, e a leitura para cálculo.
  **CRUD administrativo para editar escalões fica para uma fase
  posterior** — não construir agora, para não alargar o âmbito desta
  fase.

## 1. Migration: `tax_irt_brackets`

```bash
php artisan make:migration create_tax_irt_brackets_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_irt_brackets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Data a partir da qual esta versão da tabela é aplicada.
            // Permite ter mais de uma versão ao longo do tempo sem apagar
            // histórico (essencial: um payslip antigo tem de continuar a
            // usar a tabela que estava em vigor na altura em que foi
            // processado, mesmo que a lei mude depois).
            $table->date('effective_from');

            // Ordem do escalão dentro da tabela em vigor nessa data
            // (1 a 11 na tabela actual). Não é a chave primária porque
            // o mesmo número de escalão existe em várias versões.
            $table->unsignedTinyInteger('bracket_order');

            $table->decimal('lower_bound', 14, 2);
            // Nulo apenas no último escalão de cada versão (sem tecto).
            $table->decimal('upper_bound', 14, 2)->nullable();

            $table->decimal('rate', 5, 2);
            $table->decimal('fixed_amount', 14, 2);

            $table->timestamps();

            $table->unique(['effective_from', 'bracket_order']);
            $table->index('effective_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_irt_brackets');
    }
};
```

Porque `effective_from` + `bracket_order` e não só um `id` sequencial:
isto é o que deixa a porta aberta para o Admin da Plataforma, no
futuro, criar uma nova versão completa da tabela (nova
`effective_from`) sem tocar na versão anterior — nenhum payslip já
processado muda de valor retroactivamente.

## 2. Model `TaxIrtBracket`

`app/Models/TaxIrtBracket.php`:

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaxIrtBracket extends Model
{
    use HasUuid;

    protected $fillable = [
        'effective_from',
        'bracket_order',
        'lower_bound',
        'upper_bound',
        'rate',
        'fixed_amount',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'lower_bound' => 'decimal:2',
            'upper_bound' => 'decimal:2',
            'rate' => 'decimal:2',
            'fixed_amount' => 'decimal:2',
        ];
    }

    // Devolve apenas a versão da tabela em vigor numa determinada data
    // (por omissão, hoje). Nunca misturar escalões de versões diferentes.
    public function scopeEffectiveOn(Builder $query, \DateTimeInterface|string|null $date = null): Builder
    {
        $date = $date ?? now();

        $effectiveFrom = static::query()
            ->where('effective_from', '<=', $date)
            ->max('effective_from');

        return $query->where('effective_from', $effectiveFrom);
    }
}
```

## 3. Seeder com a tabela actual (Lei n.º 14/25, em vigor desde 01/01/2026)

```bash
php artisan make:seeder TaxIrtBracketSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\TaxIrtBracket;
use Illuminate\Database\Seeder;

class TaxIrtBracketSeeder extends Seeder
{
    public function run(): void
    {
        $effectiveFrom = '2026-01-01';

        // Valores conferidos contra o Anexo I (art. 21.º) da Lei n.º 14/25,
        // de 30 de Dezembro de 2025 (OGE 2026). Não alterar sem confirmar
        // primeiro a fonte oficial (Diário da República).
        $brackets = [
            [1,        0,        150_000, 0.00,  0],
            [2,  150_000,        200_000, 16.00, 12_500],
            [3,  200_000,        300_000, 18.00, 31_250],
            [4,  300_000,        500_000, 19.00, 49_250],
            [5,  500_000,      1_000_000, 20.00, 87_250],
            [6,  1_000_000,    1_500_000, 21.00, 187_250],
            [7,  1_500_000,    2_000_000, 22.00, 292_250],
            [8,  2_000_000,    2_500_000, 23.00, 402_250],
            [9,  2_500_000,    5_000_000, 24.00, 517_250],
            [10, 5_000_000,   10_000_000, 24.50, 1_117_250],
            [11, 10_000_000,  null,       25.00, 2_342_250],
        ];

        foreach ($brackets as [$order, $lower, $upper, $rate, $fixed]) {
            TaxIrtBracket::updateOrCreate(
                ['effective_from' => $effectiveFrom, 'bracket_order' => $order],
                [
                    'lower_bound' => $lower,
                    'upper_bound' => $upper,
                    'rate' => $rate,
                    'fixed_amount' => $fixed,
                ],
            );
        }
    }
}
```

Registar em `DatabaseSeeder`:

```php
$this->call([
    SuperAdminSeeder::class,
    DemoDataSeeder::class,
    TaxIrtBracketSeeder::class,
]);
```

## 4. Action: `CalculateNetSalaryAction`

Esta é a peça central. Segue a ordem legalmente correcta: primeiro
desconta-se o INSS do trabalhador, depois calcula-se o IRT sobre o
valor já líquido de INSS. O INSS da entidade (8%) é um custo da
empresa, não é descontado ao trabalhador.

`app/Actions/Payroll/CalculateNetSalaryAction.php`:

```php
<?php

namespace App\Actions\Payroll;

use App\Models\Institution;
use App\Models\TaxIrtBracket;
use RuntimeException;

class CalculateNetSalaryAction
{
    public function execute(Institution $institution, float $grossSalary, ?string $referenceDate = null): array
    {
        $taxConfig = $institution->taxConfiguration;

        // Resolve o bug das "configurações fantasma": se a instituição
        // ainda não tem configuração fiscal própria, usa os valores por
        // omissão em vez de rebentar com um erro de atributo nulo.
        $employeeRate = (float) ($taxConfig->employee_social_security_rate ?? 3.00);
        $employerRate = (float) ($taxConfig->employer_social_security_rate ?? 8.00);

        $employeeSocialSecurity = round($grossSalary * $employeeRate / 100, 2);
        $employerSocialSecurity = round($grossSalary * $employerRate / 100, 2);

        // Base tributável de IRT = salário bruto menos o INSS do trabalhador.
        $taxableIncome = round($grossSalary - $employeeSocialSecurity, 2);

        $bracket = TaxIrtBracket::query()
            ->effectiveOn($referenceDate)
            ->where('lower_bound', '<', $taxableIncome)
            ->where(function ($query) use ($taxableIncome) {
                $query->whereNull('upper_bound')
                    ->orWhere('upper_bound', '>=', $taxableIncome);
            })
            ->first();

        if (! $bracket) {
            throw new RuntimeException('Não foi possível determinar o escalão de IRT aplicável.');
        }

        $irtAmount = round(
            (float) $bracket->fixed_amount + ($taxableIncome - (float) $bracket->lower_bound) * ((float) $bracket->rate / 100),
            2,
        );

        $netSalary = round($grossSalary - $employeeSocialSecurity - $irtAmount, 2);

        return [
            'gross_salary' => round($grossSalary, 2),
            'employee_social_security' => $employeeSocialSecurity,
            'employer_social_security' => $employerSocialSecurity,
            'taxable_income' => $taxableIncome,
            'irt_bracket_order' => $bracket->bracket_order,
            'irt_rate' => (float) $bracket->rate,
            'irt_amount' => $irtAmount,
            'net_salary' => $netSalary,
        ];
    }
}
```

Nota de arredondamento: cada desconto é arredondado a 2 casas decimais
antes de ser usado no passo seguinte (e não só no fim), porque é assim
que a AGT espera ver o valor em cada linha do recibo de vencimento.

## 5. Form Request

`app/Http/Requests/CalculatePayrollRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gross_salary' => ['required', 'numeric', 'min:0'],
            'reference_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
```

## 6. Resource

`app/Http/Resources/PayrollCalculationResource.php`:

```php
<?php

namespace App\Http\Resources;

class PayrollCalculationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'gross_salary' => $this['gross_salary'],
            'employee_social_security' => $this['employee_social_security'],
            'employer_social_security' => $this['employer_social_security'],
            'taxable_income' => $this['taxable_income'],
            'irt_bracket_order' => $this['irt_bracket_order'],
            'irt_rate' => $this['irt_rate'],
            'irt_amount' => $this['irt_amount'],
            'net_salary' => $this['net_salary'],
        ];
    }
}
```

`PayrollCalculationResource` recebe um array (o retorno da Action), não
um Model — por isso `BaseResource` (que já estende `JsonResource`)
funciona directamente com `new PayrollCalculationResource($resultado)`.

## 7. Controller

`app/Http/Controllers/Api/V1/PayrollController.php`:

```php
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
```

Usa `authorize('view', $institution)` (já existente na
`InstitutionPolicy`) porque simular a folha de uma instituição exige,
no mínimo, o mesmo nível de acesso que consultá-la.

## 8. Rota

Adicionar em `routes/api.php`, dentro do grupo `auth:sanctum`:

```php
Route::post('/institutions/{institution}/payroll/calculate', [PayrollController::class, 'calculate'])
    ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
```

E o `use` correspondente no topo do ficheiro:

```php
use App\Http\Controllers\Api\V1\PayrollController;
```

## 9. Actualizar a documentação de decisões

Em `docs/00-decisoes-revisadas.md`, secção 7, acrescentar no final:

```markdown
**Actualização de 14/09/2026**: os três pontos pendentes foram
confirmados. A tabela do IRT foi validada byte a byte contra o Anexo I
(art. 21.º) da Lei n.º 14/25, de 30 de Dezembro de 2025 (OGE 2026),
em vigor desde 01/01/2026. A aparente inconsistência entre o 1.º e o
2.º escalão é do próprio legislador, não um erro de transcrição — a
partir do 3.º escalão a fórmula cumulativa fecha exactamente. As taxas
de INSS mantêm-se em 3%/8%. A Fase 5 está descrita em
`docs/fases/FASE-5-modulo-payroll-irt-inss.md`.
```

## Checkpoint

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\TaxIrtBracketSeeder
php artisan serve
```

Teste manual (substituir `{uuid}` e `{token}`):

```bash
curl -X POST http://localhost:8000/api/v1/institutions/{uuid}/payroll/calculate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"gross_salary": 350000}'
```

Para um salário bruto de 350.000 Kz, com INSS 3%, o esperado é:
- `employee_social_security` = 10.500
- `taxable_income` = 339.500
- escalão aplicável: 4.º (300.000 a 500.000, taxa 19%, parcela fixa 49.250)
- `irt_amount` = 49.250 + (339.500 − 300.000) × 19% = 56.755
- `net_salary` = 350.000 − 10.500 − 56.755 = 282.745

Se o `curl` devolver estes valores, a Fase 5 está a funcionar
correctamente. Testes Pest (opcional, mas recomendado antes de dar por
fechada a fase):

```bash
php artisan make:test Payroll/CalculateNetSalaryActionTest --unit
```

Cobrir pelo menos: um salário no 1.º escalão (isento, `irt_amount` = 0),
um salário exactamente na fronteira de um escalão, e o caso acima.
