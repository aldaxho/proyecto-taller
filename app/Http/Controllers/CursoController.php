<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Categoria;
use App\Models\MaterialDidactico;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cursos= Curso::All();
        $categorias = Categoria::all();
        $userId=session('usuario_id');
        $userNombre=session('usuario_nombre');
        return view('client.courses.create', compact('cursos','categorias','userId','userNombre'));
    }


    public function cursosshow(){
        $cursos= Curso::all();
        return view('client.courses.index', compact('cursos'));
    }



    /*mis cursos creados por mi */
    public function misCursos()
    {
        // Obtener el usuario autenticado con el guard 'usuarios'
        $user = Auth::user();
             // Obtener los cursos creados por el usuario
                $cursos = Curso::where('autor', $user->id)
                ->with('categoria', 'materialesDidacticos')
                ->get();

            return view('client.courses.mis-cursos', compact('cursos'));
    }

    public function detalles($id)
    {
        // Obtener el curso por ID, incluyendo los usuarios y materiales didácticos
        $curso = Curso::with(['usuarios', 'materialesDidacticos'])->findOrFail($id);

        // Usuarios que compraron el curso
        $usuarios = $curso->usuarios;

        // Materiales didácticos asociados al curso
        $materiales = $curso->materialesDidacticos;

        return view('client.courses.detalles', compact('curso', 'usuarios', 'materiales'));
    }

    public function show($id)
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a los cursos.');
        }

        // Cargar las relaciones de suscripciones y compras
        $usuario->load('suscripciones', 'compras');

        // Obtener el curso por ID
        $curso = Curso::with('materiales')->findOrFail($id);

        // Verificar si el usuario ha comprado el curso
        $haCompradoCurso = $usuario->haCompradoCurso($curso->id);

        // Verificar si el usuario está suscrito
        $esSuscriptor = $usuario->esSuscriptor;

        // Lógica de materiales
        if ($haCompradoCurso) {
            $materiales = $curso->materiales;
        } elseif ($esSuscriptor) {
            $materiales = MaterialDidactico::all();
        } else {
            $materiales = [];
        }

        // Pasar las variables a la vista
        return view('client.courses.show', [
            'curso' => $curso,
            'materiales' => $materiales,
            'haCompradoCurso' => $haCompradoCurso,
            'esSuscriptor' => $esSuscriptor
        ]);
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
       // dd($request->all());
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'autor' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric',
            'tiempo' => 'required|integer',
            'estado' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cursoData = $request->all();

        // Manejar la carga de la imagen
        if ($request->hasFile('imagen')) {
            $imageName = time() . '.' . $request->imagen->extension();
            $request->imagen->move(public_path('imagenes/cursos'), $imageName);
            $cursoData['imagen'] = 'imagenes/cursos/' . $imageName;
        }
       // dd($cursoData);
        Curso::create($cursoData);

        return redirect()->route('client.courses.create')->with('success', 'Curso creado exitosamente');
    }


    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /* Parte Administrativa*/
    public function indexAdmin()
{
    // Obtener los 10 cursos más vendidos
    $cursosMasVendidos = Curso::with(['categoria', 'autornombre'])
        ->withCount('compras') // Contar el número de compras
        ->orderByDesc('compras_count') // Ordenar por cantidad de compras
        ->take(10) // Limitar a 10 resultados
        ->get();

    // Obtener el curso mejor calificado
   // Curso mejor calificado
   $cursoMejorCalificado = Curso::with('categoria', 'autornombre', 'calificaciones')
   ->withAvg('calificaciones', 'estrellas') // Promedio de calificaciones
   ->orderByDesc('calificaciones_avg_estrellas') // Ordenamos por promedio de calificación
   ->first();

    return view('admin.secciones.CursoCrud', compact('cursosMasVendidos', 'cursoMejorCalificado'));
}
public function comprar($id)
{
    // Obtener el curso por ID
    $curso = Curso::findOrFail($id);

    // Enviar a la vista de Stripe con los detalles del curso
    return view('suscripciones.stripe', [
        'curso' => $curso,
        'precio' => $curso->precio, // Asegúrate de tener un campo 'precio' en el modelo Curso
    ]);
}

public function procesarPago(Request $request, $precio)
{
    try {
        // Procesar el pago con Stripe
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $precio * 100, // Stripe maneja centavos, por eso multiplicamos por 100
            'currency' => 'usd',
            'payment_method' => $request->input('stripeToken'),
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        // Si el pago fue exitoso, redirigir con mensaje
        return redirect()
            ->route('curso.detalles')
            ->with('success', 'Curso comprado con éxito.');
    } catch (\Exception $e) {
        // Manejar errores de Stripe
        return redirect()
            ->back()
            ->with('error', 'Hubo un error con el pago: ' . $e->getMessage());
    }
}

}
