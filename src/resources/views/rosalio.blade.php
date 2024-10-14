@extends('layout.rosalio')

@section('header1')
    Producto
@endsection

@section('content')
    <form class="row g-3 needs-validation" action="{{route("producto.store")}}" method="POST" novalidate>
        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
            <div class="invalid-feedback">
                Ingrese un nombre
            </div>
        </div>
        <div class="col-md-6">
            <label for="precio" class="form-label">Precio</label>
            <div class="input-group has-validation">
                <span class="input-group-text" id="inputGroupPrepend">$</span>
                <input type="number" step="0.01" class="form-control" name="precio" id="precio"
                    aria-describedby="inputGroupPrepend" required>
                <div class="invalid-feedback">
                    Ingrese un precio.
                </div>
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Agregar producto</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
                })
            })()
    </script>
@endsection
