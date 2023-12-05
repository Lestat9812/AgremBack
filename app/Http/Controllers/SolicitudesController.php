<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\solicitudes;

class SolicitudesController extends Controller
{
    public function updateSolicitud(Request $request, $id)

    {
        $solicitud = solicitudes::find($id);

        if (!$solicitud) {
            return response()->json(['message' => 'solicitud no encontrado'], 404);
        }

        $solicitud->update($request->all());

        return response()->json(['message' => 'solicitud actualizado con éxito']);
    }


    public function getSolicitud()
    {
        $solicitud = solicitudes::all();
        return response()->json(solicitudes::all(), 200);
    }

    public function deleteSolicitudById($id)
    {
        $solicitud = solicitudes::find($id);
        if (is_null($solicitud)) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }
        $solicitud->delete();
        return response()->json(['message' => 'Solicitud eliminada exitosamente'], 200);
    }

    public function nuevasolicitud(Request $request)
{
    $validator = Validator::make($request->all(), [
        'NUE' => 'required',
        'archivo' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:51200', // Ajusta el tamaño máximo a 50 MB
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $archivo = $request->file('archivo');

    $rutaArchivo = $archivo->getClientOriginalName(); // Obtén el nombre del archivo con la extensión

    // Almacenar el archivo en el storage
    $archivo->storeAs('public/ruta_del_archivo', $rutaArchivo);

    Solicitudes::create([
        'NUE' => $request->input('NUE'),
        'ruta_archivo' => $rutaArchivo,
        'fecha_subida' => now(),
    ]);

    return response()->json(['message' => 'Solicitud agregada exitosamente'], 200);
}


/* public function descargarArchivo($nombreArchivo)
{
    $rutaArchivo = storage_path('app/public/ruta_del_archivo/' . $nombreArchivo);

    if (!File::exists($rutaArchivo)) {
        abort(404);
    }

    $nombreDescarga = 'archivo_descargado.pdf'; // Nombre que aparecerá en el cuadro de diálogo de descarga

    return response()->download($rutaArchivo, $nombreDescarga);
} */
public function descargarArchivo($nombreArchivo)
{
    $rutaArchivo = storage_path('app/public/ruta_del_archivo/' . $nombreArchivo);

    if (!File::exists($rutaArchivo)) {
        abort(404);
    }

    $nombreOriginal = pathinfo($rutaArchivo, PATHINFO_FILENAME);
    $extension = pathinfo($rutaArchivo, PATHINFO_EXTENSION);

    $nombreDescarga = $nombreOriginal . '.' . $extension;

    return response()->download($rutaArchivo, $nombreDescarga);
}


}
