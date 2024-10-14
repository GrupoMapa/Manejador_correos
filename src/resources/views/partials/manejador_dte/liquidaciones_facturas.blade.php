@if( $reporte != null )
<table id="facturas_recolectadas" class="display" style="width:100%">
    <thead>
        <tr >
            <th title="facturas con sello marcadas en blanco, sin sello marcadas en amarillo" >id</th>
            <th >Código gen</th>
            <th >Nombre comercial</th>
            <th >Total</th>
            <th >Acción</th>
            <th >Emision</th>
            <th >Nit</th>
            <th >Teléfono</th>
            <th >Pdf</th> 
            <th >Json</th>
            <th >Correo</th>
            <th >Fecha Correo</th>
            <th >Inactiva</th>
            <th >Descripcion</th>
            <th >Sello</th>
            <th >Reg diario</th>
            <!-- datos extra para cuando ya se ingresaron -->
            @if($extra_data['paso'] > 4)
                <th >int_sello</th>
                <th >int_codigo</th>
                <th >int_asiento</th>
            @endif
            <th >Impuestos</th>
            <th >Inactivar</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach ($reporte as $index=>$valor)
            <tr class="@if($valor->id_user_tesoreria != null) fase_tres @elseif($valor->id_user != null) fase_dos @endif @if($valor->inactiva != null) factura_inactiva @endif" >
                @foreach (get_object_vars($valor) as $campo => $contenido)
                    @if( ! in_array($campo, $no_render_columns))
                        <td style="background-color:  @if($campo === 'id' && $valor->sello == '-') #ffe2b7;  @else white; @endif " class="celda data_{{ $campo }}" @if ($campo == 'totalPagar' || $campo == 'fechaEmision') data-search='{{ $contenido }}' @endif>
                            @if ($campo === 'id_user' && ($extra_data['paso'] == 5 || $extra_data['paso'] == 6 || $extra_data['paso'] == 3 ) )
                                <div class="row">
                                    <div class="col-md-6">
                                        @if ( $extra_data['paso'] == 5 || $extra_data['paso'] == 6 )
                                                <input class="asig_num_doc_diario reg_diario @if ( $valor->num_registro_diario == '' || $valor->num_registro_diario == null ) no_guardado @endif " 
                                                    value='{{$valor->num_registro_diario}}' 
                                                    data-id="{{$valor->id}}"
                                                    type="number"
                                                    placeholder="# Reg. diario"
                                                    title="# Reg. diario"
                                                >
                                        @elseif ( $extra_data['paso'] == 3 and isset($extra_data['zona']) ) 
                                            @if($extra_data['zona'] == 0)
                                                <input class="asig_num_entrada  @if ( $valor->num_entrada_mercaderia == '' || $valor->num_entrada_mercaderia == null ) no_guardado @endif " 
                                                    value='{{$valor->num_entrada_mercaderia}}' 
                                                    data-id="{{$valor->id}}"
                                                    type='number'
                                                    placeholder="# entrada mercaderia"
                                                >
                                            @endif 
                                        @endif
                                    </div>
                                    <div class="col-md-6 larger-radio">
                                        @if ( $extra_data['paso'] == 3 and isset($extra_data['zona']) )
                                            @if($extra_data['zona'] == 0 )
                                            <input class="send_num_registro num-partida-link"
                                                type="radio" 
                                                name="marca_{{$valor->id}}" 
                                                data-id="{{$valor->id}}"
                                                data-codigo-generacion="{{ $valor->codigoGeneracion }}"
                                                title=" "
                                                style="float: right;"
                                                @if ( $valor->num_entrada_mercaderia != '' ) checked @endif
                                            >
                                            @else
                                            <input class="num-partida-link @if( $extra_data['paso'] == 5) send_num_partida @endif"
                                                type="radio" 
                                                name="marca_{{$valor->id}}" 
                                                data-id="{{$valor->id}}" 
                                                data-codigo-generacion="{{ $valor->codigoGeneracion }}"
                                                title=" @if( $extra_data['paso'] == 5 ) Marcar para guardar el número @else Marcar para validar @endif "
                                                style="float: right;"
                                                @if ( $valor->num_registro_diario != '' ) checked @endif
                                            >
                                            @endif
                                        @else
                                            <input class="num-partida-link @if( $extra_data['paso'] == 5 || $extra_data['paso'] == 6 ) send_num_partida @endif"
                                                type="radio" 
                                                name="marca_{{$valor->id}}" 
                                                data-id="{{$valor->id}}" 
                                                data-codigo-generacion="{{ $valor->codigoGeneracion }}"
                                                title=" @if( $extra_data['paso'] == 5 ) Marcar para guardar el número @else Marcar para validar @endif "
                                                style="float: right;"
                                                @if ( $valor->num_registro_diario != '' ) checked @endif
                                            >
                                        @endif
                                    </div>
                                </div>
                            @elseif ($campo == "codigoGeneracion")
                                <i class=" fa fa-copy copy-icon iconof iconof_stand_button" title="{{ $contenido }}"></i>
                                <a href="#">
                                {{ substr($contenido, 0, 15) . '...' }}
                                </a>
                            @elseif ($campo === 'id_user' && is_null($contenido) && $valor->inactiva == null )
                                <a href="#" class="adjuntar-link" 
                                    data-codigo-generacion="{{ $valor->codigoGeneracion }}" 
                                    data-id="{{ $valor->id }}">Enviar a liquidar</a>
                            @elseif ($campo === 'id_user' && is_null($contenido) && $valor->inactiva != null )
                                <p>Deshabilitada</p>
                            @elseif ($campo === 'id_user' && !is_null($contenido) && $valor->inactiva == null )
                                <a href="#" class="asignar-revision" data-codigo-generacion="{{ $valor->codigoGeneracion }}">Asignar</a>
                            @elseif ($campo === 'id')
                                <a target="_blank"
                                    href="https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen={{ $valor->codigoGeneracion }}&fechaEmi={{ $valor->fechaEmision }}">{{ $contenido }}</a>
                            @elseif ($campo === 'pdf')
                                <i  class=" fa fa-copy copy-icon iconof iconof_stand_button" title="copiar"></i>
                                @if ($valor->pdf_interno != null)
                                <a target="_blank" href="/files_dte_internos/{{ $valor->pdf_interno }}"><i  class=" fa fa-file-alt iconof_view_a iconof_stand_button local_pdf" title="ver pdf interno"></i></a>
                                @endif
                                <a target="_blank" title="ver documento externo" href="/files_dte/{{ $contenido }}">factura</a>
                            @elseif ($campo === "json")
                                <i  class=" fa fa-copy copy-icon iconof iconof_stand_button" title="copiar"></i>
                                @if ($valor->json_interno != null)
                                <a target="_blank" href="/files_dte_internos/{{ $valor->json_interno }}"><i  class=" fa fa-file-archive iconof_view_a iconof_stand_button local_json" title="ver json interno"></i></a>
                                @endif
                                <a target="_blank" title="ver documento externo" href="/files_dte/{{ $contenido }}">Json</a>
                            @elseif ($campo == "local_sello" || $campo == "local_codigo" || $campo == "local_asiento_diario")
                                <i class="copy-icon fa fa-copy copy-icon iconof iconof_stand_button"></i>
                                <i class="send_data_extra copy-icon fas fa-pencil-alt fa fa-save iconof iconof_stand_button"></i>
                                <textarea class="asig_data_extra form-control  @if ( $campo == '' || $campo == null ) no_guardado @endif "
                                    data-id="{{$valor->id}}"
                                    data-tipo= "{{$campo}}"
                                    placeholder="{{$campo}}"
                                >{{$contenido}}</textarea>
                            @elseif ($campo == "nombreComercial")
                                <i class="copy-icon fa fa-copy copy-icon iconof iconof_stand_button"></i>
                                <p>{{ $contenido }}</p>
                            @elseif ($campo == "totalPagar")
                                <i class="iconof_upload_json iconof_stand_button fa fa-upload copy-icon " data-tipo='JSON' data-id='{{ $valor->id }}' title="{{ $contenido }} SUBIR JSON"></i>
                                <i class="iconof_upload_pdf iconof_stand_button fa fa-upload copy-icon " data-tipo='PDF' data-id='{{ $valor->id }}' title="{{ $contenido }} SUBIR PDF"></i>
                                <i class="fa fa-copy copy-icon iconof iconof_stand_button" title="{{ $contenido }}"></i><br>
                                <!-- Icono del lápiz -->
                                <span class="truncated-text">{{ number_format($contenido, 2, '.', '.') }}</span>
                            @elseif (strlen($contenido) > 30)
                                <i class="fa fa-copy copy-icon iconof iconof_stand_button" title="{{ $contenido }}"></i>
                                <!-- Icono del lápiz -->
                                <span class="truncated-text">{{ substr($contenido, 0, 30) . '...' }}</span>
                            @else
                                {{ $contenido }}
                            @endif
                        </td>
                    @endif
                @endforeach
                <td class="specificatios " data-title ="tributos">
                    <button class="data_tributos" data-id='{{$valor->id}}'>Ver tributos</button>
                </td>
                <td>
                    @if ( $extra_data['paso'] == 2 )
                    <i class=" fas fa-trash-alt inactivar"  onclick="inactivar_documento(event)" data-id="{{ $valor->id }}" data-monto = "{{ $valor->totalPagar }}"></i>
                    @else
                        no disponible
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
<script>


</script>
