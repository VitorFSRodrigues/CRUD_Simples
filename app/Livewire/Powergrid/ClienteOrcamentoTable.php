<?php

namespace App\Livewire\Powergrid;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields, Column, Button};
use PowerComponents\LivewirePowerGrid\Facades\{PowerGrid};
use Illuminate\Contracts\View\View;

final class ClienteOrcamentoTable extends PowerGridComponent
{
    public string $tableName = 'clientes-orcamentos';

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
        return PowerGrid::fields()
            ->add('id')
            ->add('nome_cliente')
            ->add('endereco_completo')
            ->add('municipio')
            ->add('estado')
            ->add('pais')
            ->add('cnpj');
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

    /** Botão de "Novo cadastro" (CREATE) */
    public function header(): array
    {
        return [
            Button::add('novo')
                ->slot('Novo cadastro')
                ->class('btn btn-primary')
                ->openModal('clientes.create-edit', []), // CREATE -> sem id
        ];
    }

    /** Ações de linha (EDIT/DELETE) */
    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->class('btn btn-xs btn-warning')
                ->openModal('clientes.create-edit', ['clienteId' => $row->id]),   // <- aqui

            Button::add('delete')
                ->slot('Deletar')
                ->class('btn btn-xs btn-danger')
                ->openModal('clientes.confirm-delete', ['clienteId' => $row->id]), // <- e aqui
        ];
    }
}
