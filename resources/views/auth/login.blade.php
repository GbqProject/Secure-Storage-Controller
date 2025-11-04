@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl mb-4">Iniciar sesión</h2>
        @if ($errors->any())
            <div class="text-red-600">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input name="email" type="email" class="w-full border p-2" />
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input name="password" type="password" class="w-full border p-2" />
            </div>
            <div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Entrar</button>
            </div>
        </form>
    </div>
@endsection
