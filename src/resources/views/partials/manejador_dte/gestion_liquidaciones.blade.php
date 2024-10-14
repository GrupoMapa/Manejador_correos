
@if ( $extra_data['paso'] == 2 )
  <button type="button" class="btn btn-primary btn_liquidar reclamar-link" data-value=4 >Iniciar liq. Caja chica</button>
  <button type="button" class="btn btn-secondary btn_liquidar reclamar-link" data-value=0 >Iniciar liquidación</button>

  <button class="btn btn-secondary" style="float: right;" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
    Crear o ediar?
  </button>

  <div class="collapse card text-white bg-dark mb-3" id="collapseExample" style="">
    <div class="card-body col-md-12 row">
          <div class="col-md-8">
              <p class="card-text">Cuando un correo llega sin archivo json o con un archivo dañado las información no puede ser extraida, tiene que crearse desde acá</p>
              <p class="paso_2 paso">
                  Ingresar solo facturas que tengan su código de generación registrado en hacienda. En caso contrario, llamar a la empresa para que envíe un codigo de generación válido
              </p>
            </div>
          <div class="col-md-4">
              <button id="openModalBtn" data-toggle="modal" data-target="#myModal" class="btn btn-success">Crear Factura</button>
          </div>
    </div>
  </div>

  <div class="modal fade" id="myModal">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Formulario de Facturación</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                  <form id="facturaForm" class="row">
                      <div class="col-md-12">
                        <label >Tributos:</label>
                        <button id="agregarTributo" type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Agregar Tributo</button>
                        <div class="tributos-container col-md-12 row">
                            <!-- Aquí se agregarán los campos dinámicos de tributos -->
                        </div>
                      </div>
                      <div class="col-md-6">
                          <label for="codigoGeneracion">Código de generación:</label>
                          <input type="text" id="codigoGeneracion" name="codigoGeneracion" class="form-control" placeholder="ojo revisar que el código este en hacienda" pattern="[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}" required>
                          
                          <label for="codigoGeneracion">Sello:</label>
                          <input type="text" id="sello" name="sello" class="form-control" placeholder="IMPORTANTE" required>
                          
                          <label for="nombreComercial">Nombre comercial:</label>
                          <input type="text" id="nombreComercial" name="nombreComercial" class="form-control" required>
                          
                          <label for="nit">NIT:</label>
                          <input type="text" id="nit" name="nit" pattern="[0-9]{14}" placeholder="xxxxxxxxxxxxxx" class="form-control" required>
                          
                          <label for="telefono">Teléfono:</label>
                          <input type="tel" id="telefono" name="telefono" class="form-control">
                          
                          <label for="totalPagar">Total a pagar:</label>
                          <input type="number" step="0.01" id="totalPagar" name="totalPagar" class="form-control positivo" required>
                      </div>
                      <div class="col-md-6">
                          <label for="pdf">Link al PDF:</label>
                          <input type="url" id="pdf" name="pdf" class="form-control" required>
                          
                          <label for="json">Link al JSON:</label>
                          <input type="url" id="json" name="json" class="form-control" required>
                          
                          <label for="fechaEmision">Fecha de emisión:</label>
                          <input type="date" id="fechaEmision" name="fechaEmision" class="form-control">
                          
                          <label for="direccion_origen">Correo:</label>
                          <input type="email" id="direccion_origen" name="direccion_origen" class="form-control">
                          
                          <label for="fecha_correo">Fecha del correo:</label>
                          <input type="date" id="fecha_correo" name="fecha_correo" class="form-control">

                          <label for="fecha_correo">Crear o editar?:</label>
                          <select name="tipo_creacion_dte">
                            <option value="1" selected>CREAR UN REGISTRO DESDE CERO</option>
                            <option value="2">EDITAR UN REGISTRO</option>
                          </select>
                          <input name="id_edit" type="NUMBER">
                      </div>
                      <div class="col-12">
                          <label for="descripcion">Descripción:</label>
                          <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary" id="enviarFormulario">Enviar</button>
              </div>
          </div>
      </div>
  </div>

    

  <div class="modal fade" id="reclamarModal" tabindex="-1" role="dialog" aria-labelledby="reclamarModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reclamarModalLabel">Nueva liquidación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reclamarForm">
                    <div class="form-group">
                        <label for="areaSelect">Área</label>
                        <select class="form-control" id="areaSelect" required>
                        <option value="" disabled selected>Seleccione un área</option>
                        <!-- Rellenar el select con las opciones de 'extraData.areas' -->
                        @foreach ($extra_data['areas'] as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descripcion_asign">Descripción(OPCIONAL)</label>
                        <textarea class="form-control" id="descripcion_asign" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar liquidación</button>
                </form>
            </div>
          </div>
      </div>
  </div>

  <!-- Modal para el formulario asignar factura -->
  <div class="modal fade" id="asignarModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="asignarModalLabel">Adjuntar a una liquidación <strong id="area_trabajo"></strong> </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <form id="asignarForm">
                  <div class="form-group">
                      <label for="normaSelect">Norma</label>
                      <select id="normaSelect">
                        <option value="" disabled selected>Seleccione una norma</option>
             
                        @foreach ($extra_data['normas'] as $norma)
                        <option value="{{ $norma->id }}">{{ $norma->nombre }}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="descripcion">Descripción de factura</label>
                      <textarea class="form-control" maxlength="355" id="descripcion" rows="3" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Adjuntar factura a la liquidación</button>
              </form>
          </div>
        </div>
    </div>
  </div>

  <!-- Modal para el formulario liquidación -->
  <div class="modal fade" id="inactivarModal" tabindex="-1" role="dialog" aria-labelledby="inactivarModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inactivarModalLabel">Estas seguro!?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="inactivarForm">
                    <div class="form-group">
                        <p for="descripcion">
                          Si inactivas esta factura nadie más podrá reclamarla, esta función es para facturas
                          repetidas o facturas que fueron corregidas, que ya no se procesarán</p>
                          <p>El valor de la factura es: </p><h3 id="valor_factura_inhabilitar"></h3>
                    </div>
                    <button type="submit" class="btn btn-primary deshabilitar_factura">inHabilitar</button>
                </form>
            </div>
          </div>
      </div>
  </div>
  <a type="button" href="#form_upload" class="btn btn-secondary btn_subir " data-value=0 >Subir un archivo</a>
@endif

<script>
    var deshabilitar_factura = 0

    var tributos = document.getElementsByClassName('codigo-tributo')
    var tributos_val = []
    async function validar_tributos() {
        const tributos_val = Array.from(tributos).map(item => item.value);
        const arrasingle = Array.from(new Set(tributos_val));
        const resultado = !arrasingle.length === tributos_val.length;
    
        if (resultado) {
            alertify.error('Tienes tributos repetidos, elimina los innecesarios');
        }
    
        return !resultado; // No se encontraron elementos repetidos
    }
    
    async function validarPositivos() {
        const inputsPositivos = document.querySelectorAll('.positivo');
        let bandera = true; // Cambiado a true
    
        inputsPositivos.forEach(input => {
            const valor = parseFloat(input.value);
            if (isNaN(valor) || valor <= 0 || valor > 10000) {
                input.value = ''; // Borra el valor incorrecto
                input.classList.add('error');
                alertify.warning('El valor debe ser positivo y no mayor a 10000.');
                bandera = false;
            } else {
                input.classList.remove('error');
                // Comprueba si el valor es mayor a 1000
                if (valor > 1000) {
                    alertify.warning('El valor es mayor a 1000.');
                }
            }
        });
    
        return bandera;
    }

    $(document).on('click', '.eliminar-item-btn', function() {
        // Encontrar el elemento tributo más cercano al botón pulsado
        var item = $(this).closest('.tributo');

        // Eliminar el elemento tributo completo
        if (item.length) {
            item.remove();
        }
    });
    

    document.addEventListener("DOMContentLoaded", function () {
        const buttons = document.getElementsByClassName("btn_liquidar");
        for (const button of buttons) {
        button.addEventListener("click", function () {
            const dataValue = this.getAttribute("data-value");
            const areaSelect = document.getElementById("areaSelect");
            if (dataValue === "4") {
            areaSelect.value = "4";
            }else if(dataValue === "0") {
            areaSelect.value = "0";
            }
        });
        }

        $("#openModalBtn").click(function() {
            $("#myModal").css("display", "block");
        });

        function generarJSON() {
            var tributos = [];
            $(".tributo").each(function() {
                var codigo = $(this).find(".codigo-tributo").val();
                var descripcion = $(this).find(".codigo-tributo option:selected").attr("data-descripcion");
                var valor = parseFloat($(this).find(".valor-tributo").val());
                
                // Verificar si los valores son válidos antes de agregar al JSON
                if (codigo && descripcion && !isNaN(valor)) {
                    tributos.push({ codigo: codigo, descripcion: descripcion, valor: valor });
                }
            });
            return JSON.stringify(tributos);
        }

        async  function sendForm_factura() {
            const formulario= document.getElementById('facturaForm')
            const tributos = document.getElementsByClassName('codigo-tributo')
            const tributos_validos = await validar_tributos([])
            const validarPositivos_res = await validarPositivos()

            if( formulario.reportValidity() && tributos_validos && validarPositivos_res){
                var formData = $("#facturaForm").serialize();
                formData += "&json_tributos=" + generarJSON();
                // Realizar la llamada AJAX
                $.ajax({
                    url: "/summary2/f0_enviar_factura",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if(response.status){
                            $("#myModal").modal("hide");
                            var facturahtm = document.getElementById('facturaForm')
                            facturahtm.reset()
                            alertify.success(response.message);
                        }else{
                            alertify.error(response.message);
                        }
                    },
                    error: function(error) {
                        console.error("Error en la llamada AJAX:", error);
                    }
                });
            }
        }

        // Enviar el formulario usando AJAX cuando se hace clic en el botón de enviar
        $("#enviarFormulario").click(function() {
            sendForm_factura()
        });


        /* MULTIPLES TRIBUTOS */
        var tributoCount = 0;
        var catalogo = [
            { codigo: "20", descripcion: "Impuesto al Valor Agregado 13% " }
            ,{ codigo: "C3", descripcion: "Impuesto al Valor Agregado (exportaciones) 0% " }
            ,{ codigo: "59", descripcion: "Turismo: por alojamiento (5%) " }
            ,{ codigo: "71", descripcion: "Turismo: salida del país por vía aérea $7.00 " }
            ,{ codigo: "D1", descripcion: "FOVIAL ($0.20 Ctvs. por galón) " }
            ,{ codigo: "C8", descripcion: "COTRANS ($0.10 Ctvs. por galón) " }
            ,{ codigo: "D5", descripcion: "Otras tasas casos especiales " }
            ,{ codigo: "D4", descripcion: "Otros impuestos casos especiales " }
            ,{ codigo: "A8", descripcion: "Impuesto Especial al Combustible (0%, 0.5%, 1%) " }
            ,{ codigo: "57", descripcion: "Impuesto industria de Cemento " }
            ,{ codigo: "90", descripcion: "Impuesto especial a la primera matrícula " }
            ,{ codigo: "D4", descripcion: "Otros impuestos casos especiales " }
            ,{ codigo: "D5", descripcion: "Otras tasas casos especiales " }
            ,{ codigo: "A6", descripcion: "Impuesto ad- valorem, armas de fuego, municiones explosivas y artículos similares" }
            ,{ codigo: "C5", descripcion: "Impuesto ad- valorem por diferencial de precios de bebidas alcohólicas (8%) " }
            ,{ codigo: "C6", descripcion: "Impuesto ad- valorem por diferencial de precios al tabaco cigarrillos (39%) " }
            ,{ codigo: "C7", descripcion: "Impuesto ad- valorem por diferencial de precios al tabaco cigarros " }
            ,{ codigo: "19", descripcion: "Fabricante de Bebidas Gaseosas, Isotónicas, Deportivas, Fortificantes, Energizante o Estimulante " }
            ,{ codigo: "28", descripcion: "Importador de Bebidas Gaseosas, Isotónicas, Deportivas, Fortificantes, Energizante o Estimulante " }
            ,{ codigo: "31", descripcion: "Detallistas o Expendedores de Bebidas Alcohólicas " }
            ,{ codigo: "32", descripcion: "Fabricante de Cerveza " }
            ,{ codigo: "33", descripcion: "Importador de Cerveza " }
            ,{ codigo: "34", descripcion: "Fabricante de Productos de Tabaco " }
            ,{ codigo: "35", descripcion: "Importador de Productos de Tabaco " }
            ,{ codigo: "36", descripcion: "Fabricante de Armas de Fuego, Municiones y Artículos Similares " }
            ,{ codigo: "37", descripcion: "Importador de Arma de Fuego, Munición y Artículos. Similares " }
            ,{ codigo: "38", descripcion: "Fabricante de Explosivos " }
            ,{ codigo: "39", descripcion: "Importador de Explosivos " }
            ,{ codigo: "42", descripcion: "Fabricante de Productos Pirotécnicos " }
            ,{ codigo: "43", descripcion: "Importador de Productos Pirotécnicos " }
            ,{ codigo: "44", descripcion: "Productor de Tabaco " }
            ,{ codigo: "50", descripcion: "Distribuidor de Bebidas Gaseosas, Isotónicas, Deportivas, Fortificantes, Energizante o Estimulante " }
            ,{ codigo: "51", descripcion: "Bebidas Alcohólicas " }
            ,{ codigo: "52", descripcion: "Cerveza " }
            ,{ codigo: "53", descripcion: "Productos del Tabaco " }
            ,{ codigo: "54", descripcion: "Bebidas Carbonatadas o Gaseosas Simples o Endulzadas " }
            ,{ codigo: "55", descripcion: "Otros Específicos " }
            ,{ codigo: "58", descripcion: "Alcohol " }
            ,{ codigo: "77", descripcion: "Importador de Jugos, Néctares, Bebidas con Jugo y Refrescos " }
            ,{ codigo: "78", descripcion: "Distribuidor de Jugos, Néctares, Bebidas con Jugo y Refrescos " }
            ,{ codigo: "79", descripcion: "Sobre Llamadas Telefónicas Provenientes del Ext. " }
            ,{ codigo: "85", descripcion: "Detallista de Jugos, Néctares, Bebidas con Jugo y Refrescos " }
            ,{ codigo: "86", descripcion: "Fabricante de Preparaciones Concentradas o en Polvo para la Elaboración de Bebidas " }
            ,{ codigo: "91", descripcion: "Fabricante de Jugos, Néctares, Bebidas con Jugo y Refrescos " }
            ,{ codigo: "92", descripcion: "Importador de Preparaciones Concentradas o en Polvo para la Elaboración de Bebidas " }
            ,{ codigo: "A1", descripcion: "Específicos y Ad-Valorem " }
            ,{ codigo: "A5", descripcion: "Bebidas Gaseosas, Isotónicas, Deportivas, Fortificantes, Energizantes o Estimulantes " }
            ,{ codigo: "A7", descripcion: "Alcohol Etílico " }
            ,{ codigo: "A9", descripcion: "Sacos Sintéticos" } 
        ];

        $("#agregarTributo").click(function() {
            tributoCount++;
            var tributoHtml = `
                <div class="tributo col-md-6">
                    <div class="form-group">
                        <i class="fas fa-minus eliminar-item-btn" aria-hidden="true" style="float: right;"> borrar</i>
                        <label for="codigoTributo${tributoCount}">Código:</label>
                        <select id="codigoTributo${tributoCount}" class="form-control codigo-tributo" required>
                            <option value="" >Selecciona un código</option>
                            ${catalogo.map(option => `<option value="${option.codigo}" data-descripcion="${option.descripcion}">${option.codigo} ${option.descripcion}</option>`).join("")}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="valorTributo${tributoCount}">Valor:</label>
                        <input type="number" id="valorTributo${tributoCount}" class="form-control valor-tributo positivo" step="0.01" required>
                    </div>
                </div>
            `;

            $(".tributos-container").append(tributoHtml);

            // Deshabilitar las opciones de código ya seleccionadas
            $(".codigo-tributo").each(function() {
                var selectedValue = $(this).val();
            });
        });
    });


    $(".deshabilitar_factura").on("click", function(event) {
        event.preventDefault();
        if( deshabilitar_factura != 0){
            $.ajax({
                url: "/summary2/f1_deshabilitar_factura",
                method: "POST", // o "GET" según corresponda
                data: {
                    id: deshabilitar_factura
                },
                success: function(response) {
                    if (response.status) {
                        alertify.success(response.message);
                    } else {
                        alertify.error('hubo un error al realizar la acción, intente recargar');
                    }
                    $('#inactivarModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    // Aquí puedes manejar el error si lo deseas
                    console.error("Error en la petición AJAX:", error);
                }
            });
        }else{
            alert('Contacte con ti para revisar')
        }
    });
</script>

