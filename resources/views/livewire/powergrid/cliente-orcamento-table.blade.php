<div>
    {{-- O próprio PowerGrid renderiza a tabela --}}
    <x-powergrid::table :headers="$this->headers()" :rows="$this->rows" :footer="$this->showFooter" />

    {{-- MODAL CRIAR/EDITAR --}}
    <x-adminlte-modal id="modalCreateEdit" title="{{ $isEdit ? 'Editar Cliente' : 'Novo Cliente' }}"
        theme="primary" size="lg" v-centered static-backdrop>
        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label>Nome do Cliente</label>
                    <input type="text" class="form-control" wire:model.defer="form.nome_cliente" required>
                    @error('form.nome_cliente')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-12 mb-2">
                    <label>Endereço Completo</label>
                    <input type="text" class="form-control" wire:model.defer="form.endereco_completo" required>
                    @error('form.endereco_completo')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-4 mb-2">
                    <label>Município</label>
                    <input type="text" class="form-control" wire:model.defer="form.municipio" required>
                    @error('form.municipio')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-2 mb-2">
                    <label>Estado</label>
                    <input type="text" class="form-control" wire:model.defer="form.estado" required>
                    @error('form.estado')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label>País</label>
                    <input type="text" class="form-control" wire:model.defer="form.pais" required>
                    @error('form.pais')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label>CNPJ</label>
                    <input type="text" class="form-control" wire:model.defer="form.cnpj" required>
                    @error('form.cnpj')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <x-slot name="footerSlot">
                <x-adminlte-button class="mr-auto" theme="secondary" label="Fechar" data-dismiss="modal" />
                <x-adminlte-button type="submit" theme="primary" label="Salvar" />
            </x-slot>
        </form>
    </x-adminlte-modal>

    {{-- MODAL DELETAR --}}
    <x-adminlte-modal id="modalDelete" title="Confirmar exclusão" theme="danger" icon="fas fa-trash"
        v-centered static-backdrop>
        <p>Tem certeza que deseja excluir este cadastro?</p>

        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto" theme="secondary" label="Cancelar" data-dismiss="modal" />
            <x-adminlte-button theme="danger" label="Deletar" wire:click="deleteConfirm" />
        </x-slot>
    </x-adminlte-modal>

    {{-- Eventos JS para abrir/fechar modais --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-create-edit-modal', () => $('#modalCreateEdit').modal('show'));
            Livewire.on('hide-create-edit-modal', () => $('#modalCreateEdit').modal('hide'));

            Livewire.on('show-delete-modal', () => $('#modalDelete').modal('show'));
            Livewire.on('hide-delete-modal', () => $('#modalDelete').modal('hide'));

            Livewire.on('toast', ({detail}) => {
                // Pode integrar com toastr/sweetalert; por enquanto console:
                console.log(detail);
            });
        });
    </script>
</div>
