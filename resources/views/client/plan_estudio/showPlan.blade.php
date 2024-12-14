@extends('layouts.client')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Lista de Planes de Estudio</h1>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabla de Planes de Estudio -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Link</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($planes as $plan)
                <tr>
                    <td>{{ $plan->id }}</td>
                    <td>{{ $plan->nombre }}</td>
                    <td>{{ $plan->descripcion }}</td>
                    <td>
                        <a href="{{ $plan->link }}" target="_blank" class="btn btn-link">Ver Link</a>
                    </td>
                    <td>
                        <!-- Formulario para eliminar -->
                        <form action="{{ route('planes.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay planes de estudio disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
