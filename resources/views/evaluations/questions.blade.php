<!-- resources/views/evaluations/questions.blade.php -->

@extends('layouts.client')

@section('content')
<div class="container mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-5">Responde las Siguientes Preguntas</h2>

    <form method="POST" action="{{ route('evaluation.store') }}">
        @csrf

        <input type="hidden" name="materia" value="{{ $materia }}">
        <input type="hidden" name="nivel" value="{{ $nivel }}">

        @foreach ($preguntas as $index => $pregunta)
            <div class="mb-6">
                <label for="respuesta_{{ $index }}" class="block text-gray-700 font-bold mb-2">{{ $loop->iteration }}. {{ $pregunta }}</label>
                <textarea id="respuesta_{{ $index }}" name="respuestas[]" rows="3" class="w-full p-2 border rounded" required></textarea>
            </div>
        @endforeach

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
            Enviar Respuestas
        </button>
    </form>
</div>
@endsection
