<!-- resources/views/evaluations/create.blade.php -->

@extends('layouts.client')

@section('content')
<div class="container mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-5">Solicitar Evaluación para Crear Curso</h2>

    <form method="POST" action="{{ route('evaluation.generate') }}">
        @csrf

        <div class="mb-4">
            <label for="materia" class="block text-gray-700 font-bold mb-2">Materia:</label>
            <input type="text" id="materia" name="materia" class="w-full p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label for="nivel" class="block text-gray-700 font-bold mb-2">Nivel:</label>
            <select id="nivel" name="nivel" class="w-full p-2 border rounded" required>
                <option value="principiante">Principiante</option>
                <option value="intermedio">Intermedio</option>
                <option value="avanzado">Avanzado</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
            Generar Preguntas
        </button>
    </form>
</div>
@endsection
