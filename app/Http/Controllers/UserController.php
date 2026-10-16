<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class UserController extends Controller
{


    /**
     * Thing to display the specified user.
     */
    public function showUserById(string $userId): View
    {
        $user = User::findOrFail($userId);
        return view('users.show', ['user' => $user]);
    }

    /**
     * Thing to display a listing of the users.
     */
    public function indexUsers(): View
    {
        $users = User::all();
        return view('users.index', ['users' => $users]);
    }

    /**
     * ¨Thing to show a specific user by their username.
     */
    public function showUserByName(string $userName): View
    {
        $user = User::where('user_name', $userName)->firstOrFail();
        return view('users.show', ['user' => $user]);
    }


    /**
     * Thing for updating the specified user in storage.
     */
    public function updateUser(Request $request, string $userId): view
    {
        $user = User::findOrFail($userId);

        $validatedData = $request->validate([
            'user_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_num' => 'nullable|string|max:20',
        ]);

        $user->update($validatedData);

        return view('users.show', ['user' => $user])->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Thing to remove the specified user from storage.
     */
    public function destroyUser(string $userId): View
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return view('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
