@extends('layouts.app')

@section('content')
      <div class="col-md-12">
        <h1>Informe de trabajo de DTES</h1>
        <div class="row ">
            <div class="col-md-4 card">
                - Esta lista muestra los DTES que se estan trabajando, no incluye los DTEs que aun no han sido reclamados
            </div>
            <div class="col-md-4 card">
                - El estado final de los dtes es el estado 6 
            </div>
            <div class="col-md-4 card">
                - Para buscar precione CTRL + F y busque el codigo, monto, o nombre comercial de la empresa
            </div>
            <div class="col-md-4 card">
                - Por motivos de rendimiento se mostraran los últimos 2000 registros
            </div>
            <div class="col-md-4 card">
                - Los usuarios que colaboraron en el proceso se muestran en la columna: usuarios_proceso se muestra su id de usuario
            </div>
            
        </div>
        <br>
        <table id="tabla" class="table table-striped table-bordered">
            <thead>
                <tr>
                    @foreach ($reporte as $index=>$valor)
                        @foreach (get_object_vars($valor) as $campo => $contenido)
                            @if( ! in_array($campo, $no_render_columns))
                                <th>{{ $campo }}</th>
                            @endif
                        @endforeach
                        @break
                    @endforeach
                </tr>
            </thead>
    
          <tbody>
            @foreach ($reporte as $index=>$valor)
            <tr>
                @foreach (get_object_vars($valor) as $campo => $contenido)
                        @if( ! in_array($campo, $no_render_columns))
                            <td>
                            @if( $campo == 'pdf' )
                                <a target="_blank" title="ver documento externo" href="/files_dte/{{ $contenido }}">PDF</a>
                            @elseif( $campo == 'pdf_interno' )
                                <a target="_blank" title="ver documento externo" href="/files_dte_internos/{{ $contenido }}">PDF interno</a>
                            @else
                                {{ $contenido }}
                            @endif
                            </td>
                        @endif
                @endforeach
            </tr>
            @endforeach

          </tbody>
        </table>
      </div>
@endsection