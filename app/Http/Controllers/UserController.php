<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function store(Request $request) {

         $validatedData = $request->validate([
        'email' => 'required|email|unique:users',
        'username' => 'required|string|unique:users',
        'account_type' => 'required|string',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'password' => 'required|string|min:8',
        'confirm_password' => 'required|same:password'
    ]);
        try {

            $user = new User();
            $user->email = $request->email;
            $user->username = $request->username;
            $user->account_type = $request->account_type;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
        // Additional logic if the user is successfully saved

        $user->save();

        return response()->json(['message' => 'User saved successfully'], 200);
            

            

        } catch (\Exception $e) {

            // $error = $e->getMessage();

            // return response()->json(["error", $error]);
            return response()->json(['error' => 'Unable to save user', 'details' => $e->getMessage()], 422);
            // return redirect()->back()->with("error", $e->getMessage());
        }
    }


    public function myAccount($id) {

        $myAccount = User::findOrFail($id);
         return response()->json(['message' => $myAccount]);
    }


    public function editAccount(Request $request, User $user, $id) {
        try {
            $userToUpdate = $user::findOrFail($id);

         if (empty($request['password'])) {
            return response()->json(['error' => 'Password empty!'], 422);
        }

        if (!Hash::check($request['password'], $userToUpdate->password)) {
            return response()->json(['error' => 'Password is incorrect!'], 422);
        }

        if($userToUpdate->first_name == $request['first_name'] && $userToUpdate->last_name == $request['last_name'] && $userToUpdate->email == $request['email'] && $userToUpdate->username == $request['username']) {
            return response()->json(['error' => 'The provided data matches the existing information in the database. No changes were made to update your account.'], 422);
        }
        
        $userToUpdate->first_name = $request['first_name'];
        $userToUpdate->last_name = $request['last_name'];
        $userToUpdate->email = $request['email'];
        $userToUpdate->username = $request['username'];

        $userToUpdate->save();
        

         return response()->json(['success' => true]);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
