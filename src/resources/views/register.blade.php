@extends('layouts.app')

@section('content')
  

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-dark mb-3">
                <div class="card-header">
                    <h1 class="text-center">Registro</h1>
                </div>
                <div class="card-body">
                      
                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                 
           
                       
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="form-group text-center">
                                <button class="btn btn-primary" id="btn-registrar">Registrarse</button>
                            </div>
                       
                    </form>
                </div>
        </div>
    </div>
</div>
    <script>
        $(document).ready(function() {
            /*$('#btn-registrar').click(function(event) {
                event.preventDefault
                // Enviamos los datos del formulario por ajax
                $.ajax({
                    url: "{{ route('register') }}",
                    type: 'post',
                    data: {
                        name: $('#name').val(),
                        email: $('#email').val(),
                        password: $('#password').val(),
                        password_confirmation: $('#password_confirmation').val(),
                    },
                    success: function(respuesta) {
                        // Si la respuesta es exitosa, redirigimos al usuario a la página principal
                        if (respuesta.success) {
                            window.location.href = '/';
                        } else {
                            // Si la respuesta no es exitosa, mostramos un mensaje de error
                            alert(respuesta.message);
                        }
                    },
                });
            });*/
        });
    </script>
@endsection
