<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

        
        <script src="https://code.jquery.com/jquery-3.7.1.js" type="text/javascript"></script>
       
        <meta charset="utf-8">
      

        <title>Gestor de archivos DTE</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

         <!-- Importaciones de CSS -->
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

        <!-- Importaciones de JavaScript -->

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

       
        <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.dataTables.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.print.min.js"></script>
        <link href="https://cdn.datatables.net/2.0.0/css/dataTables.dataTables.css" rel="stylesheet">
        <!-- Styles -->
 
    </head>
    <body class=" col-md-12">
        <style>
            /* Estilo para disminuir el tamaño de la letra en las celdas de encabezado */
            #tablaDatos th {
                font-size: 12px; /* Cambia el tamaño de la letra según tus preferencias */
            }

            /* Estilo para disminuir el tamaño de la letra en las celdas de datos */
            #tablaDatos td {
                font-size: 10px; /* Cambia el tamaño de la letra según tus preferencias */
            }
            .left-col {
                float: left;
                width: 33%; /* Ajusta el ancho según sea necesario */
            }

            .center-col {
                float: left;
                width: 33%; /* Ajusta el ancho según sea necesario */
            }

            .right-col {
                float: left;
                width: 33%; /* Ajusta el ancho según sea necesario */
            }

        </style>

        <div class="selection:bg-red-500 selection:text-white">
            @if (Route::has('login'))
                <?php
                $fechaActual = date('Y-m-d');
                ?>

                <div class="">
                    @auth
                       
                        <a href="{{ route('logout') }}" class="btn btn-secondary">Cerrar</a>
                        <a href="http://json2table.com" class="btn btn-secondary" target='blank' >Visualizador JSON</a>

                        <a href="https://almacenesbomba.com/api/factura-electronica/json/" class="btn btn-primary" target='blank' >Busqueda DTES bomba, improcasa, megatelas</a>
                        <a href="https://www.tiendaspremiumcenter.com/api/factura-electronica/json/" class="btn btn-secondary" target='blank' >Busqueda DTES premium</a>
                        <a href="/{{ config('app.BITACORA') }}/{{ $fechaActual }}.json" class="btn btn-secondary" target='blank' >Bitacora de eventos</a>
                        <a href="{{config('app.APP_URL')}}/json_view" class="btn btn-secondary" target='blank' >Visualizador de bitacora</a>
                        
                        
                        @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="col-md-12">
                
                
                <div class="row col-md-12">
                    <div class="col-md-10">
                        <h1 class="text-center">
                            Manejador de documentos
                        </h1>
                    </div>
                    <div class="col-md-2">
                        <div class="flex justify-center">
                        <img src="{{ env( 'URL_IMAGE') }}" alt="tiendas premium center el salvador" width="50">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="row col-md-12 " style="margin: 5px;background-color: #cfc9c6;border-radius: 5px;">
                        <div class="col-md-3">
                            <div class="row"><label for="tipoDTE">Fecha de emisión:</label> </div>
                            <div class="row"> 
                                <!-- Campo de fecha con datepicker -->
                                <label for="fechaInicio">Inicio:</label>
                                <input type="text" id="fechaInicio">
                            </div>
                            <br>
                            <div class="row"> 
                                <!-- Campo de fecha con datepicker -->
                                <label for="fechaFin">Fin:</label>
                                <input type="text" id="fechaFin">
                            </div>
                        </div>
                        <div class="col-md-3">
                             <!-- Select múltiple con Select2 -->
                             <div class="row"><label for="tipoDTE">Tipo DTE:</label> </div>
                             <div class="row">
                                <select id="tipoDTE" multiple="multiple">
                                    <option value="01"> 01 - Factura</option>
                                    <option value="03"> 03 - Comprobante de crédito fiscal</option>
                                    <option value="04"> 04 - Nota de remisión</option>
                                    <option value="05"> 05 - Nota de crédito</option>
                                    <option value="06"> 06 - Nota de débito</option>
                                    <option value="07"> 07 - Comprobante de retención</option>
                                    <option value="08"> 08 - Comprobante de liquidación</option>
                                    <option value="09"> 09 - Documento contable de liquidación</option>
                                    <option value="11"> 11 - Facturas de exportación</option>
                                    <option value="14"> 14 - Factura de sujeto excluido</option>
                                    <option value="15"> 15 - Comprobante de donación</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row"><label for="nit">NIT EMISOR</label> </div>
                            <input type="text" id="nit">
                        </div>
                        <div class="col-md-3">
                            <div class="row"><label for="nombre">NOMBRE COMERCIAL</label> </div>
                            <input type="text" id="nombre">
                            <button id="btnRecargar" class="btn btn-primary">
                                Recargar tabla
                            </button>
                        </div>
                    </div>
                    <div class="row col-md-12 ">
                        <table id="tablaDatos">
                        
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Inicialización de datepicker
            flatpickr("#fechaInicio", {
                dateFormat: "Y-m-d", // Formato de fecha
            });
            flatpickr("#fechaFin", {
                dateFormat: "Y-m-d", // Formato de fecha
            });

            // Inicialización de Select2
            $('#tipoDTE').select2({ width: '100%' });
        </script>
        <script id="example-js">
            $(document).ready(function() {

                new DataTable('#tablaDatos',{
                    lengthMenu: [
                        [ 10, 20, 50, 100, -1 ],
                        [ '10 filas', '20 filas', '50 filas', '100 filas', 'Todas' ],
                    ],pageLength: 20,
                    "dom": '<"top"<"left-col"l><"center-col"B><"right-col"f>>rtip',
                    //searching: true,
                    layout: {
                        topEnd: {
                            buttons: ['lengthChange'],
                        },
                        top: {
                            buttons: [ 'csvHtml5', 'excel']
                        },
                        lengthChange: true
                    },
                    ajax: {
                        url: '/api/summary2/tabla_datos',
                        type: 'GET',
                        data: function(d) {
                            // Agrega los parámetros de filtrado aquí
                            d.tipo_dte = $('#tipoDTE').val(); // Valor seleccionado del tipo de DTE
                            d.fecha_inicio = $('#fechaInicio').val(); // Valor seleccionado de la fecha de inicio
                            d.fecha_fin = $('#fechaFin').val(); // Valor seleccionado de la fecha de fin
                            d.nit = $('#nit').val(); // Valor del campo NIT
                            d.nombre = $('#nombre').val(); // Valor del campo Nombre Comercial
                        },
                        dataSrc: 'data'
                        },
                        "columns": [
                            { "data": "id", "title": "ID" },
                            { "data": "tipo_dte", "title": "Tipo DTE" },
                            { "data": "nit", "title": "NIT" },
                            { "data": "totalPagar", "title": "Total a Pagar" },
                            { "data": "monto_sujeto_percepcion", "title": "Monto Sujeto a Percepción" },
                            { "data": "valor_operaciones", "title": "Valor de Operaciones" },
                            { "data": "iva_percibido", "title": "IVA Percibido" },
                            { "data": "fechaEmision", "title": "Fecha de Emisión" },
                            //{ "data": "json_tributos", "title": "JSON Tributos" },
                            { "data": "pdf", "title": "PDF",
                                "render": function (data, type, row) {
                                    // Función para construir el enlace
                                    if(data == '-')
                                        return 'NO'
                                    else
                                        var urlPdf = "/PROD_files/" + data;
                                    return '<a target="_blank" href="' + urlPdf + '">PDF</a>';
                                }
                            },
                            { "data": "json", "title": "JSON",
                                "render": function (data, type, row) {
                                    // Función para construir el enlace
                                    var urlPdf = "/PROD_files/" + data;
                                    return '<a target="_blank" href="' + urlPdf + '">JSON</a>';
                                }
                            },
                            { "data": "numero_control", "title": "Número de Control" },
                            { "data": "telefono", "title": "Teléfono" },
                            { "data": "nombreComercial", "title": "Nombre Comercial" },
                            { "data": "codigoGeneracion", "title": "Código de Generación" },
                            { "data": "sello", "title": "Sello" }
                        ],
                        rowId: 'id',
                        liveAjax: false,
                        scrollX: true,
                        scrollY: true,
                        processing: false,
                });

                function actualizarTabla() {
                    $('#tablaDatos').DataTable().ajax.reload();
                }

                // Evento para recargar la tabla al hacer clic en el botón
                $('#btnRecargar').click(actualizarTabla);
                
            });


            
        </script>

        
    </body>
</html>
