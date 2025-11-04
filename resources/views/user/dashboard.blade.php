@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl mb-4">Mis archivos</h2>

        <div class="mb-4">
            <form id="uploadForm">
                @csrf
                <input type="file" id="fileInput" name="file" />
                <button id="uploadBtn" class="bg-green-600 text-white px-3 py-1 rounded">Subir</button>
            </form>
            <div id="uploadMessage" class="mt-2"></div>
        </div>

        <table class="w-full">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tamaño</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="filesTable">
                @foreach ($files as $f)
                    <tr>
                        <td>{{ $f->original_name }}</td>
                        <td>{{ number_format($f->size_bytes / 1024, 2) }} KB</td>
                        <td><a href="{{ route('files.download', $f->id) }}" class="text-blue-600">Descargar</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.appData = {
            csrfToken: '{{ csrf_token() }}',
            forbidden: @json($forbidden),
            globalQuotaBytes: {{ $globalQuota }}
        };
    </script>
@endsection
