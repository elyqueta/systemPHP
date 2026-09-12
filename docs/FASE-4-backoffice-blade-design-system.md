# Fase 4 — Backoffice Blade (Admin da Plataforma)

Objectivo: um painel administrativo mínimo em Blade (login, listagem e
criação de Institutions) que já nasce a usar os tokens visuais do
design system existente no frontend React do KwanzaFolha Cloud, para
que a transição futura para Angular seja só de framework, não de
identidade visual.

As rotas e nomes de Controller do painel seguem a mesma convenção de
inglês do resto do código; os textos visíveis ao utilizador (labels dos
formulários, mensagens) ficam em português.

## Aviso importante antes de começar esta fase

Eu (Claude) sei, pela memória de conversas anteriores sobre o
KwanzaFolha, que o frontend React usa glassmorphism, fonte Inter para
texto geral, JetBrains Mono para números, e variáveis CSS customizadas.
**Não tenho os valores exactos** (códigos de cor hex, valores de
`backdrop-filter`) porque nunca me enviaste o CSS real desse projecto
nesta conversa. Os valores abaixo são placeholder — ver checkpoint em
`HUMAN.md` sobre como resolver isto.

## 1. Estrutura de ficheiros Blade

```
resources/views/admin/
├── layouts/
│   └── app.blade.php
├── auth/
│   └── login.blade.php
└── institutions/
    ├── index.blade.php
    └── create.blade.php
```

## 2. Tokens de design (provisórios)

`resources/css/admin.css`:

```css
:root {
    --glass-bg: rgba(255, 255, 255, 0.06);
    --glass-border: rgba(255, 255, 255, 0.12);
    --glass-blur: 16px;

    --color-neutral-900: #0f1115;
    --color-neutral-700: #2a2d34;
    --color-neutral-400: #9ca0a8;
    --color-neutral-100: #f4f5f7;

    --font-sans: 'Inter', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', monospace;
}

body {
    font-family: var(--font-sans);
    background-color: var(--color-neutral-900);
    color: var(--color-neutral-100);
}

.glass-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(var(--glass-blur));
    border-radius: 0.75rem;
}

.numero,
.valor-monetario {
    font-family: var(--font-mono);
    font-variant-numeric: tabular-nums;
}
```

A classe fica com nome em português (`.numero`, `.valor-monetario`) de
propósito — é uma classe CSS de apresentação visual, não um
identificador estrutural de dados; segue a mesma lógica de
`tax_regime = 'Geral'` na Fase 1 (dado/apresentação em português,
estrutura em inglês).

## 3. Layout base

`resources/views/admin/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — @yield('title', 'Backoffice')</title>
    @vite(['resources/css/admin.css'])
</head>
<body>
    <div class="glass-card" style="margin: 2rem; padding: 1.5rem;">
        @yield('content')
    </div>
</body>
</html>
```

Registar `resources/css/admin.css` no `vite.config.js`, ao lado do
`resources/css/app.css` já existente, sem remover este último.

## 4. Autenticação do painel (sessão, não Sanctum)

`routes/web.php`:

```php
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\InstitutionController as AdminInstitutionController;

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::resource('institutions', AdminInstitutionController::class)->only(['index', 'create', 'store']);
});
```

`app/Http/Controllers/Admin/AuthController.php` usa `Auth::attempt()`
nativo do Laravel directamente (sessão), não a
`AuthenticateUserAction` da Fase 2 (essa é sobre tokens Sanctum — fluxo
diferente, mesmo autenticando o mesmo `User`).

## 5. View de login

`resources/views/admin/auth/login.blade.php`:

```blade
@extends('admin.layouts.app')

@section('title', 'Entrar')

@section('content')
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Palavra-passe</label>
        <input type="password" name="password" required>

        @error('email')
            <p style="color: #f87171;">{{ $message }}</p>
        @enderror

        <button type="submit" class="glass-card">Entrar</button>
    </form>
@endsection
```

Estilos inline mínimos aqui de propósito — refinamento visual fica para
quando o design system real chegar.

## 6. Listagem e criação de Institutions

Reaproveitar directamente as mesmas Actions e a mesma `InstitutionPolicy`
da Fase 3:

```php
public function index()
{
    $this->authorize('viewAny', Institution::class);

    return view('admin.institutions.index', [
        'institutions' => Institution::orderBy('name')->paginate(20),
    ]);
}

public function store(CreateInstitutionRequest $request, CreateInstitutionAction $action)
{
    $action->execute($request->validated());

    return redirect()->route('admin.institutions.index');
}
```

## Checkpoint

```bash
npm install
npm run build
php artisan serve
```

Aceder a `http://localhost:8000/admin/login`, autenticar com o super
admin criado na Fase 2, e confirmar que a listagem de Institutions
aparece com o `.glass-card` aplicado.

Depois deste checkpoint, decidimos se envias o CSS real do frontend
React antes de refinar visualmente, ou se isso fica para quando a base
Angular do design system estiver pronta.
