<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clientes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_cliente'       => 'required|string|max:255',
            'endereco_completo'  => 'required|string|max:255',
            'municipio'          => 'required|string|max:255',
            'estado'             => 'required|string|max:255',
            'pais'               => 'required|string|max:255',
            'cnpj'               => 'required|string|max:255',
        ]);

        Cliente::create($data);

        return back()->with('success', 'Cliente criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, Cliente $cliente)
    {
        $data = $request->validate([
            'nome_cliente'       => 'required|string|max:255',
            'endereco_completo'  => 'required|string|max:255',
            'municipio'          => 'required|string|max:255',
            'estado'             => 'required|string|max:255',
            'pais'               => 'required|string|max:255',
            'cnpj'               => 'required|string|max:255',
        ]);

        $cliente->update($data);

        return back()->with('success', 'Cliente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return back()->with('success', 'Cliente excluído com sucesso.');
    }
}
