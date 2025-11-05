<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use LivewireUI\Modal\ModalComponent;

class CreateEdit extends ModalComponent
{
    public int|string|null $clienteId = null;  // aceita "5" ou 5

    public array $form = [
        'nome_cliente' => '',
        'endereco_completo' => '',
        'municipio' => '',
        'estado' => '',
        'pais' => '',
        'cnpj' => '',
    ];

    public static function destroyOnClose(): bool
    {
        return true;
    }

    public function mount(int|string|null $clienteId = null): void
    {
        if ($clienteId === null || $clienteId === '') {
            // CREATE
            $this->clienteId = null;
            return;
        }
        
        $this->clienteId = (int) $clienteId;

        // EDIT: carrega os dados
        $c = Cliente::findOrFail((int) $clienteId);

        $this->form = [
            'nome_cliente'      => $c->nome_cliente,
            'endereco_completo' => $c->endereco_completo,
            'municipio'         => $c->municipio,
            'estado'            => $c->estado,
            'pais'              => $c->pais,
            'cnpj'              => $c->cnpj,
        ];
    }

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

        if ($this->clienteId !== null && is_numeric($this->clienteId)) {
            // EDIT
            Cliente::findOrFail((int)$this->clienteId)->update($data);
        } else {
            // CREATE
            Cliente::create($data);
        }

        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }

    public function render() { return view('livewire.clientes.create-edit'); }
}
