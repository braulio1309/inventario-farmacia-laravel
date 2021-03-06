<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Datatables;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function getUser(){
        
        $user = User::all();

        return view('user/lista', [
            'users' => $user
        ]);
    }

    public function create(){
        return view('user/create');
    }

    public function createUser(Request $request){

    //validamos los datos
        $validate = \Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'rol'      => 'required',
            'password'  => 'required',
        ]);

        if($validate->fails()){
            $request->session()->flash('alert-danger', 'Error al ingresar usuario');

            return redirect()->back();
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->rol = $request->input('rol');
        $user->save();
        $request->session()->flash('alert-success', 'Usuario registrado con exito!');


        return redirect()->route('user.lista');
    }

    public function update($id){
        $user = User::where('id', $id)->first();

        return view('user/create', [
            'user' => $user
        ]);

    }

    public function updateUser(Request $request, $user_id){

        $user = User::where('id', $user_id)->first();

        //validamos los datos
        $validate = \Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required',
            'rol'      => 'required',
            'password'  => 'required',
        ]);

        if($validate->fails()){
            $request->session()->flash('alert-danger', 'Error al ingresar usuario');

            return redirect()->back();
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->rol = $request->input('rol');
        $user->save();

        $request->session()->flash('alert-success', 'Usuario actualizado con exito!');


        return redirect()->route('user.lista');
    }
    

    public function deleteUser(){
        
    }
}
