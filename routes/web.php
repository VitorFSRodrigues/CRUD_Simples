<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redireciona raiz para /home
Route::get('/', fn() => redirect('/home'));

// /home vazia (view simples)
Route::view('/home', 'home');

// Rota da tabela Clientes (PowerGrid)
Route::resource('clientes-orcamentos', ClienteController::class)
    ->parameters(['clientes-orcamentos' => 'cliente'])
    ->only(['index','store','update','destroy']);