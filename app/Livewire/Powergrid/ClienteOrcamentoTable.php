<?php

namespace App\Livewire\Powergrid;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields, Column, Button};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid, Rule};
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;
use Illuminate\Support\Carbon;

final class ClienteOrcamentoTable extends PowerGridComponent
{
    //use ActionButton;

    public string $tableName = 'clientes-orcamentos';

    /** Form (Create/Edit) */
    public array $form = [
        'nome_cliente'      => '',
        'endereco_completo' => '',
        'municipio'         => '',
        'estado'            => '',
        'pais'              => '',
        'cnpj'              => '',
    ];

    public bool $isEdit = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Cliente::query()->orderBy('id', 'asc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nome_cliente')
            ->add('endereco_completo')
            ->add('municipio')
            ->add('estado')
            ->add('pais')
            ->add('cnpj')
            ->add('created_at_formatted', fn (Cliente $model) => Carbon::parse($model->created_at)->format('d/m/Y H:i:s'));
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Nome Cliente', 'nome_cliente')
                ->sortable()
                ->searchable(),

            Column::make('Endereco Completo', 'endereco_completo')
                ->sortable()
                ->searchable(),

            Column::make('Municipio', 'municipio')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado')
                ->sortable()
                ->searchable(),

            Column::make('Pais', 'pais')
                ->sortable()
                ->searchable(),

            Column::make('Cnpj', 'cnpj')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::action('Ações')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datetimepicker('created_at'),
        ];
    }

    /** HEADER: botão "Novo cadastro" */
    public function header(): array
    {
        return [
            Button::add('novo')
                ->slot('Novo cadastro')
                ->class('btn btn-primary')
                ->dispatch('open-create-modal', []),
        ];
    }

    /* =======================
     * AÇÕES (botões na coluna): Editar / Deletar
     * ======================= */
    public function actions(Cliente $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->class('btn btn-xs btn-warning')
                ->dispatch('open-edit-modal', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('Deletar')
                ->class('btn btn-xs btn-danger')
                ->dispatch('open-delete-modal', ['rowId' => $row->id]),
        ];
    }

    /* ==== Eventos para abrir modais ==== */
    #[On('open-create-modal')]
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('show-create-edit-modal');
    }

    #[On('open-edit-modal')]
    public function openEditModal($rowId): void
    {
        $cliente = Cliente::findOrFail((int)$rowId);
        $this->form = [
            'nome_cliente'      => $cliente->nome_cliente,
            'endereco_completo' => $cliente->endereco_completo,
            'municipio'         => $cliente->municipio,
            'estado'            => $cliente->estado,
            'pais'              => $cliente->pais,
            'cnpj'              => $cliente->cnpj,
        ];
        $this->editingId = $cliente->id;
        $this->isEdit = true;
        $this->dispatch('show-create-edit-modal');
    }

    #[On('open-delete-modal')]
    public function openDeleteModal($rowId): void
    {
        $this->deletingId = (int)$rowId;
        $this->dispatch('show-delete-modal');
    }

    /* ==== Persistência ==== */
    public function save(): void
    {
        $data = $this->validate([
            'form.nome_cliente'      => 'required|string|max:255',
            'form.endereco_completo' => 'required|string|max:255',
            'form.municipio'         => 'required|string|max:255',
            'form.estado'            => 'required|string|max:255',
            'form.pais'              => 'required|string|max:255',
            'form.cnpj'              => 'required|string|max:255',
        ])['form'];

        if ($this->isEdit && $this->editingId) {
            Cliente::findOrFail($this->editingId)->update($data);
            $this->dispatch('pg:eventRefresh-default'); // atualiza tabela
            $this->dispatch('toast', detail: 'Cliente atualizado com sucesso');
        } else {
            Cliente::create($data);
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('toast', detail: 'Cliente criado com sucesso');
        }

        $this->dispatch('hide-create-edit-modal');
        $this->resetForm();
    }

    public function deleteConfirm(): void
    {
        if ($this->deletingId) {
            Cliente::findOrFail($this->deletingId)->delete();
            $this->dispatch('pg:eventRefresh-default');
            $this->dispatch('toast', detail: 'Cliente excluído com sucesso');
        }
        $this->deletingId = null;
        $this->dispatch('hide-delete-modal');
    }

    private function resetForm(): void
    {
        $this->form = [
            'nome_cliente'      => '',
            'endereco_completo' => '',
            'municipio'         => '',
            'estado'            => '',
            'pais'              => '',
            'cnpj'              => '',
        ];
        $this->isEdit = false;
        $this->editingId = null;
    }

    /* ==== Modais (Blade inline) ==== */
    // public function render()
    // {
    //     return view('livewire.powergrid.cliente-orcamento-table');
    // }

    // #[\Livewire\Attributes\On('edit')]
    // public function edit($rowId): void
    // {
    //     $this->js('alert('.$rowId.')');
    // }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
