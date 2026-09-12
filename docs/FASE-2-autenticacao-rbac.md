# Fase 2 — Autenticação (Sanctum) e RBAC (Spatie Permission)

Objectivo: um utilizador conseguir autenticar-se via API e receber um
token Sanctum, e existir pelo menos um papel (`super_admin`) com
permissões geridas por `spatie/laravel-permission`, substituindo o
padrão de coluna `role` livre do KwanzaFolha.

Esta fase ficou mais simples do que a versão original planeada: como
decidimos usar a tabela `users` nativa do Laravel em vez de uma
`Utilizador` customizada, não há Model novo para criar — só se estende
o `User` que já existe.

## 1. Estender o Model `User` nativo

`app/Models/User.php` (ficheiro já existente, editar):

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuid, HasApiTokens, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_user')
            ->withPivot('active');
    }
}
```

Repara que só acrescentámos três traits (`HasUuid`, `HasApiTokens`,
`HasRoles`) e uma relação — nada do scaffold nativo foi removido. Isto
é o benefício directo da decisão de manter `users` em vez de criar um
Model paralelo.

`config/auth.php` **não precisa de nenhuma alteração** — já aponta
para `App\Models\User`.

## 2. Seed do primeiro Super Admin

```bash
php artisan make:seeder SuperAdminSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'institution_manager']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@system.ao'],
            [
                'name' => 'Administrador da Plataforma',
                'password' => Hash::make(config('app.seed_admin_password', 'ChangeMe123!')),
            ]
        );

        $admin->assignRole('super_admin');
    }
}
```

Nota de segurança: a password por omissão (`ChangeMe123!`) só serve
para desenvolvimento local. Antes de qualquer ambiente que não seja a
máquina do Ely, definir `SEED_ADMIN_PASSWORD` no `.env`.

## 3. Rotas de autenticação (`routes/api.php`)

```php
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
    });
});
```

## 4. `AuthController`, `LoginRequest`, `AuthenticateUserAction`, `UserResource`

`app/Http/Requests/LoginRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
```

`app/Actions/Auth/AuthenticateUserAction.php`:

```php
<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthenticateUserAction
{
    public function execute(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        // Mensagem genérica de propósito, igual ao padrão já usado no
        // KwanzaFolha: nunca revelar se foi o email ou a password que
        // falhou.
        if (! $user || ! $user->active || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Credenciais inválidas.');
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
```

`app/Http/Controllers/Api/V1/AuthController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\AuthenticateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthenticateUserAction $action)
    {
        $result = $action->execute(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'token' => $result['token'],
            'user' => new UserResource($result['user']),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
```

`app/Http/Resources/UserResource.php`:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->getRoleNames(),
        ];
    }
}
```

`uuid` sai no JSON, `id` nunca sai — regra da arquitectura híbrida
respeitada em todo o resto do projecto.

## 5. Exception Handler para respostas de erro consistentes

`bootstrap/app.php` já tem `shouldRenderJsonWhen`. Acrescentar o
formato de erro consistente pedido pelos `docs/03-arquitetura.md`
originais:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(
        fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
    );

    $exceptions->render(function (AuthenticationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => ['code' => 'NAO_AUTENTICADO', 'message' => 'Credenciais inválidas.'],
            ], 401);
        }
    });
})
```

O código do erro (`NAO_AUTENTICADO`) fica em português por ser texto
que aparece directamente na resposta da API para debugging humano — não
é um identificador de código PHP/SQL, cai do lado "português" da tabela
de convenção do `AGENTS.md`.

## Checkpoint

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\SuperAdminSeeder
php artisan serve
```

Noutro terminal:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@system.ao","password":"ChangeMe123!"}'
```

Deve devolver um `token` e um objecto `user` com `"roles":
["super_admin"]`.
