

@if( isset( $extra_data['liquidaciones'] ) )
<div id="tabla_liquidaciones">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Area</th>
                <th>Fecha Creación</th>
                <th>Estado</th>
                <th>Selecciona </th>
                <th>DTE Asignados</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($extra_data['liquidaciones'] as $liquidacion)
            <tr title="{{ $liquidacion->descripcion_general }}">
                    <td class="@if(  $liquidacion->marca_recibido ) marca @endif" >{{ $liquidacion->id }}</td>
                    <td >
                        <div class="col-md-12">
                            <div class="col-md-12">
                                {{ $liquidacion->area }}
                            </div>
                            @if(  $extra_data['paso'] > 5 )
                            <div class="col-md-12">
                                    <i class="iconof_marca_final iconof_stand_button fas fa-check-double" data-tipo="{{ $liquidacion->tipo }}" data-id='{{ $liquidacion->id }}' title=" MARCAR COMO RECIBIDO FINAL"></i>
                            </div>
                            @else
                            <div class="col-md-12">
                                
                                    <button 
                                        class="item_liquidacion_btn btn btn-warning @switch($extra_data['paso']) @case(2) send_liquidation @break @case(3)send_liquidation_p3 @break @case(4)asignar-revision @break @case(5)send_liquidation_p5 @break @default @endswitch" 
                                        data-id="{{ $liquidacion->id }}"
                                        data-tipo="{{ $liquidacion->tipo }}"
                                        @switch($extra_data['paso']) @case(3) disabled @break @case(5) disabled @break @default @endswitch
                                        >
                                        @if ( $extra_data['paso'] == 4 )
                                            Asignar
                                        @else
                                            Listo
                                        @endif
                                    </button>
                            
                            </div>
                            @endif
                        </div>
                    </td>
                    <td> 
                        <div>{{ $liquidacion->created_at }}</div>
                        <div>{{ $liquidacion->name }}</div>
                    </td>
                    <td>{{ $liquidacion->estado }}</td>
                    <td> 
                        <div class="larger-radio">
                            <input class="item_liquidacion" type="radio" data-tipo="{{ $liquidacion->tipo }}" name=@switch($extra_data['paso']) @case(2) 'id_liquidation' @break @case(3)'id_liquidation_p3' @break @case(4)'id_liquidation_p4' @break @case(5) 'id_liquidation_p3' @break @default 'id_liquidation_p3' @endswitch value="{{ $liquidacion->id }}"> 
                            <textarea rows="2" cols="40" disabled>{{ $liquidacion->descripcion_general }}</textarea>
  
                        </div>
                    </td>
                    <td>
                        <div class="mini_listas">
                            @foreach ($liquidacion->extra_data as $factura)
                                    <div class="row col-md-12 border-bottom">
                                        <div class="col-md-2">
                                            id:{{$factura->id}}
                                        </div> 
                                        <div class="col-md-10">
                                            cod:{{ substr($factura->codigoGeneracion, 0, 12) }}
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <strong>monto: {{ number_format($factura->totalPagar, 2) }}</strong>
                                        </div>

                                        <div class="col-md-8">
                                            correo:{{$factura->direccion_origen}}
                                        </div>
                                        
                                        <div class="col-md-4">
                                            norma: <strong>{{$factura->codigo}}</strong>
                                        </div>
                                        <div class="col-md-8">
                                            nombre:  <strong>{{$factura->nombre}}</strong>
                                        </div>

                                    </div>
                        
                            @endforeach
                        </div>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        //-----------------FUNCIONES PARA LA TABLA---------------------------------
         // Función para ocultar columnas y cambiar clases de botones
         function toggleColumnas(botonID, columnas) {
            table.columns(columnas).visible(!table.column(columnas[0]).visible());
            //console.log('ocultar')
            // Cambiar la clase del botón según la visibilidad de las columnas
            if (table.column(columnas[0]).visible()) {
                $("#" + botonID).removeClass("btn-secondary").addClass("btn-success");
            } else {
                $("#" + botonID).removeClass("btn-success").addClass("btn-secondary");
            }
        }

        const asignar_evento=()=>{
            console.log('asignar_evento')
            $('#d_proceso').on('click', function() {
                toggleColumnas('d_proceso', [13, 15, 16]);
            });
            $('#d_empresa').on('click', function() {
                toggleColumnas('d_empresa', [7, 10]);
            });
            $('#d_archivos').on('click', function() {
                toggleColumnas('d_archivos', [9, 11, 12]);
            });
            $('#d_inactiva').on('click', function() {
                toggleColumnas('d_inactiva', [17]);
            });

        }

        @if ($reporte && $reporte->first() != null)
            asignar_evento()
        @endif 
    
        

        function funct_tables(){
            table =
                $('#facturas_recolectadas').DataTable({
                    order: [[ 0, "asc" ]], // Ordenar por la primera columna
                    paging: true, // Paginación
                    select: true,
                    searching: true, // Barra de búsqueda
                    lengthMenu: [10, 25, 50, 100], // Opciones de cantidad de registros por página
                    dom: 'Bfrtip',
                    buttons: [
                        "colvis"
                    ],
                    language: {
                        "url": "/summary2/assets_facturacion/json/es-ES.json" // Traducción al español
                    },
                    columns: [
                        { "visible": true },//id 0
                        { "visible": true },//codigo gen 1
                        { "visible": true },//nombre comercial 2
                        { "visible": true },//total 3
                        { "visible": true },//acciones 4
                        { "visible": false },//emision 5            d_factura
                        { "visible": true },//nit      6           
                        { "visible": false },//telefono 7           d_empresa
                        { "visible": true },//pdf       8           
                        { "visible": false },//json      9          d_archivos
                        { "visible": false },//correo    10          d_empresa
                        { "visible": false },//fecha correo  11      d_archivos
                        { "visible": false },//inactivar     12     d_archivos
                        { "visible": false },//descripcion   13      d_procesp
                        { "visible": true },//sello          14      
                        { "visible": false },//reg diarios   15     d_procesp
                        @if($extra_data['paso'] > 4)
                            { "visible": true },//reg diarios   15     d_procesp
                            { "visible": true },//impuestos     16      d_procesp
                            { "visible": true },//inactivar     17     d_inactivar
                        @endif
                        { "visible": false },//impuestos     16      d_procesp
                        { "visible": false },//inactivar     17     d_inactivar
                        
                    ]
                }).on('draw.dt', function() {
                    console.log( reloads_facturas )
                    
                    // se ejecuta solo la primera vez o dara problemas por que se asignan multiples eventos

                    reloads_facturas == 0 ? asignar_evento() : false
                    reloads_facturas++
                  
                }).on('page.dt', function () {
                   
                }).on('init.dt', function() {
                   
                }).on('column-visibility.dt', function (e, settings, column, state) {
                    if (state === true && column ==16 ) {
                        iniciar_botones_pdf()
                    } else {
                     
                    }
                });
                // Agregar filtro por fecha
                $('#data_fechaEmision').on('change', function() {
                    var fecha = $(this).val();
                    table.column(5).search(fecha).draw();
                });
                // Agregar filtro por total a pagar
                $('#data_totalPagar').on('change', function() {
                    var total = $(this).val();
                    table.column(3).search(total).draw();
                });
        }
        const inactivar_documento=(event)=>{
            document.getElementById('valor_factura_inhabilitar').innerText = event.srcElement.dataset.monto
            deshabilitar_factura = event.srcElement.dataset.id
            $('#inactivarModal').modal('show');
        }
        
        //----------------------------------------------------------------------------
        function action_liquidations(){
            const send_liquidation = document.querySelectorAll('.send_liquidation');
            send_liquidation.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    const id = event.srcElement.dataset.id
                    const boton = event.srcElement

                    alertify.confirm('Enviar al siguiente paso', "Esta seguro, una vez enviada ya no podrá seguir asignando facturas a esta liquidación?.",
                        function(){
                            $.ajax({
                                url: '/summary2/f1_enviar',
                                method: 'POST', data: {
                                    id: id,
                                },
                                success: function (response) {
                                    if (response.status) {
                                        alertify.success(response.message);
                                        boton.innerText = 'Enviada!!!'
                                    } else {
                                        alertify.error(response.message);
                                    }
                                    
                                },
                                error: function (error) {
                                    // Manejar errores si es necesario
                                    console.error(error);
                                }
                            });
                        },
                        function(){
                            alertify.error('Cancel');
                        }
                    );
                });
            });

            @if ( $extra_data['paso'] == 3 ||  $extra_data['paso'] == 5 )
                $('.item_liquidacion').on('input', function() {
                    // Encuentra todos los botones con la clase 'item_liquidacion_btn' y deshabilítalos
                    $('.item_liquidacion_btn').prop('disabled', true);
                    // Encuentra el botón correspondiente dentro del mismo tr y habilítalo
                    var $boton = $(this).closest('tr').find('.item_liquidacion_btn');
                    if ($(this).is(':checked')) {
                        $boton.prop('disabled', false);
                    }
                });
            @endif

        }
        action_liquidations()
    </script>
</div>   
@endif

