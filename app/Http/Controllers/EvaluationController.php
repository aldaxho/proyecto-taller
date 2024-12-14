<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvaluationController extends Controller
{
    // Mostrar el formulario inicial
    public function create()
    {
        return view('evaluations.create');
    }

    // Generar preguntas con IA
    public function generate(Request $request)
    {
        $request->validate([
            'materia' => 'required|string',
            'nivel' => 'required|string',
        ]);

        $materia = $request->materia;
        $nivel = $request->nivel;

        // Obtener preguntas de la IA
        $preguntas = $this->generarPreguntasIA($materia, $nivel);

        return view('evaluations.questions', compact('materia', 'nivel', 'preguntas'));
    }

    // Guardar la evaluación final
    public function store(Request $request)
    {
        $request->validate([
            'materia' => 'required|string',
            'nivel' => 'required|string',
            'respuestas' => 'required|array',
        ]);

        // Evaluar las respuestas con IA
        $resultado = $this->evaluarConIA($request->respuestas);
        $permiso = $resultado === 'aprobado';

        Evaluation::create([
            'user_id' => Auth::id(),
            'materia' => $request->materia,
            'nivel' => $request->nivel,
            'resultado' => $resultado,
            'permiso' => $permiso,
        ]);

        return redirect()->route('home')->with('status', 'Evaluación completada: ' . $resultado);
    }

    // Función para generar preguntas con IA
    private function generarPreguntasIA($materia, $nivel)
    {
        try {
            $prompt = "Genera exactamente 10 preguntas de evaluación en formato JSON para una persona que quiere enseñar la materia '{$materia}' a nivel '{$nivel}'.

            Las preguntas pueden ser teóricas o desafíos de código, dependiendo de la naturaleza de la materia. Asegúrate de que las preguntas sean apropiadas para el nivel indicado ('{$nivel}').

            La respuesta debe ser exclusivamente un JSON válido con la siguiente estructura:
            {
              \"preguntas\": [
                \"Pregunta 1\",
                \"Pregunta 2\",
                \"Pregunta 3\",
                \"Pregunta 4\",
                \"Pregunta 5\",
                \"Pregunta 6\",
                \"Pregunta 7\",
                \"Pregunta 8\",
                \"Pregunta 9\",
                \"Pregunta 10\"
              ]
            }";

            $apiKey = env('OPENAI_API_KEY');

            // Solicitud a la API de OpenAI
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['choices'][0]['message']['content'];
                $preguntas = json_decode($message, true)['preguntas'] ?? [];

                if (count($preguntas) === 10) {
                    return $preguntas;
                } else {
                    throw new \Exception('La API no devolvió 10 preguntas.');
                }
            } else {
                Log::error('Error en la respuesta de la API de IA', ['response' => $response->body()]);
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Error al conectar con la API de IA', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // Evaluar respuestas con IA
    private function evaluarConIA($respuestas)
    {
        try {
            $apiKey = env('OPENAI_API_KEY');

            $prompt = 'Evalúa las siguientes respuestas de un formulario de evaluación. Responde con "aprobado" o "rechazado" según el desempeño del usuario.

            Respuestas del usuario:
            ' . json_encode($respuestas);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 100,
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $resultado = strtolower(trim($data['choices'][0]['message']['content']));

                return in_array($resultado, ['aprobado', 'rechazado']) ? $resultado : 'rechazado';
            } else {
                Log::error('Error en la respuesta de la API de IA', ['response' => $response->body()]);
                return 'rechazado';
            }
        } catch (\Exception $e) {
            Log::error('Error al conectar con la API de IA', ['error' => $e->getMessage()]);
            return 'rechazado';
        }
    }
}
