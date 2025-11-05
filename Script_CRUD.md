# Script de Criação de CRUD (Base PowerGrid + Livewire + AdminLTE + SQLite)

> Repositório de referência: **CRUD_Simples** (estrutura/fluxo igual ao que validamos). Este roteiro parte do zero até o CRUD operacional com **modais** (Create/Edit/Delete) usando **wire-elements/modal** e **PowerGrid v6**.

---

## Pré‑requisitos

* PHP 8.2+
* Composer
* Node.js + npm
* Extensões PHP compatíveis com Laravel 12

---

## 0) Criar projeto e preparar ambiente

```bash
composer create-project laravel/laravel:^12.0 projeto
cd projeto
php artisan key:generate
npm install
npm run dev
```

### 0.1) Banco SQLite

```bash
# cria o arquivo do banco
mkdir -p database && type nul > database\database.sqlite   # Windows
# ou: touch database/database.sqlite                       # Linux/Mac
```

No `.env`:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

(Remova/ignore HOST/USER/PASS de MySQL.)

---

## 1) Instalações de pacotes

```bash
# AdminLTE (tema + layout)
composer require jeroennoten/laravel-adminlte
php artisan adminlte:install
php artisan adminlte:install --only=assets
php artisan adminlte:plugins install

# Livewire v3
composer require livewire/livewire

# PowerGrid v6
composer require power-components/livewire-powergrid

# wire-elements/modal (modais para Livewire v3)
composer require wire-elements/modal
```

No **layout base** do AdminLTE (ex.: `resources/views/vendor/adminlte/page.blade.php`), antes de `</body>` adicione:

```blade
@livewire('wire-elements-modal')
```

> Opcional: publicar as views do modal para customização (Bootstrap/AdminLTE):

```bash
php artisan vendor:publish --tag=wire-elements-modal-views
```

---

## 2) Criação de tabela (Migration)

```bash
php artisan make:migration create_clientes_table
```

Edite `database/migrations/xxxx_xx_xx_xxxxxx_create_clientes_table.php`:

```php
Schema::create('clientes', function (Blueprint $table) {
    $table->id();
    $table->string('nome_cliente', 255);
    $table->string('endereco_completo', 255);
    $table->string('municipio', 255);
    $table->string('estado', 255);
    $table->string('pais', 255);
    $table->string('cnpj', 255);
    $table->softDeletes();     // deleted_at
    $table->timestamps();      // created_at, updated_at
});
```

Aplicar migrações:

```bash
php artisan migrate --force
```

---

## 3) Criação de Model

```bash
php artisan make:model Cliente
```

`app/Models/Cliente.php`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nome_cliente','endereco_completo','municipio','estado','pais','cnpj',
    ];
}
```

---

## 4) Seeder (Opcional, mas recomendado em dev)

```bash
php artisan make:seeder ClientesSeeder
```

`database/seeders/ClientesSeeder.php` (exemplo com 5 registros):

```php
public function run(): void
{
    $rows = [
        ['id'=>1,'nome_cliente'=>'SAINT GOBAIN - SEKURIT - SP','endereco_completo'=>'Rua Rui Barbosa, 346, Centro, Mauá, SP, CEP: 09.390-000','municipio'=>'Mauá','estado'=>'SP','pais'=>'Brasil','cnpj'=>'61.064.838/0089-75'],
        ['id'=>2,'nome_cliente'=>'TRÊS CORAÇÕES - CE','endereco_completo'=>'Rua Rufino Ferreira da Silva, 200 SANTA CLARA EUSEBIO - CE 61760-000','municipio'=>'Eusébio','estado'=>'CE','pais'=>'Brasil','cnpj'=>'63.310.411/0010-94'],
        ['id'=>3,'nome_cliente'=>'CONSTRUTORA CERTA - RN','endereco_completo'=>'Rua Romualdo Galvao, 2109, Cond. Trade Center, Sala 503, LAGOA NOVA, NATAL, RN, CEP: 59056-165','municipio'=>'Natal','estado'=>'RN','pais'=>'Brasil','cnpj'=>'08.210.031/0001-89'],
        ['id'=>4,'nome_cliente'=>'DOW BRASIL INDUSTRIA E COMERCIO DE PRODUTOS QUÍMICOS LTDA','endereco_completo'=>'Rua ROD. Matoim s/n, Rótula 3, Bairro Zip, Municipio Candeias-BA','municipio'=>'Candeias','estado'=>'BA','pais'=>'Brasil','cnpj'=>'60.435.351/0017-14'],
        ['id'=>5,'nome_cliente'=>'BASF - WEISÓPOLIS - PR','endereco_completo'=>'Rua Rio Piquiri, 650, Weisópolis, PR, CEP: 83.322-010','municipio'=>'Pinhais','estado'=>'PR','pais'=>'Brasil','cnpj'=>'02.930.855/0001-47'],
    ];

    foreach ($rows as $r) {
        \App\Models\Cliente::updateOrCreate(['id'=>$r['id']], $r);
    }
}
```

**Anexar ao `DatabaseSeeder`:**

```php
public function run(): void
{
    $this->call([ClientesSeeder::class]);
}
```

Rodar:

```bash
php artisan db:seed --force
```

---

## 5) Controlador (para possíveis rotas REST/API)

```bash
php artisan make:controller ClientesController --resource --model=Cliente
```

> *Dica:* Mesmo usando modais Livewire para persistir, manter o controller ajuda em integrações futuras.

---

## 6) Rotas e tela

`routes/web.php`:

```php
Route::redirect('/', '/home');
Route::view('/home', 'home')->name('home'); // tela vazia
Route::view('/clientes-orcamentos', 'clientes.index')->name('clientes.index');
```

`resources/views/home.blade.php`:

```blade
@extends('adminlte::page')
@section('title', 'Home')
@section('content_header')<h1>Home</h1>@endsection
@section('content')<p>Página inicial vazia.</p>@endsection
```

`resources/views/clientes/index.blade.php`:

```blade
@extends('adminlte::page')
@section('title', 'Clientes (Orçamentos)')
@section('content_header')<h1>Clientes (Orçamentos)</h1>@endsection
@section('content')
    <livewire:powergrid.cliente-orcamento-table />
@endsection
```

### 6.1) Menu (AdminLTE)

`config/adminlte.php` → chave `menu`:

```php
['text' => 'Home', 'url' => 'home', 'icon' => 'fas fa-home'],
['text' => 'Clientes Orçamentos', 'url' => 'clientes-orcamentos', 'icon' => 'fas fa-users'],
```

---

## 7) PowerGrid – criação do componente da tabela

```bash
php artisan powergrid:create ClienteOrcamentoTable --model=App\Models\Cliente --folder=Powergrid
```

Arquivo gerado: `app/Livewire/Powergrid/ClienteOrcamentoTable.php`

### 7.1) Ajustes essenciais do componente

* **Sem** método `render()` custom (o PG v6 cuida da view)
* **Defina o nome da tabela** (para refresh):

```php
public string $tableName = 'clientes-orcamentos';
```

* **datasource/fields/columns** conforme os campos
* **Botão header** (Create) e **ações por linha** (Edit/Delete) usando **`openModal()`**

**Exemplo mínimo (trechos principais):**

```php
public function setUp(): array
{
    return [
        PowerGrid::header()->showToggleColumns(),
        PowerGrid::footer()->showPerPage(10)->showRecordCount(),
    ];
}

public function datasource(): Builder
{
    return Cliente::query()->orderBy('id', 'asc');
}

public function fields(): PowerGridFields
{
    return PowerGrid::fields()->add('id')->add('nome_cliente')->add('endereco_completo')
        ->add('municipio')->add('estado')->add('pais')->add('cnpj');
}

public function columns(): array
{
    return [
        Column::make('ID','id')->sortable()->searchable(),
        Column::make('Cliente','nome_cliente')->searchable()->sortable(),
        Column::make('Endereço','endereco_completo')->searchable(),
        Column::make('Município','municipio')->searchable()->sortable(),
        Column::make('Estado','estado')->searchable()->sortable(),
        Column::make('País','pais')->searchable()->sortable(),
        Column::make('CNPJ','cnpj')->searchable(),
        Column::action('Ações'),
    ];
}

public function header(): array
{
    return [
        Button::add('novo')->slot('Novo cadastro')->class('btn btn-primary')
            ->openModal('clientes.create-edit', []), // CREATE
    ];
}

public function actions($row): array
{
    return [
        Button::add('edit')->slot('Editar')->class('btn btn-xs btn-warning')
            ->openModal('clientes.create-edit', ['clienteId' => $row->id]),

        Button::add('delete')->slot('Deletar')->class('btn btn-xs btn-danger')
            ->openModal('clientes.confirm-delete', ['clienteId' => $row->id]),
    ];
}
```

> **Importante (PG v6):** use `slot()` (não `caption()`), `openModal()` (não `emit()`), e **não crie** Blade própria para a tabela.

---

## 8) Modais (Create/Edit e ConfirmDelete)

Crie os componentes Livewire **estendendo** `LivewireUI\Modal\ModalComponent`.

### 8.1) Create/Edit

`app/Livewire/Clientes/CreateEdit.php`:

```php
use App\Models\Cliente;
use LivewireUI\Modal\ModalComponent;

class CreateEdit extends ModalComponent
{
    public int|string|null $clienteId = null;

    public array $form = [
        'nome_cliente' => '', 'endereco_completo' => '', 'municipio' => '',
        'estado' => '', 'pais' => '', 'cnpj' => '',
    ];

    public static function destroyOnClose(): bool { return true; }

    public function mount(int|string|null $clienteId = null): void
    {
        if ($clienteId === null || $clienteId === '') { $this->clienteId = null; return; }
        $this->clienteId = (int) $clienteId;
        $c = Cliente::findOrFail($this->clienteId);
        $this->form = [
            'nome_cliente' => $c->nome_cliente,
            'endereco_completo' => $c->endereco_completo,
            'municipio' => $c->municipio,
            'estado' => $c->estado,
            'pais' => $c->pais,
            'cnpj' => $c->cnpj,
        ];
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.nome_cliente' => 'required|string|max:255',
            'form.endereco_completo' => 'required|string|max:255',
            'form.municipio' => 'required|string|max:255',
            'form.estado' => 'required|string|max:255',
            'form.pais' => 'required|string|max:255',
            'form.cnpj' => 'required|string|max:255',
        ])['form'];

        $this->clienteId !== null
            ? Cliente::findOrFail((int)$this->clienteId)->update($data)
            : Cliente::create($data);

        // Refresh PowerGrid
        $this->dispatch('pg:eventRefresh', id: 'clientes-orcamentos');
        $this->closeModal();
    }

    public function render() { return view('livewire.clientes.create-edit'); }
}
```

`resources/views/livewire/clientes/create-edit.blade.php` (inputs com `wire:model.defer` e botões `type="button"`):

```blade
<div class="p-4 space-y-3">
  <h5>{{ $clienteId ? 'Editar Cliente' : 'Novo Cliente' }}</h5>
  <div class="form-group"><label>Nome</label>
    <input type="text" class="form-control" wire:model.defer="form.nome_cliente"></div>
  <div class="form-group"><label>Endereço Completo</label>
    <input type="text" class="form-control" wire:model.defer="form.endereco_completo"></div>
  <div class="row">
    <div class="col-md-4 form-group"><label>Município</label>
      <input type="text" class="form-control" wire:model.defer="form.municipio"></div>
    <div class="col-md-2 form-group"><label>Estado</label>
      <input type="text" class="form-control" wire:model.defer="form.estado"></div>
    <div class="col-md-3 form-group"><label>País</label>
      <input type="text" class="form-control" wire:model.defer="form.pais"></div>
    <div class="col-md-3 form-group"><label>CNPJ</label>
      <input type="text" class="form-control" wire:model.defer="form.cnpj"></div>
  </div>
  <div class="d-flex justify-content-end gap-2 mt-3">
    <button type="button" class="btn btn-secondary" wire:click="$dispatch('closeModal')">Fechar</button>
    <button type="button" class="btn btn-primary" wire:click="save">Salvar</button>
  </div>
</div>
```

### 8.2) ConfirmDelete

`app/Livewire/Clientes/ConfirmDelete.php`:

```php
use App\Models\Cliente;
use LivewireUI\Modal\ModalComponent;

class ConfirmDelete extends ModalComponent
{
    public int|string $clienteId;
    public static function destroyOnClose(): bool { return true; }

    public function mount(int|string $clienteId): void
    { $this->clienteId = (int) $clienteId; }

    public function delete(): void
    {
        Cliente::findOrFail($this->clienteId)->delete();
        $this->dispatch('pg:eventRefresh', id: 'clientes-orcamentos');
        $this->closeModal();
    }

    public function render() { return view('livewire.clientes.confirm-delete'); }
}
```

`resources/views/livewire/clientes/confirm-delete.blade.php`:

```blade
<div class="p-4">
  <h5>Confirmar exclusão</h5>
  <p>Tem certeza que deseja excluir este cadastro?</p>
  <div class="d-flex justify-content-end gap-2 mt-3">
    <button type="button" class="btn btn-secondary" wire:click="$dispatch('closeModal')">Cancelar</button>
    <button type="button" class="btn btn-danger" wire:click="delete">Deletar</button>
  </div>
</div>
```

---

## 9) Dicas e armadilhas comuns (PowerGrid v6)

* **`slot()`** no `Button` (não `caption()`).
* Use **`openModal()`** (não `emit()`/`dispatch()` para abrir modal).
* No header, **não** passe `clienteId`; nas ações por linha, passe `['clienteId' => $row->id]`.
* **Não** crie Blade própria da tabela; deixe o `PowerGridComponent` renderizar.
* Se precisar `render()` manual: retorne `livewire-powergrid::components.table` e injete `headers/rows/showFooter`.
* Para refresh do grid após salvar/deletar: `dispatch('pg:eventRefresh', id: 'seu-tableName')`.
* Se o VSCode apontar `dispatch()` como “indefinido”, é **falso positivo** do Intelephense.
* Se aparecer erro do tipo `Unable to locate class view for component [powergrid::table]`, limpe caches e teste alias `x-livewire-powergrid::table`.
* **Tipagem**: IDs chegam como string; faça cast `(int) $id` no `mount()`.

---

## 10) Comandos úteis

```bash
php artisan optimize:clear
php artisan view:clear
composer dump-autoload
```

---

## 11) Estrutura mínima que esperamos

```
app/
 └─ Livewire/
     ├─ Clientes/
     │   ├─ ConfirmDelete.php
     │   └─ CreateEdit.php
     └─ Powergrid/
         └─ ClienteOrcamentoTable.php
resources/
 └─ views/
     ├─ clientes/
     │  └─ index.blade.php
     └─ livewire/
        └─ clientes/
           ├─ confirm-delete.blade.php
           └─ create-edit.blade.php
```

---

## 12) Próximos CRUDs (reuso)

* Copie o fluxo acima trocando nomes (migration/model/seed/pg component/modal views) e **mantenha** o padrão de IDs, `$tableName`, e o refresh por evento.
* Centralize máscaras/validações em *Form Requests* quando começar a repetir regras.
* Se precisar filtros/exports no PowerGrid, habilite no `setUp()` (filters, exportable) e nas `columns()`.

> Este documento é seu “script base”. Ajuste nomes e caminhos conforme o novo módulo (Produtos, Fornecedores, etc.) e siga a mesma receita.
