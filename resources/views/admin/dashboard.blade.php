@extends('layouts.app')
@section('title', 'Admin')
@section('content')
    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold mb-3">Grupos</h3>
            <form method="POST" action="/admin/groups">
                @csrf
                <input name="name" placeholder="Nombre grupo" class="border p-2 w-full mb-2" />
                <input name="quota_mb" placeholder="Cuota (MB) opcional" class="border p-2 w-full mb-2" />
                <button class="bg-blue-600 text-white px-3 py-1">Crear</button>
            </form>

            <h4 class="mt-4">Lista de grupos</h4>
            <ul>
                @foreach ($groups as $g)
                    <li>{{ $g->name }} — {{ $g->quota_bytes ? round($g->quota_bytes / 1024 / 1024, 2) . ' MB' : 'sin quota' }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold mb-3">Usuarios</h3>
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Grupo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->group ? $u->group->name : '-' }}</td>
                            <td>
                                <form method="POST" action="/admin/users/{{ $u->id }}/assign-group">@csrf
                                    <select name="group_id">
                                        <option value="">--</option>
                                        @foreach ($groups as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="px-2 bg-gray-200">Asignar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="mt-4">Extensiones prohibidas</h4>
            <form method="POST" action="/admin/forbidden-extensions">@csrf
                <input name="ext" placeholder="ej: exe" class="border p-2" />
                <button>Agregar</button>
            </form>
            <ul>
                @foreach ($forbidden as $f)
                    <li>{{ $f->ext }} <form method="POST" action="/admin/forbidden-extensions/{{ $f->id }}"
                            style="display:inline">@csrf @method('DELETE') <button class="text-red-600">x</button></form>
                    </li>
                @endforeach
            </ul>

            <h4 class="mt-4">Límite global</h4>
            <form method="POST" action="/admin/settings/limits">@csrf
                <input name="global_quota_mb" value="{{ round($globalQuota / 1024 / 1024, 2) }}" /> MB
                <button>Actualizar</button>
            </form>
        </div>
    </div>
@endsection
