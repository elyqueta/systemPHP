# Fase 1 — Schema Core em PostgreSQL (identificadores em inglês)

Objectivo: portar o módulo `01_core` do KwanzaFolha Cloud
(`utilizadores`, `empresas`, `empresa_config_fiscal`,
`empresa_contas_bancarias`, `utilizador_empresa`) para migrations
Eloquent em PostgreSQL, com **todos os nomes de tabelas, colunas,
Models e rotas em inglês** (decisão de 12/09/2026 — ver
`docs/00-decisoes-revisadas.md`, secção 6), e com a arquitectura
híbrida de IDs.

Comentários de código e mensagens continuam em português. Nenhuma
lógica de payroll (IRT/INSS) entra aqui — só o núcleo institucional e
fiscal, tal como o `01_core` original.

## 1. O trait `HasUuid`

Criar `app/Traits/HasUuid.php`:

```php
<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
```

Porque isto e não uma coluna gerada pelo Postgres (`gen_random_uuid()`
como no KwanzaFolha): em Eloquent é mais idiomático gerar o UUID em PHP
no evento `creating`, porque assim o valor já está disponível no objecto
imediatamente após `Model::create()`, sem precisar de um `refresh()`.

`getRouteKeyName()` é o que faz o Laravel resolver
`Route::apiResource('institutions', ...)` pelo `uuid` em vez do `id`
automaticamente, em qualquer Controller que use route-model-binding.

## 2. Estender a tabela `users` nativa (em vez de criar `utilizadores`)

Como decidido na secção 6 de `docs/00-decisoes-revisadas.md`, não
criamos uma tabela `Utilizador` customizada. A migration nativa
`0001_01_01_000000_create_users_table.php` já existe e já tem `id`,
`name`, `email`, `password`, timestamps. Só falta acrescentar:

```bash
php artisan make:migration add_system_fields_to_users_table
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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->boolean('active')->default(true)->after('password');
            $table->timestamp('last_login_at')->nullable()->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'active', 'last_login_at']);
        });
    }
};
```

Isto é uma migration **aditiva**, não uma reescrita — respeita a regra
de nunca editar uma migration nativa já existente.

## 3. Migration: `institutions`

```bash
php artisan make:migration create_institutions_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name', 200);
            $table->string('commercial_name', 200)->nullable();
            $table->string('tax_id', 20)->unique()->nullable();
            $table->string('institution_type', 10)->default('LDA');

            $table->date('founding_date')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 200)->nullable();

            $table->string('address', 250)->nullable();
            $table->string('neighborhood', 150)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedInteger('province_id')->nullable();
            $table->unsignedInteger('municipality_id')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index('active');
        });

        // Eloquent não tem helper nativo para CHECK constraints,
        // por isso usamos DB::statement — mesma abordagem do
        // KwanzaFolha em SQL cru.
        DB::statement("
            ALTER TABLE institutions
            ADD CONSTRAINT chk_institutions_type
            CHECK (institution_type IN ('LDA', 'SA', 'ENI', 'ONG', 'EP', 'OUTRO'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
```

Os valores possíveis de `institution_type` continuam siglas legais
angolanas (LDA, SA, ENI, ONG, EP, OUTRO) — isto é dado de domínio, não
identificador de código, por isso não se traduz.

`province_id`/`municipality_id` ficam sem `REFERENCES` por agora, tal
como no KwanzaFolha original — essas tabelas de referência ainda não
existem nesta fase.

## 4. Migrations: `institution_tax_configurations` e `institution_bank_accounts`

```php
Schema::create('institution_tax_configurations', function (Blueprint $table) {
    $table->foreignId('institution_id')
        ->primary()
        ->constrained('institutions')
        ->cascadeOnDelete();

    $table->decimal('employee_social_security_rate', 5, 2)->default(3.00);
    $table->decimal('employer_social_security_rate', 5, 2)->default(8.00);
    $table->decimal('meal_allowance', 12, 2)->default(15000.00);
    $table->decimal('transport_allowance', 12, 2)->default(10000.00);
    $table->unsignedInteger('currency_id')->nullable();
    $table->string('tax_regime', 50)->default('Geral');

    $table->timestamp('updated_at')->useCurrent();
});
```

```php
Schema::create('institution_bank_accounts', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('institution_id')
        ->constrained('institutions')
        ->cascadeOnDelete();

    $table->unsignedInteger('bank_id')->nullable();
    $table->string('bank_name', 150)->nullable();
    $table->string('account_number', 50);
    $table->string('iban', 34)->nullable();
    $table->unsignedInteger('currency_id')->nullable();
    $table->boolean('is_primary')->default(false);

    $table->timestamp('created_at')->useCurrent();

    $table->index('institution_id');
});

// Índice único parcial, igual ao KwanzaFolha:
DB::statement('
    CREATE UNIQUE INDEX idx_one_primary_account_per_institution
    ON institution_bank_accounts (institution_id)
    WHERE is_primary = TRUE
');
```

Nota: `tax_regime` fica com o valor `'Geral'` em português de propósito
— é um dado de domínio fiscal angolano (o próprio regime chama-se
"Geral" na legislação), não um identificador de código.

## 5. Migration: `institution_user` (pivot)

```php
Schema::create('institution_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
    $table->boolean('active')->default(true);
    $table->timestamp('created_at')->useCurrent();

    $table->primary(['user_id', 'institution_id']);
});
```

Convenção Laravel: o nome da pivot table usa a ordem alfabética dos
dois Models relacionados (`institution` antes de `user`) — na
verdade seria `institution_user` mesmo assim, então não há ambiguidade
aqui.

## 6. Models Eloquent

`app/Models/Institution.php`:

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Institution extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'commercial_name', 'tax_id', 'institution_type',
        'founding_date', 'phone', 'email', 'website',
        'address', 'neighborhood', 'city', 'province_id', 'municipality_id',
    ];

    public function taxConfiguration(): HasOne
    {
        return $this->hasOne(InstitutionTaxConfiguration::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(InstitutionBankAccount::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'institution_user')
            ->withPivot('active')
            ->withTimestamps(false);
    }
}
```

`app/Models/InstitutionTaxConfiguration.php` e
`app/Models/InstitutionBankAccount.php` seguem o mesmo padrão —
`InstitutionBankAccount` usa `HasUuid`, `InstitutionTaxConfiguration`
não (não tem `uuid` próprio, tal como no KwanzaFolha — nunca é acedida
directamente por ID, só via `/institutions/{uuid}/tax-configuration`).

O Model `User` nativo (`app/Models/User.php`) ganha o trait `HasUuid` e
a relação inversa — detalhes na Fase 2, onde também entra o Sanctum.

## Checkpoint

```bash
php artisan migrate:fresh
php artisan tinker --execute="
  \$i = App\Models\Institution::create(['name' => 'Kwanza Tech Lda', 'tax_id' => '5000000001']);
  echo \$i->uuid . PHP_EOL;
  echo \$i->id . PHP_EOL;
"
```

Confirmar no output: um UUID válido e um `id` numérico pequeno (`1`).

Verificação extra opcional:

```bash
docker compose exec db psql -U systemphp_app -d systemphp -c "\d institutions"
```

Deve listar colunas em inglês: `id`, `uuid`, `name`, `tax_id`, etc.
