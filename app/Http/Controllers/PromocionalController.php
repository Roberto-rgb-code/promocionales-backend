<?php

namespace App\Http\Controllers;

use App\Models\Promocional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromocionalController extends Controller {
  public function index() {
    try {
      $promocionales = Promocional::with('fotos')->get();
      return response()->json($promocionales);
    } catch (\Exception $e) {
      Log::error('Error en index: ' . $e->getMessage());
      return response()->json(['error' => 'Error al obtener promocionales'], 500);
    }
  }

  public function store(Request $request) {
    try {
      Log::info('Iniciando almacenamiento de promocional', $request->all());
      $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'categoria' => 'required|in:Agendas Zegno,Antiestres,Artículos de Viaje,Bar,Bebidas,Belleza,Bolsas,Complementos,Deportes,Entretenimiento,Escritura,Herramientas,Hieleras Loncheras y Portaviandas,Hogar,Libretas y Carpetas,Llaveros,Maletas,Mochilas,Niños,Oficina,Paraguas e Impermeables,Portafolios,Salud,Tecnología,Textiles|string|max:255',
        'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
      ]);

      $promocional = new Promocional();
      $promocional->nombre = $validatedData['nombre'];
      $promocional->descripcion = $validatedData['descripcion'];
      $promocional->categoria = $validatedData['categoria'];
      if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('public/uploads');
        $promocional->foto_path = str_replace('public/', '', $path);
      }
      $promocional->save();

      if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
          $path = $foto->store('public/uploads');
          $fotoPath = str_replace('public/', '', $path);
          $promocional->fotos()->create(['foto_path' => $fotoPath]);
        }
      }

      Log::info('Promocional almacenado exitosamente', $promocional->toArray());
      return response()->json($promocional->load('fotos'), 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
      Log::error('Validación fallida: ' . json_encode($e->errors()));
      return response()->json(['message' => 'Validación fallida', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
      Log::error('Error en store: ' . $e->getMessage());
      return response()->json(['error' => 'Error al guardar el promocional', 'details' => $e->getMessage()], 500);
    }
  }

  public function show($id) {
    try {
      $promocional = Promocional::with('fotos')->findOrFail($id);
      return response()->json($promocional);
    } catch (\Exception $e) {
      Log::error('Error en show: ' . $e->getMessage());
      return response()->json(['error' => 'Promocional no encontrado'], 404);
    }
  }

  public function update(Request $request, $id) {
    try {
      Log::info('Iniciando actualización de promocional con ID: ' . $id, $request->all());
      $validatedData = $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'categoria' => 'required|in:Agendas Zegno,Antiestres,Artículos de Viaje,Bar,Bebidas,Belleza,Bolsas,Complementos,Deportes,Entretenimiento,Escritura,Herramientas,Hieleras Loncheras y Portaviandas,Hogar,Libretas y Carpetas,Llaveros,Maletas,Mochilas,Niños,Oficina,Paraguas e Impermeables,Portafolios,Salud,Tecnología,Textiles|string|max:255',
        'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
      ]);

      $promocional = Promocional::findOrFail($id);
      $promocional->nombre = $validatedData['nombre'];
      $promocional->descripcion = $validatedData['descripcion'];
      $promocional->categoria = $validatedData['categoria'];
      $promocional->save();

      if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
          $path = $foto->store('public/uploads');
          $fotoPath = str_replace('public/', '', $path);
          $promocional->fotos()->create(['foto_path' => $fotoPath]);
        }
      }

      Log::info('Promocional actualizado exitosamente', $promocional->toArray());
      return response()->json($promocional->load('fotos'));
    } catch (\Illuminate\Validation\ValidationException $e) {
      Log::error('Validación fallida: ' . json_encode($e->errors()));
      return response()->json(['message' => 'Validación fallida', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
      Log::error('Error en update: ' . $e->getMessage());
      return response()->json(['error' => 'Error al actualizar el promocional', 'details' => $e->getMessage()], 500);
    }
  }

  public function destroy($id) {
    try {
      $promocional = Promocional::findOrFail($id);
      $promocional->delete();
      return response()->json(null, 204);
    } catch (\Exception $e) {
      Log::error('Error en destroy: ' . $e->getMessage());
      return response()->json(['error' => 'Error al eliminar el promocional'], 500);
    }
  }
}