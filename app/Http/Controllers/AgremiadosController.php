<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\agremiados;
use App\Models\User;

class AgremiadosController extends Controller
{
    public function newAgremiado(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'a_paterno' => 'required',
            'a_materno' => 'required',
            'nombre' => 'required',
            'sexo' => 'required',
            'NUP' => 'required',
            'NUE' => 'required', // Asegura que NUE sea único en la tabla
            'RFC' => 'required',
            'NSS' => 'required',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required',
            'cuota' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $agremiado = agremiados::create($request->all());
        User::create([
            'NUE' => $request->NUE,
            'password' => bcrypt($request->NUE),
            'id_rol' => 2
        ]);
        return response($agremiado, 200);
    }

    /* public function getAgremiado()
    {
        return response()->json(agremiados::all(), 200);
    } */

    public function getAgremiadoById($id){
        
        $agremiado = agremiados::find($id);
        if (!$agremiado) {
            return response()->json(['message' => 'Agremiado no encontrado'], 404);
        }

        return response()->json($agremiado, 200);

    }

    public function getAgremiado()
    {
        $agremiados = Agremiados::join('usuarios', 'agremiados.NUE', '=', 'usuarios.NUE')
            ->where('usuarios.id_rol', '=', 2)
            ->select('agremiados.*')
            ->get();

        return response()->json($agremiados, 200);
    }

    public function getAdmin()
    {
        $admin = Agremiados::join('usuarios', 'agremiados.NUE', '=', 'usuarios.NUE')
            ->where('usuarios.id_rol', '=', 1)
            ->select('agremiados.*')
            ->get();

        return response()->json($admin, 200);
    }

    public function deleteAgremiadoById($id)
    {
        $agremiado = agremiados::find($id);
        if (is_null($agremiado)) {
            return response()->json(['message' => 'Agremiado no encontrado'], 404);
        }
        $agremiado->delete();
        return response()->json(['message' => 'Agremiado eliminado exitosamente'], 200);
    }

    public function updateagremiado(Request $request, $id)
    {
        $agremiado = agremiados::find($id);

        if (!$agremiado) {
            return response()->json(['message' => 'Agremiado no encontrado'], 404);
        }

        $agremiado->update($request->all());

        return response()->json(['message' => 'Agremiado actualizado con éxito']);
    }
}
