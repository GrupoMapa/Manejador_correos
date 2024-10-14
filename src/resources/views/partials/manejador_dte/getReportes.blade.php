@forelse ($reportes as $reporte)
    <tr>
        <td>{{ $reporte->titulo }}</td>
        <td>{{ $reporte->autor }}</td>
        <td>{{ $reporte->categoria }}</td>
        <td>{{ $reporte->created_at }}</td>
        <td>
            <div class="d-flex">
                <a  class="btn btn-secondary" target="_blank" href="https://drive.google.com/viewerng/viewer?embedded=true&url=https://almacenesbomba.com/summary2/src/storage/app/{{$reporte->url}}">
                    <i class="far fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">No hay registros</td>
    </tr>
@endforelse