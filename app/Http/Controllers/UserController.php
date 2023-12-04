<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return view('users.index', ['users' => $users]);
    }

    public function getUsers(){
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = role::all();
        return view('users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */


     /* public function nuevoAgremiado(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:5',
        'roles' => 'required|array',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    try {
        // Verificar si el usuario ya existe por su correo electrónico
        $existingUser = User::where('email', $request->input('email'))->first();

        if ($existingUser) {
            return response()->json(['error' => 'El correo electrónico ya está registrado.'], 422);
        }

        // Crear el nuevo usuario
        $user = User::create($request->all());
        $user->roles()->attach($request->input('roles'));

        return response()->json($user);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error al crear el usuario.'], 500);
    }
} */
public function nuevoAgremiado(Request $request)
{
    $existingUser = User::where('email', $request->input('email'))->first();

    if ($existingUser) {
        return response()->json(['error' => ['email' => ['The email has already been taken.']]], 422);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:5',
        'roles' => 'required|array',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    try {
        // Crear el nuevo usuario
        $user = User::create($request->all());
        $user->roles()->attach($request->input('roles'));

        // Agrega registros de depuración
        Log::info('Usuario creado correctamente:', ['user' => $user->toArray()]);

        return response()->json($user);
    } catch (\Exception $e) {
        // Agrega registros de depuración
        Log::error('Error al crear el usuario:', ['error' => $e->getMessage()]);

        return response()->json(['error' => 'Error al crear el usuario.'], 500);
    }
}


    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:5',
                'roles' => 'required|array',
            ]
        );
        $user = User::create($request->all());

        $user->roles()->attach($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = role::all();
        return view('users.show', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'required|string|min:5',
                'roles' => 'required|array',
            ]
        );
        $user->update($request->all());
        $user->roles()->sync($request->input('roles'));
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->roles()->detach();
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
