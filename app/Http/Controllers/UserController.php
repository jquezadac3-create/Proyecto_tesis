<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('permission:view users', only: ['index']),
            new Middleware('permission:create user', only: ['store']),
            new Middleware('permission:edit user', only: ['update']),
            new Middleware('permission:delete user', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::withCount('facturas')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $password = $request->input('password');
        $hashedPassword = Hash::make($password);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $hashedPassword,
        ]);

        $user->assignRole('seller');

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $password = $request->input('password');
        $hashedPassword = $password ? Hash::make($password) : null;

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($hashedPassword) {
            $user->password = $hashedPassword;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente',
        ]);
    }
}
