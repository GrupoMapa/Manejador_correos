@extends('layouts.app')

@section('content')
    <!-- Agrega Alertify.js a tu página -->
    <link rel="stylesheet" href="/summary2/assets_facturacion/css/alertify.min.css" />
    <script src="/summary2/assets_facturacion/js/alertify.min.js"></script>
    <link rel="stylesheet" href="/summary2/assets_facturacion/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/summary2/assets_facturacion/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="/summary2/assets_facturacion/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="/summary2/assets_facturacion/js/net_buttons_2.4.1_js_buttons.colVis.min.js"></script>
    <script type="text/javascript" charset="utf8" src="/summary2/assets_facturacion/js/net_buttons_2.4.1_js_dataTables.buttons.min.js"></script>
    <style>
        .paso{
            font-size: 15px;
            color: #0c992a;
        }
        .container2{
            margin-right: 58px;
            margin-left: 32px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            max-width: 200px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        th {
            background-color: #f2f2f2;
        }
        .iconof {
            background: linear-gradient(315deg, #64d48c, #77fda6);
            box-shadow:  -2px -2px 5px #316844,
                        2px 2px 5px #adfff2;
        }
        .iconof:hover{
            border-radius: 2px;
            background: linear-gradient(315deg, #77fda6, #64d48c);
            box-shadow:  -2px -2px 5px #316844,
                        2px 2px 5px #adfff2;
        }
        .iconof_upload_pdf {
            background: linear-gradient(145deg, #c49a7d, #e9b795);
            box-shadow:  2px 2px 2px #cda183,
             -2px -2px 2px #e7b593;
        }
        .iconof_upload_pdf:hover{
            background: linear-gradient(145deg, #eca97b, #c78e68);
            box-shadow:  2px 2px 2px #d0956c,
             -2px -2px 2px #eaa77a;
        }
        .iconof_upload_json {
            background: linear-gradient(145deg, #8f9ace, #aab7f5);
            box-shadow:  2px 2px 2px #95a1d7,
             -2px -2px 2px #a9b5f3;
        }
        .iconof_upload_json:hover{
            background: linear-gradient(145deg, #7285e0, #6070bc);
            box-shadow:  2px 2px 4px #5b69b2,
             -2px -2px 4px #7b8ff0;
        }
        .iconof_view_a{
            border-radius: 2px;
            background: linear-gradient(315deg, #77eefd, #64c8d4);
            box-shadow:  -2px -2px 5px #316268,
                        2px 2px 5px #adffff;
        }
        .iconof_view_a:hover{
            background: linear-gradient(315deg, #64c8d4, #77eefd);
            box-shadow:  -2px -2px 5px #316268,
                        2px 2px 5px #adffff;
        }

        .iconof_marca_final{
            background: linear-gradient(315deg, #77eefd, #64c8d4);
            box-shadow:  -2px -2px 5px #316268,
                        2px 2px 5px #adffff;
        }
        .iconof_marca_final:hover{
            background: linear-gradient(315deg, #64c8d4, #77eefd);
            box-shadow:  -2px -2px 5px #316268,
                        2px 2px 5px #adffff;
        }

        .iconof_stand_button{
            border-radius: 2px;
            width: 30px;
            height: 18px;
            font-size: 17px;
            text-align: -webkit-center;
        }


        .titulo_logo{
            font-family: sans-serif;
            color: greenyellow;
            text-align: -webkit-center;
            min-width: 100%;
        }
        .highlighted {
            background-color: yellow;
        }
        td.celda {
            font-size: 11px;
            padding: 4px !important;
        }
        .fase_dos {
            background-color: #a1c4d0;
        }
        .fase_tres {
            background-color: #d3ffd0;
        }
        .fase_cuatro{
            background-color: #5fc9d5;
        }
        .fase_cinco{
            background-color: #76d6ff;
        }

        .factura_inactiva{
            background-color: #ffa5a5;
        }

        div.mini_listas {
            font-size: 12px;
        }
        /* Estilos impuestos */
            .tributos {
                font-size: 10px;
            }
            .celda_impuesto{
                margin-bottom: 1px;
            }
        /*------------------------------------*/
        .larger-radio label {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
          }
          .larger-radio input[type="radio"] {
            transform: scale(2.5); /* Ajusta este valor para cambiar el tamaño del círculo del radio */
            margin-right: 10px; /* Espacio entre el círculo y el texto */
          }
        .text-container {
            max-width: 250px;
        }
        .tributo{
            border-style: dotted;
            border-width: 1px;
        }
        .reg_diario{
            max-width: 180%;
        }
        .guardado{
            border-color: green;
        }
        .no_guardado{
            border-color: red;
        }
        textarea.save.form-control {
            color: #35e500;
        }
        td.marca {
            background: #8bef36;
        }
    </style>
    <div class="container2">
        <div class="row">
            <div class="col-md-6">
                <h3>{!! $titulo !!}</h3>
            </div>
            <div class="col-md-6" style="text-align: -webkit-right;">
                <strong>Escanner de correos</strong>
            </div>
        </div>
        <hr class="border-bottom">

        <div class="">
            <p class="paso_1 paso">
                {!! $instrucciones[0] !!}
            </p>
            @include('partials.manejador_dte.gestion_liquidaciones')
            <hr class="border-bottom">
            <h6>Liquidaciones activas</h6>
            <p class="paso_2 paso">
                {!! $instrucciones[1] !!}
            </p>
            @include('partials.manejador_dte.liquidaciones_user')
           
        </div>
        <hr class="border-bottom">
        <p class="paso_2 paso">
     
            {!! $instrucciones[2] !!}
        </p>
       
        <div id="tab_facturas" style="overflow: overlay;">
            @if ($reporte && $reporte->first() != null)
                
                <div class="row"> 
                    <div class="col-md-4 row">
                        <div  class="col-md-6">
                            <label> <strong> Inicio </strong></label><br>
                            <input type="date" id="fechaInicio" placeholder="Fecha de inicio">
                        </div>
                        <div class="col-md-6">
                            <label> <strong> Fin </strong></label><br>
                            <input type="date" id="fechaFin"  placeholder="Fecha de final">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label> <strong> Monto </strong></label> <br><input type="number" id="campoDinero" step="0.01" pattern="\d+(\.\d{1,2})?" title="Ingresa un número con hasta dos decimales" required>
                        <button onclick="filtrarPorFechas()" class="btn btn-success">Filtrar</button>
                    </div>
                    <div class="col-md-5">
                        <button  class="btn btn-secondary" id="d_proceso">Datos de proceso</button>
                        <button  class="btn btn-secondary" id="d_empresa">Datos de empresa</button>
                        <button  class="btn btn-secondary" id="d_archivos">Archivos</button>
                        <button  class="btn btn-secondary" id="d_inactiva">Inactivas?</button>
                    </div>
                </div>
                <hr class="border-bottom">
                
                <div class="col-md-12" id="table_facturas">
                @include('partials.manejador_dte.liquidaciones_facturas')
                </div>
             
            @else
                <div class="row"> 
                    <div class="col-md-4 row">
                        <div  class="col-md-6">
                            <label> <strong> Inicio </strong></label><br>
                            <input type="date" id="fechaInicio" placeholder="Fecha de inicio">
                        </div>
                        <div class="col-md-6">
                            <label> <strong> Fin </strong></label><br>
                            <input type="date" id="fechaFin"  placeholder="Fecha de final">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label> <strong> Monto </strong></label> <br><input type="number" id="campoDinero" step="0.01" pattern="\d+(\.\d{1,2})?" title="Ingresa un número con hasta dos decimales" required>
                        <button onclick="filtrarPorFechas()" class="btn btn-success">Filtrar</button>
                    </div>
                    <div class="col-md-5">
                        <button  class="btn btn-secondary" id="d_proceso">Datos de proceso</button>
                        <button  class="btn btn-secondary" id="d_empresa">Datos de empresa</button>
                        <button  class="btn btn-secondary" id="d_archivos">Archivos</button>
                        <button  class="btn btn-secondary" id="d_inactiva">Inactivas?</button>
                    </div>
                </div>
                <hr class="border-bottom">
                <div class="col-md-12" id="table_facturas">
                       
                </div>
               
            @endif
        </div>

        <hr class="border-bottom">

        <div class="card border-info mb-3" id="form_upload" style='display:none'>
            <h5 class="card-header" id="envio_info">Subir archivos PDF Y JSON</h5>
            <div class="card-body text-info">
                <p class="card-text">En caso de que un documento no tenga datos enlazados, subir por este medio y tomar el nombre del archivo para registrarlo</p>
                <p class="card-text"><strong>TODO DOCUMENTO QUE SE SUBA DEBE SER ANALIZADO CON ANTIVIRUS</strong></p>
                <form id="uploadForm" action="{{ route('upload_pdf_json') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <P>Seleccione el archivo<strong id="tipo_subida"> ESTA SUBIENDO DOCUMENTOS EXTERNOS </strong></P>
                    <div class="row col-md-12"> 
                        <div class="col-md-6">

                        </div>
                    </div>
                    <input type="file" class="form-control" id="fileInput" name="fileInput" title="Seleccione el archivo"/>
                    <hr class="border-bottom">
                    <input type="button" value="Subir archivo" onclick="uploadFile()" />
                </form>
                <!-- Elemento para mostrar los nombres de los archivos -->
                <div id="fileNamesDiv"></div>
            </div>
        </div>
            
        @if ( $extra_data['paso'] == 4 )
            <!-- Modal para la lista de empleados -->
            <div class="modal fade" id="empleadosModal" tabindex="-1" role="dialog" aria-labelledby="empleadosModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document"> 
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="empleadosModalLabel">Seleccionar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    <form id="empleadosForm">
                        <div class="form-group">
                        <label for="empleadoSelect">Seleccionar Empleado</label>
                        <select class="form-control" id="empleadoSelect" required>
                            <option value="" disabled selected>Seleccione un empleado</option>
                            <!-- Rellenar el select con las opciones de 'extraData.users' -->
                            @if($extra_data['users'])
                                @foreach ($extra_data['users'] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                    </form>
                    </div>
                </div>
                </div>
            </div>
        @endif

    <script src="/summary2/assets_facturacion/js/clipboard.min.js"></script>
    <link rel="stylesheet" href="/summary2/assets_facturacion/css/selectize.default.min.css"/>
    <script src="/summary2/assets_facturacion/js/selectize.min.js" ></script>

    <!-- scripts de la fase 1 -->
        <script>
            var tipo_estado = ''
            var codigoGeneracion = null
            var value_liquidation = null
            var id_factura = null
            var linea_factura = null
            var boton_asignar_revision = null
            var id_asignar_rev = null
            var tipo_documento = null
            var reloads_facturas = 0 
            const radioLiquidacion = document.querySelectorAll('input[name="id_liquidation_p3"]');
            const radioLiquidacion_f4 = document.querySelectorAll('input[name="id_liquidation_p4"]');
            const radioLiquidacion_0 = document.querySelectorAll('input[name="id_liquidation"]');
            var id_factura = 0
            var tipo_file = 0

            function bloquearPantalla(event = null) {
                document.body.style.cursor = "wait";
                document.body.style.opacity = 0.5;
                if(event != null){
                    console.log(typeof event.srcElement )
                    console.log( event )
                    if(typeof event.srcElement != 'undefined')
                        event.srcElement.style.display = 'none'
                    else
                        event.style.display = 'none'
                }
            }

            function desbloquearPantalla(event = null) {
                document.body.style.cursor = "default";
                document.body.style.opacity = 1;
                if(event != null){
                    console.log(typeof event.srcElement )
                    console.log( event )
                    if(typeof event.srcElement != 'undefined')
                        event.srcElement.style.display = 'inline'
                    else
                        event.style.display = 'inline'
                }
            }
  
            $('#normaSelect').selectize();
            const accion_copiar_icons = () =>{
                const copyIcons = document.querySelectorAll('.copy-icon');
                const copyButtons = document.querySelectorAll('.copy-button');
                copyButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        const cellContent = this.dataset.content;
                        copyToClipboard(cellContent);
                    });
                });
                function copyToClipboard(text) {
                    const dummy = document.createElement('textarea');
                    dummy.value = text;
                    document.body.appendChild(dummy);
                    dummy.select();
                    document.execCommand('copy');
                    document.body.removeChild(dummy);
                }

                copyIcons.forEach(function(icon) {
                    icon.addEventListener('click', function(event) {
                        const titleValue = this.getAttribute('title');
                        copyToClipboard(titleValue);

                        // Cambiar el color de fondo del elemento al que se hizo clic
                        const parentCell = this.closest('td');
                        parentCell.classList.add('highlighted');

                        // Restaurar el color de fondo después de 1 segundo
                        setTimeout(function() {
                            parentCell.classList.remove('highlighted');
                        }, 20000);
                    });
                });
            }

            const render_impuestos = ()=>{
                // Obtener todos los elementos <td> con la clase "specificatios"
                const especificacionesElements = document.querySelectorAll('.specificatios');
                // Función para crear una lista con dos columnas por n filas
                function crearLista(datosJson) {
                    const lista = document.createElement('div');
                    lista.classList = ['tributos'];
                    lista.style.padding = '0px';

                    // Iterar sobre los datos JSON y crear los elementos de la lista
                    for (const item of datosJson) {
                        const listItem = document.createElement('p');
                        listItem.classList = ['celda_impuesto']
                        listItem.textContent = `COD: ${item.codigo}, valor: ${item.valor}`;
                        lista.appendChild(listItem);
                    }
                    return lista;
                }
                // Procesar cada elemento <td> con la clase "specificatios"
                especificacionesElements.forEach((element) => {
                    const dataTitle = element.getAttribute('data-title');
                    if (dataTitle && dataTitle !== 'null') {
                        const jsonData = JSON.parse(dataTitle);
                        
                        // Crear la lista y agregarla dentro del <td>
                        const lista = crearLista(jsonData);
                        element.appendChild(lista);
                    }
                });
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                funct_tables()
                accion_copiar_icons()

                const reload_liquidations= (id_liquidacion = 0) =>{
                    bloquearPantalla()
                    $.ajax({
                        url: '/summary2/f1_reload_liquidations',
                        method: 'POST',
                        success: function (response) {
                            const element = document.getElementById('tabla_liquidaciones').innerHTML = response
                            action_liquidations();
                            $('input[name="id_liquidation"][value="'+id_liquidacion+'"]').prop('checked', true);
                            const radioLiquidacion_0 = document.querySelectorAll('input[name="id_liquidation"]');
                            radioLiquidacion_0.forEach((radioButton) => {
                                set_title_modal(radioButton)
                            });
                            desbloquearPantalla()
                        },
                        error: function (error) {
                            // Manejar errores si es necesario
                            console.error(error);
                            desbloquearPantalla()
                        }
                    });
                }

                const validar_repetidos = (event) =>{
                    const codigo_gen = event.srcElement.dataset.codigoGeneracion
                    const id = event.srcElement.dataset.id
                    bloquearPantalla(event) 
                    $.ajax({
                        url: "/summary2/valid_code_gen",
                        method: "POST", // o "GET" según corresponda
                        data: {
                            codigo_generacion: codigo_gen,
                            id: id
                        },
                        success: function(response) {
                            if (response.status) {
                                $('#asignarModal').modal('show');
                            } else {
                                alertify.error(response.message);
                            }
                            desbloquearPantalla(event)
                        },
                        error: function(xhr, status, error) {
                            // Aquí puedes manejar el error si lo deseas
                            console.error("Error en la petición AJAX:", error);
                            desbloquearPantalla(event)
                        }
                    });
                }
                //Agregar un evento de clic a todos los enlaces con la clase "adjuntar-link"
                document.querySelectorAll('.adjuntar-link').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                        linea_factura= event.srcElement
                        value_liquidation = $('input[name="id_liquidation"]:checked').val();
                        codigoGeneracion = event.srcElement.dataset.codigoGeneracion
                        id_factura = event.srcElement.dataset.id
                        if(typeof value_liquidation != 'undefined'){
                            // realizar la validacion de este otro
                            validar_repetidos(event) 
                        }else{
                            alertify.error("Selecciona un área de la primera tabla, antes de asignar");
                        }
                    });
                });
                //Agregar un evento de clic a todos los enlaces con la clase "adjuntar-link"
                document.querySelectorAll('.reclamar-link').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                        // Mostrar el modal de reclamación cuando se hace clic en el enlace "Reclamar"
                        $('#reclamarModal').modal('show');
                        // Obtener el valor de "codigoGeneracion" desde el atributo "data-codigo-generacion"
                        //codigoGeneracion = this.getAttribute('data-codigo-generacion');
                    });
                });

                
                @if ( $extra_data['paso'] == 2 )
                    // Manejar la sumisión del formulario de reclamación
                    document.getElementById('asignarForm').addEventListener('submit', function (event) {
                        event.preventDefault(); // Prevenir la sumisión del formulario
                      
                        // Obtener los valores del formulario
                        var normaId = document.getElementById('normaSelect').value;
                        var descripcion = document.getElementById('descripcion').value;
                        // Realizar la solicitud AJAX con los datos recolectados del formulario
                        bloquearPantalla(event) 
                        $.ajax({
                        url: '/summary2/f1_asignar_factura',
                        method: 'POST',
                        data: {
                            codigoGeneracion: codigoGeneracion,
                            id_factura: id_factura,
                            value_liquidation: value_liquidation,
                            normaId: normaId,
                            descripcion: descripcion
                        },
                        success: function (response) {
                            // Resto del código para procesar la respuesta AJAX
                            codigoGeneracion = null
                            if (response.status) {
                                alertify.success(response.message);
                                reload_liquidations(value_liquidation)
                                linea_factura.parentElement.parentElement.style.backgroundColor= 'rgb(198 250 184)'
                                const formulario = document.getElementById('asignarForm')
                                formulario.reset()
                                var selectizeInstance = $('#normaSelect').eq(0).get(0).selectize;
                                // Selecciona la primera opción
                                selectizeInstance.setValue();
                                $('#asignarModal').modal('hide');
                                linea_factura.parentElement.parentElement.style.color = 'blue'
                                //linea_factura.revome()
                            } else {
                                alertify.error(response.message);
                            }
                            desbloquearPantalla(event)
                        },
                        error: function (error) {
                            // Manejar errores si es necesario
                            console.error(error);
                            desbloquearPantalla(event)
                        }
                        });

                        // Cerrar el modal después de enviar la solicitud AJAX
                        $('#reclamarModal').modal('hide');
                    });

                     // Manejar la sumisión del formulario de reclamación
                    document.getElementById('reclamarForm').addEventListener('submit', function (event) {
                        event.preventDefault(); // Prevenir la sumisión del formulario
                        // Obtener los valores del formulario
                        var areaId = document.getElementById('areaSelect').value;
                        var normaId = document.getElementById('normaSelect').value;
                        var descripcion = document.getElementById('descripcion_asign').value;

                        // Realizar la solicitud AJAX con los datos recolectados del formulario
                        bloquearPantalla(event) 
                        $.ajax({
                        url: '/summary2/f1_iniciar_liquidacion',
                        method: 'POST',
                        data: {
                            codigoGeneracion: codigoGeneracion,
                            areaId: areaId,
                            normaId: normaId,
                            descripcion: descripcion
                        },
                        success: function (response) {
                            // Resto del código para procesar la respuesta AJAX
                            codigoGeneracion = null
                            if (response.status) {
                                alertify.success(response.message);
                                reload_liquidations();
                                /*var idsActualizados = response.idsActualizados;
                                $('.data_id').each(function () {
                                    var idFila = $(this).text(); // Obtener el contenido innertext de la celda
                                    if (idsActualizados.includes(parseInt(idFila))) {
                                        $(this).closest('tr').addClass('fase_dos');
                                    }
                                });*/
                            } else {
                                alertify.error(response.message);
                            }
                            const formulario = document.getElementById('reclamarForm')
                            formulario.reset()
                            desbloquearPantalla(event)
                        },
                        error: function (error) {
                            // Manejar errores si es necesario
                            console.error(error);
                            desbloquearPantalla(event)
                        }
                        });

                        // Cerrar el modal después de enviar la solicitud AJAX
                        $('#reclamarModal').modal('hide');
                    });

                    

                @endif

                

                const btnSubir = document.querySelector(".btn_subir");
                if( btnSubir != null ){
                    console.log('asdasda')
                    btnSubir.addEventListener("click", function() {
                        const formUpload = document.querySelector("#form_upload");
                        formUpload.style.display = "block";
                    });
                }
                    
            
                document.querySelectorAll('.asignar-revision').forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
                        // Obtener el valor de "codigoGeneracion" desde el atributo "data-codigo-generacion"
                        boton_asignar_revision = event.srcElement
                        id_asignar_rev = this.getAttribute('data-id');
                        tipo_documento = this.getAttribute('data-tipo');
                        // Mostrar el segundo modal para seleccionar el empleado
                        $('#empleadosModal').modal('show');
                        
                        // Manejar la sumisión del formulario de selección de empleado
                        
                    });
                });
                
                @if ( $extra_data['paso'] == 4 )
                    document.getElementById('empleadosForm').addEventListener('submit', function (event) {
                        event.preventDefault(); // Prevenir la sumisión del formulario
                        console.log('prevenido')
                        // Obtener el valor seleccionado del empleado
                        var idEmpleadoAsignado = document.getElementById('empleadoSelect').value;
                        // Realizar la solicitud AJAX con los datos recolectados del formulario
                        bloquearPantalla(event) 
                        $.ajax({
                            url: '/summary2/asignar_empleado',
                            method: 'POST',
                            data: {
                                id: id_asignar_rev,
                                id_empleado_asignado: idEmpleadoAsignado, 
                                tipo_documento: tipo_documento
                            },
                            success: function (response) {
                                // Resto del código para procesar la respuesta AJAX
                                if (response.status) {
                                    var idsActualizados = response.idsActualizados;
                                    boton_asignar_revision.style.display = 'none'
                                    $('.data_id').each(function () {
                                        var idFila = $(this).text(); // Obtener el contenido innertext de la celda
                                        if (idsActualizados.includes(parseInt(idFila))) {
                                        $(this).closest('tr').addClass('fase_cinco');
                                        }
                                    });
                                } else {
                                    alert(response.message);
                                }
                                $('#empleadosModal').modal('hide'); // Cerrar el modal de selección de empleado
                                const formulario = document.getElementById('empleadosForm')
                                formulario.reset()
                                desbloquearPantalla(event)
                            },
                            error: function (error) {
                                // Manejar errores si es necesario
                                console.error(error);
                                $('#empleadosModal').modal('hide'); // Cerrar el modal de selección de empleado en caso de error
                                desbloquearPantalla(event)
                            }
                        });
                    });
                @elseif ( $extra_data['paso'] == 3 || $extra_data['paso'] == 6 )
                     // Agregar evento clic a los elementos con la clase "num-partida-link"
                    $(".num-partida-link").on("click", function(event) {
                        console.log("num partida")
                        var dataInputValue = $(this).data("input");
                        // Buscar el input con la clase igual al valor de data-input
                        var codeSap = $("input." + dataInputValue);
                        
                        // Verificar si se encontró el input
                        if (codeSap.length > 0) {
                            // Obtener el valor del input encontrado
                            var codeSapValue = codeSap.val();
                            // Obtener el valor de data-codigo-generacion
                            var codGenValue = $(this).data("codigo-generacion");

                            // Verificar si el campo de texto está vacío
                            if (codeSapValue.trim() === "") {
                                // Mostrar mensaje de Alertify
                                alertify.error("El campo de texto está vacío");
                                return; // Detener la ejecución, evitando enviar la petición AJAX
                            }
                            bloquearPantalla(event) 
                            $.ajax({
                                url: "/summary2/rev_tesoreria",
                                method: "POST", // o "GET" según corresponda
                                data: {
                                    code_sap: codeSapValue,
                                    cod_gen: codGenValue
                                },
                                success: function(response) {
                                    if (response.status) {
                                        var idsActualizados = response.idsActualizados;
                                        $('.data_id').each(function () {
                                            var idFila = $(this).text(); // Obtener el contenido innertext de la celda
                                            if (idsActualizados.includes(parseInt(idFila))) {
                                            $(this).closest('tr').addClass('fase_cuatro');
                                            }
                                        });
                                    } else {
                                        alert(response.message);
                                    }
                                    desbloquearPantalla(event)
                                },
                                error: function(xhr, status, error) {
                                    // Aquí puedes manejar el error si lo deseas
                                    console.error("Error en la petición AJAX:", error);
                                    desbloquearPantalla(event)
                                }
                            });
                            
                        }
                    });
                @endif


                //----------------------------render impuestos------------------------------------------
                    
                    render_impuestos()

                //----------------------------------------------------------------------


            });
            
            function validarRadiobuttons() {
                // Obtén todos los elementos de tipo radiobutton con la clase 'num-partida-link'
                var radiobuttons = document.querySelectorAll('.num-partida-link');
                var todosMarcados = true;
                if(radiobuttons.length == 0){
                    alertify.warning('Debes revisar cada item y marcarlo antes de continuar');
                    return false
                }
                for (var i = 0; i < radiobuttons.length; i++) {
                    if (!radiobuttons[i].checked) {
                        // Si encuentra uno que no está marcado en 'true', establece la variable a 'false' y sale del bucle
                        todosMarcados = false;
                    }
                }
                // Verifica el resultado de la validación y muestra el mensaje con Alertify
                if (todosMarcados == false) {
                    alertify.warning('No has marcado todos los items de esta liquidación');
                }
                return todosMarcados
            }
            
            function filtrarPorFechas() {

                var fvali = document.getElementById("fechaInicio").value
                var fvalf = document.getElementById("fechaFin").value
                var fechaInicio = fvali != '' ? new Date(fvali):'';
                var fechaFin = fvalf != '' ? new Date(fvalf):'';
                var monto = document.getElementById("campoDinero").value;
                
                var tabla = document.getElementById("facturas_recolectadas").getElementsByTagName('tbody')[0];
                var filas = tabla.getElementsByTagName('tr');
                
                for (var i = 0; i < filas.length; i++) {
                    var celdaFecha = filas[i].getElementsByTagName('td')[5];
                    var montoCelda = filas[i].getElementsByTagName('td')[3].innerText;
                    var fechaCelda = new Date(celdaFecha.innerText.replace(/-/g, '/').replace(' ', 'T'));
                    console.log(fechaInicio,
                        (fechaInicio == '' || fechaCelda >= fechaInicio) , 
                        (fechaFin == '' || fechaCelda <= fechaFin) ,
                        (monto=='' || monto == montoCelda)
                    )
                    if(
                        (fechaInicio == '' || fechaCelda >= fechaInicio) && 
                        (fechaFin == '' || fechaCelda <= fechaFin) && 
                        (monto=='' || monto == montoCelda)
                    ){
                        filas[i].style.display = '';
                    } else {
                        filas[i].style.display = 'none';
                    }
                }
            }

            const sendData = ($url, id, boton, tipo_estado) =>{
                bloquearPantalla( boton ) 
                $.ajax({
                    url: $url,
                    method: 'POST', data: {
                        id: id,
                        num_documento: '',
                        tipo_estado
                    },
                    success: function (response) {
                        if (response.status) {
                            alertify.success(response.message);
                            /*boton.innerText = 'Listo!'
                            boton.disabled = true*/
                            boton.remove()
                          
                        } else {
                            alertify.error(response.message);
                        }
                        desbloquearPantalla(boton)
                    },
                    error: function (error) {
                        // Manejar errores si es necesario
                        console.error(error);
                        desbloquearPantalla(boton)
                    }
                });
            }

            const cancelfunc = () =>{
                alertify.error('Cancel');
            }
        
            const send_estado = (button, $url, $validarRadiobuttons=true) =>{
                button.addEventListener('click', function(event, $validarRadiobuttons) {
                    const id = event.srcElement.dataset.id
                    const boton = event.srcElement
                    if(validarRadiobuttons() || tipo_estado == 'exclu'){
                        alertify.confirm('Esta seguro, recuerde verificar bien los datos!', 
                            function( ){
                                var evt = null
                                sendData($url, id, boton, tipo_estado)
                            }
                            ,
                            function(){
                                alertify.error('Cancel');
                            }
                        );
                    } 
                });
            }

            const send_liquidation = document.querySelectorAll('.send_liquidation_p3');
            send_liquidation.forEach(function(button){
                send_estado(button, '/summary2/f2_enviar', validarRadiobuttons)
            });
            const send_liquidation_dat = document.querySelectorAll('.send_liquidation_p4');
            send_liquidation_dat.forEach(function(button){
                //send_estado(button, '/summary2/f3_enviar')
                //empleadosModal
            });
            const send_liquidation_p5 = document.querySelectorAll('.send_liquidation_p5');
            send_liquidation_p5.forEach(function(button){
                send_estado(button, '/summary2/f5_enviar', validarRadiobuttons)
            });

            const iniciar_botones_pdf = () => {
                // Obtén todos los elementos con la clase "data_tributos"
                var elementos = document.querySelectorAll(".data_tributos");
                // Agrega un controlador de eventos clic a cada uno de ellos
                elementos.forEach(function(elemento) {
                    elemento.addEventListener("click", function(event) {
                        // Obtiene el valor del atributo "data-id" del elemento clicado
                        var dataId = elemento.getAttribute("data-id");
                        // Realiza la solicitud AJAX GET
                        $.ajax({
                            url: "/summary2/ver_impuestos_factura?id=" + dataId,
                            type: "GET",
                            success: function(respuesta) {
                                respuesta = JSON.parse(respuesta)
                                // Obtiene el div donde se mostrará la lista
                                var resultado = event.srcElement.parentElement
                                // Crea una lista no ordenada (ul)
                                var lista = document.createElement("ul");
                                // Itera sobre los elementos json_tributos
                                console.log(respuesta)
                                respuesta.json_tributos.forEach(function(tributo) {
                                    // Crea un elemento de lista (li)
                                    var elementoLista = document.createElement("li");
                                    // Establece el título como el valor de descripción
                                    elementoLista.title = tributo.descripcion;
                                    // Crea un elemento span para mostrar el código y el valor
                                    var elementoSpan = document.createElement("span");
                                    elementoSpan.innerText = "Código: " + tributo.codigo + ", Valor: " + tributo.valor;
                                    // Agrega el elemento span como hijo del elemento de lista
                                    elementoLista.appendChild(elementoSpan);
                                    
                                    // Agrega el elemento de lista a la lista no ordenada
                                    lista.appendChild(elementoLista);
                                });
                                // Limpia el contenido anterior del resultado y agrega la nueva lista
                                resultado.innerHTML = "";
                                resultado.appendChild(lista);
                            }
                        });
                    });
                });
            };
      
            /*------------------------RADIO BUTTONS---------------------------------------*/
            const  reload_facts = (radioButton) => {
                radioButton.addEventListener('click', () => {
                    // Llamamos a la función handleRadioButtonClick pasando el valor del radio button seleccionado.
                    handleRadioButtonClick(radioButton.value, radioButton.dataset.tipo );
                });
            }
            const  set_title_modal = (radioButton) => {
                radioButton.addEventListener('click', (event) => {
                    const texto = event.srcElement.parentElement.parentElement.parentElement.children[1].children[0].children[0].innerText
                    console.log(texto)
                    const element = document.getElementById('area_trabajo')
                    element.innerText = texto
                });
            }

            
            const validarNumDiario = (num_diario) => {
                // Usamos una expresión regular para verificar si la cadena contiene solo números y letras sin espacio.
                var regex = /^[a-zA-Z0-9_-]+$/; 
                
                // Probamos la cadena con la expresión regular.
                if (regex.test(num_diario)) {
                return true; // La cadena es válida.
                } else {
                return false; // La cadena no es válida.
                }
            }
            
            // Agregamos un event listener a cada radio button
            radioLiquidacion.forEach((radioButton) => {
                reload_facts(radioButton)
            });
            radioLiquidacion_f4.forEach((radioButton) => {
                reload_facts(radioButton)
            });
            radioLiquidacion_0.forEach((radioButton) => {
                set_title_modal(radioButton)
            });

            const action_send_num_p = ()=>{
                $('.send_num_partida').on('click', function () {
                    var tdElement = $(this).closest('td');
                    var inputElement = tdElement.find('.asig_num_doc_diario');
                    var dataId = inputElement.data('id');
                    var num_diario = inputElement.val();
                    const radioButton = document.querySelector("input[name='id_liquidation_p3']:checked");
                    const data_tipo = radioButton.dataset.tipo;
                    if( validarNumDiario(num_diario) ){
                        $.ajax({
                            url: '/summary2/f5_registro_diario',
                            type: 'POST', // Puedes ajustar el método según tus necesidades
                            data: { dataId: dataId, num_diario: num_diario, data_tipo },
                            success: function (response) {
                                console.log('33333')
                                if (response.status) {
                                    alertify.success(response.message);
                                    inputElement.removeClass('no_guardado')
                                    inputElement.addClass('guardado')
                                } else {
                                    alertify.error(response.message);
                                    inputElement.removeClass('guardado')
                                    inputElement.addClass('no_guardado')
                                }
                                //bloquearPantalla(this) 
                            },
                            error: function (error) {
                                // Manejar errores si es necesario
                                console.error(error);
                                //bloquearPantalla(this) 
                            }
                        });
                    }else{

                        this.checked = false
                        alertify.error('El número de registro diario parece inválido');
                    }
                });

                $('.send_data_extra').on('click', function () {
                    var tdElement = $(this).closest('td');
                    var inputElement = tdElement.find('.asig_data_extra');
                    var dataId = inputElement.data('id');
                    var dataTipo = inputElement.data('tipo');
                    var dato_extra = inputElement.val();
                    //desbloquearPantalla(this)
                    if(dato_extra == '' || dato_extra ==' '){
                        alertify.error('El dato esta vacío');
                    }else{
                        $.ajax({
                            url: '/summary2/fx_registro_extra',
                            type: 'POST', // Puedes ajustar el método según tus necesidades
                            data: { dataId: dataId, dato_extra: dato_extra, data_tipo: dataTipo },
                            success: function (response) {
                                if (response.status) {
                                    alertify.success(response.message);
                                    inputElement.removeClass('no_guardado')
                                    inputElement.addClass('save')
                                } else {
                                    alertify.error(response.message);
                                    inputElement.removeClass('save')
                                    inputElement.addClass('no_guardado')
                                }
                                //bloquearPantalla(this) 
                            },
                            error: function (error) {
                                // Manejar errores si es necesario
                                console.error(error);
                                //bloquearPantalla(this) 
                            }
                        });
                    }
                });

            }
            const action_send_num_reg = ()=>{
                $('.send_num_registro').on('click', function (event) {
                    var tdElement = $(this).closest('td');
                    var inputElement = tdElement.find('.asig_num_entrada');
                    var dataId = inputElement.data('id');
                    var num_reg_mercad = inputElement.val();
                    console.log('asig_num_entrada')
                    if( validarNumDiario(num_reg_mercad) ){
                        bloquearPantalla()
                        $.ajax({
                            url: '/summary2/f3_registro_num_entrada',
                            type: 'POST', // Puedes ajustar el método según tus necesidades
                            data: { dataId: dataId, num_reg_mercad: num_reg_mercad },
                            success: function (response) {
                                console.log('33333')
                                if (response.status) {
                                    alertify.success(response.message);
                                    inputElement.removeClass('no_guardado')
                                    inputElement.addClass('guardado')
                                } else {
                                    alertify.error(response.message);
                                    inputElement.removeClass('guardado')
                                    inputElement.addClass('no_guardado')
                                }
                                desbloquearPantalla() 
                            },
                            error: function (error) {
                                // Manejar errores si es necesario
                                console.error(error);
                                desbloquearPantalla() 
                            }
                        });
                    }else{

                        this.checked = false
                        alertify.error('El número de registro diario parece inválido');
                    }
                  
                });
            }

            const action_send_marca_final = ()=>{
                $('.iconof_marca_final').on('click', function (event) {
                    window.evt = event
                    const dataId = event.currentTarget.dataset.id
                    const tipo = event.currentTarget.dataset.tipo
                    bloquearPantalla()
                    $.ajax({
                        url: '/summary2/f6_marca_final/'+dataId+'/'+tipo,
                        type: 'GET', // Puedes ajustar el método según tus necesidades
                        data: {  },
                        success: function (response) {
                            if (response.status) {
                                alertify.success(response.message);
                                //inputElement.removeClass('no_guardado')
                                //inputElement.addClass('guardado')
                            } else {
                                alertify.error(response.message);
                                //inputElement.removeClass('guardado')
                                //inputElement.addClass('no_guardado')
                            }
                            desbloquearPantalla() 
                        },
                        error: function (error) {
                            // Manejar errores si es necesario
                            console.error(error);
                            desbloquearPantalla() 
                        }
                    });
                });
            }
            action_send_marca_final()
            
            
            const iniciar_tab = () => {
                
                const mostrar_formulario=( id_fac, tipo )=>{
                    const formUpload = document.querySelector("#form_upload");
                    formUpload.style.display = "block";
                    alertify.success(`Para subir un documento relacionado al documento ${id_fac}, ve hasta abajo de la pantalla, selecciona el documento y da click en subir archivo `);
                    id_factura = id_fac
                    tipo_file = tipo
                }    
           
                const elements = document.querySelectorAll('.iconof_upload_json, .iconof_upload_pdf');
                elements.forEach(element => {
                    element.addEventListener('click', (event) => {
                        const id_fac = event.srcElement.dataset.id;
                        const tipo = event.srcElement.dataset.tipo
                        mostrar_formulario(id_fac, tipo)
                        const titulo = document.getElementById('envio_info')
                        titulo.innerText = "Subir archivos PDF Y JSON del documento con ID:"+ id_fac + " subir: " + tipo

                    });
                });
            }

            function handleRadioButtonClick(value, tipo) {
                var tabla = document.getElementById('table_facturas')
                tabla.innerHTML = ''
                bloquearPantalla()
                tipo_estado = tipo
                $.ajax({
                    url: '/summary2/f2_reload_facturas_from_liqu',
                    method: 'GET', data: {
                        id: value,
                        paso: {{ $extra_data['paso'] }},
                        zona: {{ isset($extra_data['zona']) ? $extra_data['zona'] : '-1' }},
                        tipo: tipo
                    },
                    success: function (response) {
                        var tabla = document.getElementById('table_facturas')
                        tabla.innerHTML = ''
                        tabla.innerHTML = response
                        funct_tables(  )
                        accion_copiar_icons()
                        render_impuestos()
                        action_send_num_p()
                        action_send_num_reg()
                        desbloquearPantalla()
                        iniciar_tab()
                        //action_send_marca_final()
                    },
                    error: function (error) {
                        // Manejar errores si es necesario
                        console.error(error);
                        desbloquearPantalla()
                    }
                });
            }
        /*------------------------------------------------------------------------------------*/
        function uploadFile() {
            // Obtiene el formulario y el archivo
            var form = document.getElementById('uploadForm');
            var fileInput = document.getElementById('fileInput');
            var file = fileInput.files[0];

            // Crea un objeto FormData y agrega el archivo
            var formData = new FormData();
            formData.append('file', file);
            formData.append('tipo_file', tipo_file);
            formData.append('id_factura', id_factura);
            // Realiza la solicitud AJAX
            $.ajax({
                url: '{{ route("upload_pdf_json") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success){
                        var fileNamesDiv = document.getElementById('fileNamesDiv');
                        var fileNameSpan = document.createElement('span');
                        fileNameSpan.innerText = response.fileName;
                        fileNamesDiv.appendChild(fileNameSpan);
                        var lineBreak = document.createElement('br');
                        fileNamesDiv.appendChild(lineBreak);
                        alertify.success(`archivo para ${id_factura} subido correctamente!`);
                    }else{
                        alertify.error(response.message);
                    }

                },
                error: function(error) {
                    
                        alert('hubo un error, ojo los tipos admitidos son PDF Y JSON')
                 
                }
            });
        }
        </script>
    </div>
@endsection
