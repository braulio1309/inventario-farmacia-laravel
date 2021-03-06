@extends('adminlte::page')
@section('title', 'Usuarios')

@section('content_header')
<div class="card">
    <div class="card-header">
      <h2>Usuarios</h2>
    </div>
    
  </div>
    
@endsection

@section('content')



<div class="container">
    <a href="{{route('user.create.vista')}}" class="btn btn-primary mb-2">Añadir nuevo</a>
    @foreach (['danger', 'warning', 'success', 'info'] as $msg) 
      @if(Session::has('alert-' . $msg)) 
        <div class="alert {{'alert-' . $msg}} alert-dismissable">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          {{ Session::get('alert-' . $msg) }} 
        </div>
        
        @endif 
    @endforeach 
    <br>
   <table class="table table-striped">
        <thead>
          <tr>
            <th >#</th>
            <th >Nombre</th>
            <th>Email</th>
            <th >Rol</th>
            <th >Acción</th>

          </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <th >{{$user->id}}</th>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->rol}}</td>
                <td><a href="{{route('user.update.vista', $user->id)}}" class="btn btn-success mb-2">Editar</a>
                </td>

            </tr>
          @endforeach
         
        </tbody>
      </table>
</div>



@endsection
