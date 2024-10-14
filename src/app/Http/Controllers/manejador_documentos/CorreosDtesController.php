<?php

namespace App\Http\Controllers\manejador_documentos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Fac_liquidacion;
use App\Models\Factura_electronica;
use App\Models\Fac_sujeto_excluido;
use App\Models\Fac_liquidacion_factura;
use App\Models\Fac_montos_tributo_factura;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class CorreosDtesController extends Controller
{
    public $con_bomba_summary=null;

    public function __construct() {
        $connection = 'my_summay_almacenesbomba'; 
        $this->con_bomba_summary=  DB::connection($connection);
        /*if (DB::connection($connection)->getPdo()->getAttribute(\PDO::ATTR_CONNECTION_STATUS)) {
        } else {
            echo ' Coneccion fallida con la base de datos de summary';
            die;
        }*/

    }

    public function facturas_electronicas_f2(request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p1', 'all','f_p'], $permisos)) ) {
            return 'sin acceso';
        }

        $fechaLimite = Carbon::now()->subDays(20)->toDateString(); //90 días
        $idUsuarioLogueado = auth()->id();
        $reporte = DB::table('factura_electronicas')
                        ->select(
                            'factura_electronicas.id',  'codigoGeneracion',        'nombreComercial',       'totalPagar',
                            'id_user',                  'fechaEmision',             'nit',                  'telefono',
                            'pdf',                      'json',                     'direccion_origen',     'fecha_correo', 
                            'id_user_tesoreria',        'json_tributos', 'inactiva','descripcion',          'factura_electronicas.sello', 'factura_electronicas.num_registro_diario',
                            'json_interno', 'pdf_interno'
                            )
                        ->where('created_at','>=',$fechaLimite)
                        ->whereNull('id_user')
                            ->get();
        $normas =   Cache::has('normas') ? Cache::get('normas'): DB::table('fac_norma_repartos')->get();
            if(!Cache::has('normas')) cache(['normas' => $normas], 10);  

        $areas =    Cache::has('areas') ? Cache::get('areas') : DB::table('fac_areas')->get();
            if(!Cache::has('areas')) cache(['areas' => $areas], 10);
        

        $liquidaciones_proceso = $this->liquidaciones_user($idUsuarioLogueado);            
        $titulo = "Fase 2 Clasificación";            
        $instrucciones = [
                '<strong>Paso 1:</strong> Inicia una liquidación para poder asignar facturas, si ya tienes una para el área que necesitas, ve al paso 2 <strong>.<br> Si acá no aparece tu factura consulta con el encargado de la fase 1 del correo, el debe buscar un correo con tu factura, esto sucede cuando las empresas no envían el json con la información</strong>',
                '<strong>Paso 2:</strong> Selecciona tu área o caja chica para reclamar tus facturas, ten cuidado de asignar correctamente cada factura',
                '<strong>Paso 3:</strong> Busca tu factura, da click en <strong>enviar a liquidar</strong> , esto asignará la factura al área que has seleccionado, <strong>Facturas de más de 90 días se ocultan por motivos de rendimiento</strong>'
        ];

        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno', 'estado'];
        $extra_data = Array( 'normas'=> $normas, 'areas'=> $areas, 'users'=> null, 'liquidaciones'=> $liquidaciones_proceso, 'paso'=> 2 );
        
        return view ('manejador_dte.datos_correo',compact('reporte', 'extra_data', 'no_render_columns', 'instrucciones', 'titulo'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else{
            $permisos_conta= Array('f_p2','f_p3','f_p4','f_p5','f_p6','f_p');
            $intersec = array_intersect($permisos, $permisos_conta);
            if( !empty($intersec) ){
                return 'sin acceso';
            }
        }
        $nombreUsuario = Auth::user()->name;
        $rol = session('rol');
        if($rol != 0  && $rol != 1){
            return 'sin acceso';
        }
        return 'sin acceso';
    }


    public function facturas_electronicas_f3(request $request)
    {

        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p2', 'all'], $permisos)) ) {
            return 'sin acceso';
        }

        if( $request->sub_estado == 'tesoreria' ){
            $zona = 1;
        }else if( $request->sub_estado == 'proveduria' ){
            $zona = 0;
        }else{
            echo 'Debes enviar un parametro extra'; die;
        }

        //ASIGNACIONES DE TRABAJO
        $expiredAt = Carbon::now()->subMinutes(43200); // 30 DIAS 
        $reporte = null;
        $normas =   [];//DB::table('fac_norma_repartos')->get();
        $areas  =   [];//DB::table('fac_areas')->get();
        $users = DB::table('users')->select('id', 'name', 'email')->get();

        $estado = 2;
        $area = null;
        $no_area = null;
        $asignacion = false;
        /* 
            Para el caso de tesoreria queremos que se muestren las liquidaciones en estado #2 que sean de caja chica
            Para el caso de proveduria se muestran las liquidaciones en estados dos que no sean de caja chica
        */
        if($zona == 1){
            $area = 4;
            $asignacion=false;
            $no_area = null;
        }else if($zona == 0){
            $area = null;
            $asignacion=false;
            $no_area = 4;
        }
        
        $liquidaciones_proceso = $this->liquidaciones_user(0, $estado, $area, $asignacion, $no_area); //$idUsuarioLogueado, $estado=1, $area = null, $asignacion=false, $no_area=null   
        $extra_data = Array( 'normas'=> $normas, 'areas'=> $areas, 'users'=> $users, 'liquidaciones'=> $liquidaciones_proceso, 'paso'=> 3, 'zona'=> $zona);
        $titulo = "Fase 3 Revisión";
        $instrucciones = [
            '1- Identifica la liquidación en la tabla. <br>
             2- verifica los datos que te corresponden y da click en listo <br>',
            'Nota: Puedes seleccionar el circulo de una liquidación para visualizar los datos de las facturas asociadas a esta',
            '3- Marcar los dte que ya fueron revisados y posterior mente dar click en Listo para enviar al siguiente paso'
        ];
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno'];
        return view ('manejador_dte.datos_correo',compact('reporte', 'extra_data', 'titulo', 'instrucciones', 'no_render_columns', 'estado'));
    }

    public function facturas_electronicas_f4(request $request)
    {
        $permisos= session('permisos');
       
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p3', 'all'], $permisos)) ) {
            return 'sin acceso';
        }
        //ASIGNACIONES DE TRABAJO
        $expiredAt = Carbon::now()->subMinutes(43200); // 30 DIAS 
        $reporte = null;
        $normas =   Cache::has('normas') ? Cache::get('normas'): DB::table('fac_norma_repartos')->get();
                        if(!Cache::has('normas')) cache(['normas' => $normas], 10); 
        $areas  =   [];
        $users =    DB::table('users')->select('id', 'name', 'email')
                        ->leftJoin('model_has_roles', 'model_has_roles.model_id','users.id')
                        ->wherein('model_has_roles.role_id' , [1,2])
                        ->get();

        $estado = null;
        $area = null;
        $liquidaciones_proceso      = $this->liquidaciones_user(-1, $estado, $area);
        $sujetos_excluidos_proceso  = $this->sujetos_excluidos_user(-1, 3, $area);
       
        $liquidaciones_proceso= $liquidaciones_proceso->merge($sujetos_excluidos_proceso);

        $extra_data = Array( 'normas'=> $normas, 'areas'=> $areas, 'users'=> $users, 'liquidaciones'=> $liquidaciones_proceso, 'paso'=> 4 );
        $titulo = "Fase 4 Asignación";
        $instrucciones = [
            '1- IMPORTANTE EXISTE UN PASO DE PREAPROBACION, LAS LINEAS AMARILLAS ESTAN POR SER APROBADAS,
                Identifica la liquidación en la tabla. <br>
             2- Asigna al empleado que la procesará dando click en el botón <br>',
            'Nota: Puedes seleccionar el circulo de una liquidación para visualizar los datos de las facturas asociadas a esta',
            'Facturas asociadas'
        ];
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno'];
        return view ('manejador_dte.datos_correo',compact('reporte', 'extra_data', 'titulo', 'instrucciones', 'no_render_columns', 'estado'));

    }
    
    public function facturas_electronicas_f5(request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p4', 'all'], $permisos)) ) {
            return 'sin acceso';
        }
        //ASIGNACIONES DE TRABAJO
        $expiredAt  =   Carbon::now()->subMinutes(43200); // 30 DIAS 
        $reporte    =   null;
        $normas     =   [];
        $areas      =   [];
        $users      =   DB::table('users')->select('id', 'name', 'email')
                            ->leftJoin('model_has_roles', 'model_has_roles.model_id','users.id')
                            ->wherein('model_has_roles.role_id' , [1,2])
                            ->get();

        $idUsuarioLogueado      = auth()->id();
        $estado                 = 4;
        $area                   = null;
        //var_dump($idUsuarioLogueado, $estado, $area);
        $liquidaciones_proceso  = $this->liquidaciones_user($idUsuarioLogueado, $estado, $area, true);
        $sujetos_excluidos_proceso  = $this->sujetos_excluidos_user($idUsuarioLogueado, $estado, $area, true);
       
       
        $liquidaciones_proceso= $liquidaciones_proceso->merge($sujetos_excluidos_proceso);

        $extra_data             = Array( 'normas'=> $normas, 'areas'=> $areas, 'users'=> $users, 'liquidaciones'=> $liquidaciones_proceso, 'paso'=> 5 );
        $titulo                 = "Fase 5 Registro";
        $instrucciones = [
            '1- Identifica la liquidación en la tabla. <br>
             2- Selecciona la liquidación que trabajarás, ve abajo y completa los datos del número de reg del libro diario <br>',
            'Nota: Puedes seleccionar el circulo de una liquidación para visualizar los datos de las facturas asociadas a esta',
            '3- Debes asignar un número de documento diario a cada documento, <strong>Para guardar debes dar click en el circulo al lado de cada campo</strong><br> el icono de disket es para guardar y el de paginitas es para copiar'
        ];
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno', 'estado'];
        return view ('manejador_dte.datos_correo',compact('reporte', 'extra_data', 'titulo', 'instrucciones', 'no_render_columns'));

    }

    public function facturas_electronicas_f6(request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p5', 'all', 'f_p'], $permisos)) ) {
            return 'sin acceso';
        }

        //ASIGNACIONES DE TRABAJO
        $expiredAt          = Carbon::now()->subMinutes(43200); // 30 DIAS 
        $reporte            = null;
        $idUsuarioLogueado  = auth()->id();
        $normas             =   [];//DB::table('fac_norma_repartos')->get();
        $areas              =   [];//DB::table('fac_areas')->get();
        $users              =   [];//DB::table('users')->select('id', 'name', 'email')->get();

        $estado = 5;
        $area = null;
        $liquidaciones_proceso = $this->liquidaciones_user(0, $estado, $area);
        $sujetos_excluidos_proceso  = $this->sujetos_excluidos_user(-1, 3, $area);
        $liquidaciones_proceso= $liquidaciones_proceso->merge($sujetos_excluidos_proceso);

        $extra_data = Array( 'normas'=> $normas, 'areas'=> $areas, 'users'=> $users, 'liquidaciones'=> $liquidaciones_proceso, 'paso'=> 6 );
        $titulo = "Fase 6 Lista de liquidaciones finalizadas";
        $instrucciones = [
            '1- Identifica la liquidación en la tabla. <br>
             2- verifica los datos que te corresponden y da click en listo <br>',
            'Nota: Puedes seleccionar el circulo de una liquidación para visualizar los datos de las facturas asociadas a esta',
            'Facturas asociadas'
        ];
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno', 'estado'];
        return view ('manejador_dte.datos_correo',compact('reporte', 'extra_data', 'titulo', 'instrucciones', 'no_render_columns'));

    }

    /*public function facturas_electronicas_general(request $request)
    {
        $permisos= session('permisos');
      
        if(!in_array('f_p0', $permisos) && !in_array('f_p', $permisos)) {
            return 'sin acceso';
        }
        $liquidacionesGeneral = Fac_liquidacion::all();
        
        return view('liquidacionesDataTable',compact('liquidacionesGeneral'));
    }*/

    public function facturas_electronicas_usuario(request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos)) ) {
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        if (Auth::check()) {
            $userId = Auth::user()->id;
            $liquidacionesGeneral = Fac_liquidacion::where('id_usuario',$userId)->get();
            return view('liquidacionesDataTable',compact('liquidacionesGeneral'));
        } else {
            return "Ningún usuario autenticado";
        }        
    }

    public function liquidaciones_user($idUsuarioLogueado, $estado=1, $area = null, $asignacion=false, $no_area=null){
        //dd($idUsuarioLogueado, $estado,  $area , $asignacion, $no_area);
        if( $idUsuarioLogueado == -1 ){
            $liquidaciones_proceso  =   
                DB::table('fac_liquidacions')
                    ->select('fac_liquidacions.created_at','estado','descripcion_general','fac_areas.nombre as area','fac_liquidacions.id', 'users.name', 'marca_recibido')
                    ->addSelect(\DB::raw("'dte_norm' as tipo"))
                    ->leftJoin('users', 'users.id','fac_liquidacions.id_usuario')
                    ->leftJoin('fac_areas', 'fac_areas.id','fac_liquidacions.id_area')
                    ->where('estado', 3);
        }else if( $idUsuarioLogueado == 0 ){
            $liquidaciones_proceso  =   
                DB::table('fac_liquidacions')
                    ->select('fac_liquidacions.created_at','estado','descripcion_general','fac_areas.nombre as area','fac_liquidacions.id', 'users.name', 'marca_recibido')
                    ->addSelect(\DB::raw("'dte_norm' as tipo"))
                    ->leftJoin('users', 'users.id','fac_liquidacions.id_usuario')
                    ->leftJoin('fac_areas', 'fac_areas.id','fac_liquidacions.id_area');
        }else if( $idUsuarioLogueado > 0 &&  $asignacion){
            $liquidaciones_proceso  =   
                DB::table('fac_liquidacions')
                    ->select('fac_liquidacions.created_at','estado','descripcion_general','fac_areas.nombre as area','fac_liquidacions.id', 'users.name', 'marca_recibido')
                    ->addSelect(\DB::raw("'dte_norm' as tipo"))
                    ->leftJoin('users', 'users.id','fac_liquidacions.id_usuario')
                    ->leftJoin('fac_areas', 'fac_areas.id','fac_liquidacions.id_area' )
                    ->where('id_user_asignado', $idUsuarioLogueado);
        }else{
            $liquidaciones_proceso  =   
                DB::table('fac_liquidacions')
                    ->select('fac_liquidacions.created_at','estado','descripcion_general','fac_areas.nombre as area','fac_liquidacions.id', 'users.name', 'marca_recibido')
                    ->addSelect(\DB::raw("'dte_norm' as tipo"))
                    ->leftJoin('users', 'users.id','fac_liquidacions.id_usuario')
                    ->leftJoin('fac_areas', 'fac_areas.id','fac_liquidacions.id_area' )
                    ->where('id_usuario', $idUsuarioLogueado);
        }

        if($estado != null){
            $liquidaciones_proceso->where('estado', $estado);
        }

        if($area != null){
            $liquidaciones_proceso->where('id_area', $area);
        }
        if($no_area != null){
            $liquidaciones_proceso->where('fac_liquidacions.id_area','!=',$no_area);
        }

        $liquidaciones_proceso = $liquidaciones_proceso->orderBy('created_at', 'DESC')->get();
        //dd($liquidaciones_proceso, $no_area); die; 
        /* 
            aca es posible optimizar pero no creo que agreguen demaciados items a las liquidaciones
        */
        foreach ($liquidaciones_proceso as $key => $value) {
            if( $idUsuarioLogueado == 0 || $idUsuarioLogueado == -1 ){
                $data = DB::table('fac_liquidacion_facturas')
                ->select(   'factura_electronicas.id',                  'factura_electronicas.codigoGeneracion',    'factura_electronicas.totalPagar', 
                            'factura_electronicas.direccion_origen',    'fac_norma_repartos.codigo',                'fac_norma_repartos.nombre')
                ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                ->leftJoin('fac_norma_repartos', 'fac_norma_repartos.id', '=', 'factura_electronicas.id_norma')
                ->where('fac_liquidacion_facturas.id_liquidacion', $value->id)
                ->orderBy('fac_liquidacion_facturas.created_at', 'DESC')
                ->get();
            }else{
                $data = DB::table('fac_liquidacion_facturas')
                ->select(   'factura_electronicas.id', 'factura_electronicas.codigoGeneracion', 'factura_electronicas.totalPagar', 
                            'factura_electronicas.direccion_origen', 'fac_norma_repartos.codigo','fac_norma_repartos.nombre'
                )
                ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                ->leftJoin('fac_norma_repartos', 'fac_norma_repartos.id', '=', 'factura_electronicas.id_norma')
                ->where('factura_electronicas.id_user', $idUsuarioLogueado)
                ->where('fac_liquidacion_facturas.id_liquidacion', $value->id)
                ->orderBy('fac_liquidacion_facturas.created_at', 'DESC')
                ->get();
            }
            $liquidaciones_proceso[$key]->extra_data = $data;    
        }
        return $liquidaciones_proceso;
    }

    public function sujetos_excluidos_user($idUsuarioLogueado, $estado=1, $area = null, $asignacion=false, $no_area=null){
        try {
            $sujetos_excluidos  =   
            DB::table('fac_sujeto_excluido')
                ->select('fac_sujeto_excluido.created_at','fac_sujeto_excluido.estado',
                        \DB::raw("CONCAT('sujeto excluido: ', fac_sujeto_excluido.id) as descripcion_general"), 
                        "fac_sujeto_excluido.id as area",'fac_sujeto_excluido.id', 'fac_sujeto_excluido.id as name', 'fac_sujeto_excluido.marca_recibido')
                ->addSelect(\DB::raw("'exclu' as tipo"))
                ->leftJoin('users', 'users.id','fac_sujeto_excluido.id_usuario')
                ->where('fac_sujeto_excluido.id_usuario', $idUsuarioLogueado)
                ->where('estado', $estado);

            $sujetos_excluidos = $sujetos_excluidos->orderBy('fac_sujeto_excluido.created_at', 'DESC')->get();
            //dd($sujetos_excluidos, $no_area); die; 
            /* 
                aca es posible optimizar pero no creo que agreguen demaciados items a las liquidaciones
            */
            foreach ($sujetos_excluidos as $key => $value) {
                $data = DB::table('fac_sujeto_excluido')
                    ->select('factura_electronicas.id',                  'factura_electronicas.codigoGeneracion',    'factura_electronicas.totalPagar', 
                                'factura_electronicas.direccion_origen',    'fac_norma_repartos.codigo',                'fac_norma_repartos.nombre')
                    ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_sujeto_excluido.id_factura_electronica')
                    ->leftJoin('fac_norma_repartos', 'fac_norma_repartos.id', '=', 'fac_sujeto_excluido.id_norma_reparto_sap')
                    ->where('fac_sujeto_excluido.id', $value->id)
                    ->orderBy('factura_electronicas.created_at', 'DESC')
                    ->get();
                
                $sujetos_excluidos[$key]->extra_data = $data;    
            }
        } catch (Exception $e) {
            // Handle the exception
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
        
        return $sujetos_excluidos;
    }

    public function f1_iniciar_liquidacion(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all', 'f_p1'], $permisos)) ) {
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        //$codigoGeneracion = $request->input('codigoGeneracion');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();
        $areaId             = $request->input('areaId');
        //$normaId            = $request->input('normaId');
        $descripcion        = $request->input('descripcion');

        $activas = 
            Fac_liquidacion::
                where('id_usuario', $idUsuarioLogueado)
                ->where('id_area', $areaId)
                ->where('estado', 1)
                ->pluck('id') // Obtener solo el campo 'id'
                ->toArray(); // Convertir la colección a un array de IDs
        if(count($activas)>0){
            $response = [
                'message' => 'Ya tienes una liquidación iniciada con esta área, finalizala antes de crear una nueva',
                "status"=> false
            ];
        }else{
            $liquidacion = new Fac_liquidacion;
            $liquidacion->id_usuario = $idUsuarioLogueado;
            $liquidacion->id_area = $areaId;
            $liquidacion->estado = 1;
            $liquidacion->descripcion_general = $descripcion;
            $liquidacion->save();
            $response = [ 'message' => 'listo!', "status"=> true ];
        }
        return response()->json($response);
    }

    public function asignar_empleado(Request $request){
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p3', 'all'], $permisos)) ) {
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }

        $id = $request->input('id');
        $id_empleado_asignado = $request->input('id_empleado_asignado');
        $idUsuarioLogueado = auth()->id();
        $fechaActual = Carbon::now();
     
        if($request->input('tipo_documento') =='dte_norm' ){
            // Validar si algún registro ya tiene asignado un usuario
            $facturasConUsuarioLiquida = Fac_liquidacion::where('id', $id)->where('id_user_asignado', $id_empleado_asignado)
            //id_user_asignado
            //->whereNotNull('id_user_tesoreria')
            ->exists();
       
            if ($facturasConUsuarioLiquida != null) {
                return response()->json(['message' => 'Ya fue asignado, te recomendamos recargar la página',"status"=> false]);
            }
            // Actualizar los registros en la base de datos
            Fac_liquidacion::
                where('id', $id)
                ->where('estado', 3)
                ->update([
                    'id_user_asignado' => $id_empleado_asignado,
                    'id_usuario' => $idUsuarioLogueado,
                    'fecha_asignacion' => $fechaActual,
                    'estado' => 4
                ]);
        }else{
            // Validar si algún registro ya tiene asignado un usuario
            $facturasConUsuarioLiquida = Fac_sujeto_excluido::where('id', $id)->where('id_user_asignado', $id_empleado_asignado)->where('estado', 3)
            //id_user_asignado
            //->whereNotNull('id_user_tesoreria')
            ->exists();
            if ($facturasConUsuarioLiquida != null) {
                return response()->json(['message' => 'Ya fue asignado, te recomendamos recargar la página',"status"=> false]);
            }
            // Actualizar los registros en la base de datos
            Fac_sujeto_excluido::
                where('id', $id)
                ->where('estado', 3)
                ->update([
                    'id_user_asignado' => $id_empleado_asignado,
                    'id_usuario' => $idUsuarioLogueado,
                    'fecha_asignacion' => $fechaActual,
                    'estado' => 4
                ]);
        }
       
        return response()->json([
            'message' => 'Facturas actualizadas correctamente',
            'idsActualizados' => [],
            "status"=> true
        ]);
    }

    public function rev_tesoreria(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p2', 'all'], $permisos)) ) {
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $codigoGeneracion = $request->input('cod_gen');
        $code_sap = $request->input('code_sap');
        $idUsuarioLogueado = auth()->id();
        $fechaActual = Carbon::now();

        $facturasConUsuarioLiquida = Factura_electronica::
            where('codigoGeneracion', $codigoGeneracion)
            ->whereNull('id_user_tesoreria')
            ->exists();

        if ($facturasConUsuarioLiquida==null) {
            return response()->json(['message' => 'Ya fue asignado, te recomendamos recargar la página',"status"=> false]);
        }
        // Actualizar los registros en la base de datos
        Factura_electronica::
            where('codigoGeneracion', $codigoGeneracion)
            ->whereNull('id_user_tesoreria')
            ->update([
                'id_user_tesoreria' => $idUsuarioLogueado,
                'codigo_sap' => $code_sap
            ]);

        $idsActualizados = Factura_electronica::
            where('codigoGeneracion', $codigoGeneracion)
            ->whereNotNull('id_user_tesoreria')
            ->pluck('id') // Obtener solo el campo 'id'
            ->toArray(); // Convertir la colección a un array de IDs

        return response()->json([
            'message' => 'Actualizado correctamente',
            'idsActualizados' => $idsActualizados,
            "status"=> true
        ]);
    }

    public function valid_code_gen(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos)) ) {
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $codigoGeneracion = $request->input('codigo_generacion');
        $id = $request->input('id');
        $facturas_codigo = Factura_electronica::
            where('codigoGeneracion', $codigoGeneracion)
            ->whereNull('inactiva')
                ->pluck('id') // Obtener solo el campo 'id'
                    ->toArray();

        if (count($facturas_codigo)>1) {
            return response()->json(['message' => 'Tenemos esta factura repetida, quizá la empresa realizó una corrección, 
                                                    debes de inactivar las facturas extra y solo dejar una, las repetidas son: '. implode(',', $facturas_codigo) ,"status"=> false, 'facturas'=> $facturas_codigo]);
        }else{
            return response()->json(['message' => '',"status"=> true]);
        }
    }


    public function f1_deshabilitar_factura(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p1', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id = $request->input('id');
        $date = Carbon::now(); //cada 2 horas 
        $idUsuarioLogueado = auth()->id();

        Factura_electronica::
        where('id', $id)
        ->update([
            'inactiva' => 1,
            'fecha_inactiva' => $date,
            'id_user_inactiva'=> $idUsuarioLogueado
        ]);
        
        return response()->json(['message' => 'Deshabilitada con éxito',"status"=> true]);
    }

    public function f1_asignar_factura(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p3', 'all'], $permisos))){
            return 'sin acceso';
        }
        $codigoGeneracion = $request->input('codigoGeneracion');
        $id_factura = $request->input('id_factura');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();
        //$areaId             = $request->input('areaId');
        $normaId            = $request->input('normaId');
        $descripcion        = $request->input('descripcion');
        $id_liquidacion     = $request->input('value_liquidation');
        
        $liquidacion_valida = 
                Fac_liquidacion::
                        where('fac_liquidacions.id', $id_liquidacion)
                        ->where('fac_liquidacions.estado', 1)
                        ->pluck('fac_liquidacions.id')
                        ->toArray(); // Convertir la colección a un array de ID
        //dd(count($liquidacion_valida));
        if(count($liquidacion_valida) == 0){
            $response = [
                'message' => 'Recarga la página, ya no es posible asignar facturas a esta liquidación ha pasado al siguiente paso',
                "status"=> false
            ];
            return response()->json($response);
        }

        $activas = 
                Factura_electronica::
                        leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_factura', 'factura_electronicas.id')
                        ->leftJoin('fac_liquidacions', 'fac_liquidacions.id', 'fac_liquidacion_facturas.id_liquidacion')
                        //->where('id_usuario', $idUsuarioLogueado)
                        ->where('factura_electronicas.id', $id_factura)
                        ->where('fac_liquidacions.estado', null)
                        ->whereNull('fac_liquidacion_facturas.id_factura')
                        ->where('codigoGeneracion', $codigoGeneracion)
                        ->whereNull('inactiva') //no incluir deshabilitadas
                        ->pluck('factura_electronicas.id') // Obtener solo el campo 'id'
                        ->toArray(); // Convertir la colección a un array de IDs
        if(count($activas)>1){
            $response = [
                'message' => 'Recarga la página, existe una segunda factura activa con este mismo código de generación, debes deshabilitar las incorrectas',
                "status"=> false
            ];
        }else if(count($activas)==0){
            $response = [
                'message' => 'No encontramos la factura, es posible que esté inactivada, recarga la página',
                "status"=> false
            ];
        }else{
            $id_factura = $activas[0]; 
            $liquidacion_factura = new Fac_liquidacion_factura;
            $liquidacion_factura->id_liquidacion = $id_liquidacion;
            $liquidacion_factura->id_factura = $id_factura;
            $liquidacion_factura->updated_at = $fechaActual;
            $liquidacion_factura->created_at = $fechaActual;
            $liquidacion_factura->save();

            Factura_electronica::
                where('id', $id_factura)
                ->whereNull('inactiva')
                ->update([
                    'id_user' => $idUsuarioLogueado,
                    'updated_at' => $fechaActual,
                    'fechaAsignacion' => $fechaActual,
                    'descripcion'=> $descripcion,
                    'id_norma' => $normaId
                ]);
            $response = [
                'message' => 'listo!',
                "status"=> true
            ];
        }
        return response()->json($response);
    }

    public function f1_reload_liquidations(){//liquidaciones_user
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $idUsuarioLogueado = auth()->id();
        $liquidaciones_proceso = $this->liquidaciones_user($idUsuarioLogueado);
        $extra_data = Array('liquidaciones'=> $liquidaciones_proceso, 'paso'=>2 );
        $reporte = null;
        return view ('partials.manejador_dte.liquidaciones_user',compact('extra_data', 'reporte'));
    }


    public function f1_enviar(Request $request)
    {   
        $permisos= session('permisos');
        //dd($permisos);
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p1', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id = $request->input('id');
        $date = Carbon::now(); //cada 2 horas 
        $idUsuarioLogueado = auth()->id();

         $liquidacion = Fac_liquidacion_factura::
                            where('id_liquidacion', $id)
                                ->count();

        if($liquidacion == 0){
            return response()->json(['message' => 'No tiene facturas asignadas en esta liqudación, mantengala activa hasta tener alguna que asignar',"status"=> false]);
        }

        $liquidacion = Fac_liquidacion::
            where('id', $id)
                ->where('id_usuario', $idUsuarioLogueado)
                ->where('estado', 1)
                ->get();
        
        if( count($liquidacion) == 1){
            $estado_siguiente =  2; //$liquidacion[0]->id_area == 4 ? 2 : 3;
            Fac_liquidacion::
                where('id', $id)
                ->update([
                    'estado' => $estado_siguiente,
                    'updated_at' => $date,
                    'set_estado_2'=> $idUsuarioLogueado
                ]);
            return response()->json(['message' => 'Listo!',"status"=> true]);
        }else{
            return response()->json(['message' => 'Hubo un error al buscar, quizà ya la guardaste!',"status"=> false]);
        }
    }
    
    public function f2_reload_facturas_from_liqu(Request $request){
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $reporte = $request->tipo == 'dte_norm' ? 
                        $this->facturas_liquidation($request->get('id'), $request->get('paso')): 
                        $this->excluidos_liquidation($request->get('id'), $request->get('paso'));
        //dd($reporte);
        $reporte = count($reporte) == 0 ? null : $reporte;
        $extra_data = Array( 'paso'=> $request->get('paso'), 'zona'=> $request->get('zona') );
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'json_interno', 'pdf_interno', 'estado' ];

        return view ('partials.manejador_dte.liquidaciones_facturas',compact('reporte', 'extra_data', 'no_render_columns'));
    }

    
    public function facturas_liquidation($id, $paso = null, $en_proceso= null){
        $reporte =  DB::
                    table('factura_electronicas');
                        
        if($paso > 4){
            $reporte = $reporte->select('factura_electronicas.id',  'codigoGeneracion',        'nombreComercial',       'totalPagar',
                'id_user',                  'fechaEmision',             'nit',                  'telefono',
                'pdf',                      'json',                     'direccion_origen',     'fecha_correo', 
                'fac_liquidacions.id_user_tesoreria',        'json_tributos',            'inactiva',             'descripcion',          
                'factura_electronicas.sello','num_registro_diario',     'num_entrada_mercaderia', 
                'local_sello', 'local_codigo', 'local_asiento_diario', 'json_interno', 'pdf_interno', 'fac_liquidacions.estado'
            );
        }else{
            $reporte = $reporte->select(
                'factura_electronicas.id',  'codigoGeneracion',        'nombreComercial',       'totalPagar',
                'id_user',                  'fechaEmision',             'nit',                  'telefono',
                'pdf',                      'json',                     'direccion_origen',     'fecha_correo', 
                'fac_liquidacions.id_user_tesoreria',        'json_tributos',            'inactiva',             'descripcion',          
                'factura_electronicas.sello','num_registro_diario',     'num_entrada_mercaderia', 'json_interno', 'pdf_interno', 
                'fac_liquidacions.estado'
            );
        }
        $reporte = $reporte
                        ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_factura','factura_electronicas.id')
                        ->leftJoin('fac_liquidacions', 'fac_liquidacions.id', 'fac_liquidacion_facturas.id_liquidacion');
                        //->where('created_at','>',$expiredAt)

        //si ambos son null entonces espero la lista del reporte general por lo que no filtro nada
        if($id != null || $paso !=null){
            $reporte = $reporte->whereNotNull('id_user');
        }
        if($en_proceso){
            $reporte = $reporte
                        ->selectRaw(
                            "CONCAT(fac_liquidacions.set_estado_2,',', fac_liquidacions.set_estado_3,',',fac_liquidacions.set_estado_5,',',fac_liquidacions.set_estado_6) AS usuarios_proceso"
                        )
                        ->whereNotNull('fac_liquidacions.estado')
                        ->orderBy('factura_electronicas.id', 'desc')
                        ->limit(2000)
            ;
        }

        if($id != null){
            $reporte = $reporte->where('fac_liquidacion_facturas.id_liquidacion', $id);
        }

        $reporte =  $reporte->get();
        
        return $reporte;
    }
    /* 
        En proceso es un documento que se ha empezado a procesar osea ya tiene liquidacion o sujeto excluido

    */
    public function excluidos_liquidation($id, $paso = null, $en_proceso= null){ 
        $reporte =  DB::
                    table('factura_electronicas');
        if($paso > 4){
            $reporte =  $reporte->select(
                'factura_electronicas.id',  'codigoGeneracion',        'nombreComercial',       'totalPagar',
                'id_user',                  'fechaEmision',             'nit',                  'telefono',
                'pdf',                      'json',                     'direccion_origen',     'fecha_correo', 
                'id_user_tesoreria',        'json_tributos',            'inactiva',             'descripcion',          
                'factura_electronicas.sello','num_registro_diario',     'id_norma_reparto_sap as num_entrada_mercaderia',
                'local_sello', 'local_codigo', 'local_asiento_diario',  'json_interno',         'pdf_interno', 'fac_sujeto_excluido.estado');
        }else{
            $reporte =  $reporte->select(
                'factura_electronicas.id',  'codigoGeneracion',        'nombreComercial',       'totalPagar',
                'id_user',                  'fechaEmision',             'nit',                  'telefono',
                'pdf',                      'json',                     'direccion_origen',     'fecha_correo', 
                'id_user_tesoreria',        'json_tributos',            'inactiva',             'descripcion',          
                'factura_electronicas.sello','num_registro_diario',     'id_norma_reparto_sap as num_entrada_mercaderia', 'json_interno', 'pdf_interno', 
                'fac_sujeto_excluido.estado');
        }
          
        $reporte = $reporte->leftJoin('fac_sujeto_excluido', 'fac_sujeto_excluido.id_factura_electronica','factura_electronicas.id');
                      
        if($id != null){
            $reporte = $reporte->whereNotNull('fac_sujeto_excluido.id', $id);
        }

        if($en_proceso){
            $reporte = $reporte
                        ->selectRaw("CONCAT(fac_sujeto_excluido.set_estado_5,',',fac_sujeto_excluido.set_estado_6) AS usuarios_proceso")
                        ->whereNotNull('fac_sujeto_excluido.estado');
        }
        
        $reporte = $reporte->get();
        
        return $reporte;
    }
    
    public function f2_enviar(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p1', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id = $request->input('id');
        $date = Carbon::now(); //cada 2 horas 
        $idUsuarioLogueado = auth()->id();

         $liquidacion = Fac_liquidacion_factura::
                            where('id_liquidacion', $id)
                                ->count();

        if($liquidacion == 0){
            return response()->json(['message' => 'No tiene facturas asignadas en esta liqudación, mantengala activa hasta tener alguna que asignar',"status"=> false]);
        }

        $liquidacion = Fac_liquidacion::
            where('id', $id)
                //->where('id_usuario', $idUsuarioLogueado)
                ->where('estado', 2)
                ->get();
        //dd($liquidacion); die;
        if( count($liquidacion) == 1){
            Fac_liquidacion::
                where('id', $id)
                ->update([
                    'estado' => 3,
                    'updated_at' => $date,
                    'set_estado_3'=> $idUsuarioLogueado
                ]);
            return response()->json(['message' => 'Listo!',"status"=> true]);
        }else{
            return response()->json(['message' => 'Hubo un error al buscar, contacta con ti para verificar',"status"=> false]);
        }
    }

    public function f3_enviar(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p2', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id = $request->input('id');
        $date = Carbon::now(); //cada 2 horas 

         $liquidacion = Fac_liquidacion_factura::
                            where('id_liquidacion', $id)
                                ->count();

        if($liquidacion == 0){
            return response()->json(['message' => 'No tiene facturas asignadas en esta liqudación, mantengala activa hasta tener alguna que asignar',"status"=> false]);
        }

        $liquidacion = Fac_liquidacion::
            where('id', $id)
                ->where('estado', 3)
                ->get();
        //dd($liquidacion); die;
        if( count($liquidacion) == 1){
            Fac_liquidacion::
                where('id', $id)
                ->update([
                    'estado' => 4,
                    'updated_at' => $date,
                ]);
            return response()->json(['message' => 'Listo!',"status"=> true]);
        }else{
            return response()->json(['message' => 'Hubo un error al buscar, contacta con ti para verificar',"status"=> false]);
        }
    }

    public function f5_enviar(Request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id = $request->input('id');
        $date = Carbon::now(); //cada 2 horas 
        $idUsuarioLogueado = auth()->id();
        
        $liquidacion = $request->input('tipo_estado') == 'exclu' ? 
                            Fac_sujeto_excluido::
                                select('id')
                                ->where('id', $id)
                                ->where('estado', 4)
                                ->first() 
                            :
                            Fac_liquidacion::
                                select('id')
                                ->where('id', $id)
                                ->where('estado', 4)
                                ->first()
                            ;
        

        if($liquidacion == null){
            return response()->json(['message' => 'Parece que esta liquidación aun no esta en estado 4, o fue eliminada',"status"=> false]);
        }
        
        /*$liquidacion = Fac_liquidacion::
            where('id', $liquidacion->id)
                ->where('estado', 4)
                ->where('id_user_asignado', $idUsuarioLogueado)
                ->get();*/

        //dd($liquidacion); die;
        $request->input('tipo_estado') == 'exclu' ?
            Fac_sujeto_excluido::
                where('id', $liquidacion->id)
                ->update([
                    'estado' => 5,
                    'updated_at' => $date,
                    'set_estado_5'=> $idUsuarioLogueado
            ])
            :
            Fac_liquidacion::
                where('id', $liquidacion->id)
                ->update([
                    'estado' => 5,
                    'updated_at' => $date,
                    'set_estado_5'=> $idUsuarioLogueado
            ]);
            return response()->json(['message' => 'Listo!',"status"=> true]);
        
    }

    public function f0_enviar_factura(Request $request)
    {
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect([ 'all'], $permisos))){ // 'f_p_M1',
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $date = Carbon::now();
        $factura_repetida = 0;
        $request->id_edit == null ?
            $factura_repetida = Factura_electronica::
                where('codigoGeneracion', strtoupper($request->codigoGeneracion))
                    ->whereNull('inactiva')
                        ->count():
            0;

        if($factura_repetida > 0){
            return response()->json(['message' => 'Ya existe una factura con este código de generación, debe inhabilitarla para poder subir esta factura',"status"=> false]);
        }
        //dd($request->tipo_creacion_dte, $request->id_edit);
        if( $request->tipo_creacion_dte == 2 and $request->id_edit != NULL ){
            return response()->json($this->editar_reg($request));
        }else if ( $request->tipo_creacion_dte == 1 and $request->id_edit == NULL ){
            return response()->json($this->crear_reg($request));
        }else{
            return response()->json(['message' => 'Parametros incorrectos, si si edita un registro debe indicar cual es',"status"=> false]);
        }
        
    }

    function editar_reg( $request ){
        $idUsuarioLogueado = auth()->id();
        // Validar que el id sea un entero
        $id = filter_var( intval( $request->id_edit), FILTER_VALIDATE_INT );
        if ( !$id ) {
            return [
                'message' => 'Id incorrecto',
                "status"=> false
            ];
        }

        $valida = Fac_liquidacion:: select('factura_electronicas.id')
                                    ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_liquidacion', 'fac_liquidacions.id')
                                        ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                                            ->where('factura_electronicas.id', $id)
                                            ->where('fac_liquidacions.estado', null)
                                                ->first();
        if ( $valida !=null ) {
            return [
                'message' => 'Esta factura no existe, esta deshabilitada o ya se encuentra dentro del proceso de trabajo',
                "status"=> false
            ];
        }
        $facturaElectronica = Factura_electronica::
                                where('id', $id)->first()
                                ->where('codigoGeneracion', $request->codigoGeneracion)->first();

        if ( $facturaElectronica == null ) {
            return [
                'message' => 'Esta factura no existe, revisa que el id y el código de generacion coincidan',
                "status"=> false
            ];
        }

        $facturaElectronica->codigoGeneracion =     $request->codigoGeneracion != null ?    $request->codigoGeneracion : $facturaElectronica->codigoGeneracion;
        $facturaElectronica->nombreComercial =      $request->nombreComercial != null ?     $request->nombreComercial : $facturaElectronica->nombreComercial;
        $facturaElectronica->nit =                  $request->nit != null ?                 $request->nit : $facturaElectronica->nit;
        $facturaElectronica->telefono =             $request->telefono != null ?            $request->telefono : $facturaElectronica->telefono;
        $facturaElectronica->totalPagar =           $request->totalPagar != null ?          $request->totalPagar : $facturaElectronica->totalPagar;
        $facturaElectronica->pdf =                  $request->pdf != null ?                 $request->pdf : $facturaElectronica->pdf;
        $facturaElectronica->json =                 $request->json != null ?                $request->json : $facturaElectronica->json;
        $facturaElectronica->fechaEmision =         $request->fechaEmision != null ?        $request->fechaEmision : $facturaElectronica->fechaEmision;
        $facturaElectronica->direccion_origen =     $request->direccion_origen != null ?    $request->direccion_origen : $facturaElectronica->direccion_origen;
        $facturaElectronica->fecha_correo =         $request->fecha_correo != null ?        $request->fecha_correo : $facturaElectronica->fecha_correo;
        $facturaElectronica->descripcion =          $request->descripcion != null ?         $request->descripcion : $facturaElectronica->descripcion;
        $facturaElectronica->json_tributos =        $request->json_tributos != null ?       $request->json_tributos : $facturaElectronica->json_tributos;
        $facturaElectronica->sello =                $request->sello != null ?               $request->sello : $facturaElectronica->sello;
        $facturaElectronica->id_user_process =  $idUsuarioLogueado;
    
        // Guardar los cambios en la base de datos
        $facturaElectronica->save();
        // Devolver un mensaje de éxito
        return [
            'success' => 'El registro se ha editado correctamente.',
            "status"=> true
        ];
    }

    function crear_reg( $request ){
        //$liquidacion_factura = new Fac_liquidacion_factura;
        $idUsuarioLogueado = auth()->id();
        $facturaElectronica = new Factura_electronica;
        try {
            $facturaElectronica->codigoGeneracion = $request->codigoGeneracion;
            $facturaElectronica->nombreComercial =  $request->nombreComercial;
            $facturaElectronica->nit =              $request->nit;
            $facturaElectronica->telefono =         $request->telefono;
            $facturaElectronica->totalPagar =       $request->totalPagar;
            $facturaElectronica->pdf =              $request->pdf;
            $facturaElectronica->json =             $request->json;
            $facturaElectronica->fechaEmision =     $request->fechaEmision;
            $facturaElectronica->direccion_origen = $request->direccion_origen;
            $facturaElectronica->fecha_correo =     $request->fecha_correo;
            $facturaElectronica->descripcion =      $request->descripcion;
            $facturaElectronica->json_tributos =    $request->json_tributos;
            $facturaElectronica->sello =            $request->sello;
            $facturaElectronica->id_user_process =  $idUsuarioLogueado;
            $facturaElectronica->save();
        } catch (Exception $e) {
            // Handle the exception
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return [
            'success' => 'El registro se ha creado correctamente.',
            "status"=> true
        ];
    }

    public function f5_registro_diario(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p4', 'f_p_admin', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        //dd($permisos);
        $id                 = $request->input('dataId');
        $num_diario         = $request->input('num_diario');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();
        
        $facturas = $request->data_tipo != 'exclu' ?  // si es una de sujetos excluidos entonces : 
            Fac_liquidacion:: 
                select('factura_electronicas.id', 'fac_liquidacions.estado', 'fac_liquidacions.id_user_asignado')
                    ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_liquidacion', 'fac_liquidacions.id')
                        ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                            ->where('factura_electronicas.id', $id)
                            //->where('fac_liquidacions.estado', 4)
                            //->where('fac_liquidacions.id_user_asignado', $idUsuarioLogueado)
                                ->first()
            :
            Fac_sujeto_excluido:: 
                select('factura_electronicas.id', 'fac_sujeto_excluido.estado', 'fac_sujeto_excluido.id_user_asignado')
                        ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_sujeto_excluido.id_factura_electronica')
                            ->where('factura_electronicas.id', $id)
                                ->first()
        ;
        //dd($facturas);

        if( $facturas == null){
            return response()->json(['message' => 'No se encontró la liquidación',"status"=> false]);
        }else if( ($facturas->estado > 4 || $facturas->id_user_asignado != $idUsuarioLogueado) && empty(array_intersect(['f_p_admin', 'all'], $permisos))){
            return response()->json(['message' => 'No se encontró la liquidación que puedas editar con tus privilegios',"status"=> false]);
        }else{
            // Actualizar los registros en la base de datos
            Factura_electronica::
                where('id', $facturas->id)
                ->update([
                    'num_registro_diario' => $num_diario,
                    'updated_at' => $fechaActual
                ]);

            $idsActualizados = [$id];

            return response()->json([
                'message' => 'Actualizada correctamente',
                'idsActualizados' => $idsActualizados,
                "status"=> true
            ]);
        }
        
    }

    public function f3_registro_num_entrada(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p2', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $id                 = $request->input('dataId');
        $num_reg_mercad         = $request->input('num_reg_mercad');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();
        
        $facturas           = Fac_liquidacion:: 
                                select('factura_electronicas.id')
                                    ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_liquidacion', 'fac_liquidacions.id')
                                        ->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                                            ->where('factura_electronicas.id', $id)
                                            ->where('fac_liquidacions.estado', 2) //en el estado correcto
                                            ->where('fac_liquidacions.id_area','!=', 4) // para el area 4
                                                ->first();
       
        if ($facturas == null) {
            return response()->json(['message' => 'No se encontró la liquidación correspondiente en estado 5 para este usuario',"status"=> false]);
        }else{
            // Actualizar los registros en la base de datos
            Factura_electronica::
                where('id', $id)
                ->update([
                    'num_entrada_mercaderia' => $num_reg_mercad,
                    'updated_at' => $fechaActual,
                    'set_entrada_merc'=> $idUsuarioLogueado
                ]);

            $idsActualizados = [$id];

            return response()->json([
                'message' => 'Actualizada correctamente',
                'idsActualizados' => $idsActualizados,
                "status"=> true
            ]);
        }
        
    }

    public function ver_impuestos(Request $request){
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        //$idUsuarioLogueado = auth()->id();
        $data = $this->facturas_impuestos($request->get('id'));
        return $data;
    }

    public function facturas_impuestos($id){
        $id = intval($id);
        $idUsuarioLogueado = auth()->id();
        $data = DB::table('factura_electronicas')
                    ->select('id', 'json_tributos')
                    ->where('id', $id)
                    ->first();  // Usar first() en lugar de get() para obtener solo una fila

        if ($data) {
            // Obtener el valor de json_tributos y analizarlo como JSON
            $jsonTributos = json_decode($data->json_tributos);

            // Crear un nuevo arreglo asociativo con id y json_tributos
            $resultado = [
                'success'=> true,
                'id' => $data->id,
                'json_tributos' => $jsonTributos
            ];

            // Convertir el arreglo asociativo en un JSON
            $jsonResultado = json_encode($resultado);
            // Devolver el JSON resultante
            return $jsonResultado;
        } else {
            // Manejar el caso en el que no se encontró ningún registro con el ID dado
            return json_encode(['success'=> false, 'error' => 'Registro no encontrado'], 404);
        }
    }

    public function factura_electronica(Request $request){
     
        try {
            $factura = new Factura_electronica;
            $factura->codigoGeneracion = $request->codigo_generacion;
            $factura->nombreComercial = $request->nombre_comercial;
            $factura->nit = $request->nit;
            $factura->telefono= $request->telefono;
            $factura->totalPagar= $request->total_pagar;
            $factura->pdf= $request->pdf;
            $factura->json= $request->json;
            $factura->fechaEmision= $request->fecha_emision;
            $factura->direccion_origen= $request->direccion_origen;
            $factura->fecha_correo= $request->fecha_correo;
            $factura->json_tributos= $request->tributos;
            $factura->sello= $request->sello;
            $factura->tipo_dte= $request->tipo_dte;
            $factura->message_id= $request->message_id;

            $factura->iva_percibido= $request->iva_percibido;
            $factura->valor_operaciones= $request->valor_operaciones;
            $factura->monto_sujeto_percepcion= $request->monto_sujeto_percepcion;
            $factura->numero_control= $request->numero_control;
            $factura->nit_receptor = $request->nit_receptor;

            $factura->save();
        } catch (Exception $e) {
            // Handle the exception
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

    
        // Obtener la factura de la base de datos después de haberla guardado
        /*$factura = Factura_electronica::where('codigoGeneracion', $request->codigo_generacion)
        ->orderBy('id', 'DESC')
        ->first();*/
       
        if ($request->tipo_dte == '14' && $request->nit == '06142910081021') {
            // Estado del elemento, para sujetos excluidos de la empresa pasar a estado 3
            $estado = 3;
            $sujeto = new Fac_sujeto_excluido;
            $sujeto->num_entrada_sap = '-';
            $sujeto->id_factura_electronica = $factura->id;
            $sujeto->estado =  $estado;
            $sujeto->save();
        }
        // Reemplazar las comillas simples por comillas dobles
        $cadena_json = str_replace("'", "\"", $request->tributos);
        /*if($cadena_json != 'null' && $cadena_json != null ){
            $array_tributos = $cadena_json != '{}' ?  json_decode($cadena_json, true) : [];
           
                foreach ($array_tributos as $key => $value) {
                
                    $tipo_tributo = DB::table('fac_tipos_tributos')
                                ->select('id') 
                                ->Where('codigo', $value['codigo'])
                                ->first();
                    $tributos_factura = new Fac_montos_tributo_factura;
                    $tributos_factura->monto = $value['valor'];
                    $tributos_factura->id_factura = $factura->id;
                    $tributos_factura->id_tipo_tributo = $tipo_tributo->id;
                    $tributos_factura->save();
                }
            
            
        }*/
    
        return response()->json(['code'=>1]);  
    }

    public function get_archivos()
    {   
        $reporte = DB::table('archivos')->orderBy('created_at','desc')->get();
        return $reporte;
    }

    public function get_list_all()
    {   
        try {
            // Obtener los message_id de la base de datos
            $messageIds = Factura_electronica::select('message_id')->orderBy('id', 'asc')->pluck('message_id')->toArray();

            // Crear un arreglo asociativo con la lista de message_id
            $data = ['messageIds' => $messageIds];

            // Convertir el arreglo asociativo a formato JSON
            $json_data = json_encode($data);

            // Devolver la cadena JSON directamente
            return response($json_data)
                    ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Manejar la excepción, por ejemplo, loggearla o retornar un error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fx_registro_extra(Request $request)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_px', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        
        $id                 = $request->input('dataId');
        $dato_extra         = $request->input('dato_extra');
        $data_tipo          = $request->input('data_tipo');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();
        
        $facturas           = Factura_electronica:: 
                                select('factura_electronicas.id', 'fac_liquidacions.estado as liquidacion', 'fac_sujeto_excluido.estado as excluido')
                                    ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_factura', 'factura_electronicas.id')
                                    ->leftJoin('fac_liquidacions', 'fac_liquidacions.id', 'fac_liquidacion_facturas.id_liquidacion')
                                    ->leftJoin('fac_sujeto_excluido', 'fac_sujeto_excluido.id_factura_electronica', 'factura_electronicas.id')
                                        //->leftJoin('factura_electronicas', 'factura_electronicas.id', '=', 'fac_liquidacion_facturas.id_factura')
                                            ->where('factura_electronicas.id', $id)
                                            //->whereIn('fac_liquidacions.id_area', Array(4,5,6)) // para el area 4
                                            ->first();
       //dd($facturas);
        if ($facturas == null) {
            return response()->json(['message' => 'No se encontró la liquidación correspondiente para asignar',"status"=> false]);
        }else if( $facturas->id && ( $facturas->liquidacion> 3 || $facturas->excluido > 3 )  ){

            //dd( ($facturas->liquidacion> 4 || $facturas->excluido > 4), !empty(array_intersect(['f_p_admin', 'all'], $permisos)) );

            if(($facturas->liquidacion> 4 || $facturas->excluido > 4) && empty(array_intersect(['f_p_admin', 'all'], $permisos))){
                
                return response()->json(['message' => 'Ya no puedes modificar este dato, solo el admin de esta sección puede',"status"=> false]);
            }
           
            switch ($data_tipo) {
                case 'local_sello':
                    $datos = [
                        'local_sello' => $dato_extra,
                        'updated_at' => $fechaActual
                    ];
                    break;
                case 'local_codigo':
                    $datos = [
                        'local_codigo' => $dato_extra,
                        'updated_at' => $fechaActual
                    ];
                    break;
                case 'local_asiento_diario':
                    $datos = [
                        'local_asiento_diario' => $dato_extra,
                        'updated_at' => $fechaActual
                    ];
                    break;
                default:
                    return response()->json([
                        'message' => 'ERROR',
                        'idsActualizados' => 0,
                        "status"=> true
                    ]);
                    break;
            }

            // Actualizar los registros en la base de datos
            Factura_electronica::
                where('id', $id)
                ->update(
                    $datos
                );

            $idsActualizados = [$id];

            return response()->json([
                'message' => 'Actualizada correctamente',
                'idsActualizados' => $idsActualizados,
                "status"=> true
            ]);
        }else{
            return response()->json(['message' => 'No se encontró una liquidacion que permita editar este dato',"status"=> false]);
        }
        
    }

    public function upload_pdf_json(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:json,pdf|max:10240', // Cambiado a 10 megabytes
        ]);
        $file = $request->file('file');
        $timestamp = now()->format('Y_m_d_H_i_s');
        $randomString = Str::random(4);
        $fileName = "{$timestamp}_{$randomString}.{$file->getClientOriginalExtension()}";
       
        $id_factura = (Integer) $request->id_factura;
        $subida_hacia_dte = 
            ($request->tipo_file == 'PDF' || $request->tipo_file == 'JSON')  && $id_factura > 0 ? TRUE : FALSE;


            // Verificamos si el archivo con el mismo nombre ya existe
        if (file_exists(public_path('files_dte/' . $fileName))) {
            return response()->json(['success' => false, 'message' => '¡Error! El archivo con el mismo nombre ya existe.']);
        }

        $path = public_path('files_dte');
        if($subida_hacia_dte){

            $path = public_path('files_dte_internos');
            dd($path);
            $file->move($path, $fileName);
            if($request->tipo_file == 'PDF'){
                $info = ['pdf_interno'=>$fileName ];
            }else if($request->tipo_file == 'JSON'){
                $info = ['json_interno'=>$fileName ];
            }
            Factura_electronica::
                where('id', $id_factura)
                ->update( $info );
        }else{
           
            $file->move($path, $fileName);
            $url = asset('files_dte/' . $fileName);
            return response()->json(['success' => true, 'message' => 'Listo!', 'fileName' => $fileName, 'url' => $url]);
        }

        return response()->json(['success' => true, "message"=>"Listo!", 'fileName' => $fileName]);
    }

    public function f6_marca_final(Request $request, $id_dte, $tipo)
    {   
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p6', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene accesoa esta funciòn', "status"=> false ]);
        }
        //$id                 = $request->input('id_dte');
        $idUsuarioLogueado  = auth()->id();
        $fechaActual        = Carbon::now();

        $facturas           = Factura_electronica:: 
            select('factura_electronicas.id')
                ->leftJoin('fac_liquidacion_facturas', 'fac_liquidacion_facturas.id_factura', 'factura_electronicas.id')
                    ->leftJoin('fac_liquidacions', 'fac_liquidacions.id', '=', 'fac_liquidacion_facturas.id_liquidacion')
                ->leftJoin('fac_sujeto_excluido', 'fac_sujeto_excluido.id_factura_electronica', '=', 'factura_electronicas.id')
                        ->where(function($query) {
                            $query->where('fac_liquidacions.estado', 5)
                                ->orWhere('fac_sujeto_excluido.estado', 5);
                        });
        if($tipo == 'dte_norm')
            $facturas= $facturas->where('fac_liquidacions.id', $id_dte)
            ->get();
        elseif($tipo == 'exclu'){
            $facturas= $facturas->where('fac_sujeto_excluido.id', $id_dte)
            ->get();
        }

        if ($facturas == null) {
            return response()->json(['message' => 'No se encontró un elemento en estado 5 que puedas marcar',"status"=> false]);
        }else{
            // Actualizar los registros en la base de datos
        
            if($tipo == 'dte_norm')
                Fac_liquidacion::
                    where('id', $id_dte)
                    ->update([
                        'marca_recibido' => true,
                        'updated_at' => $fechaActual,
                        'set_estado_6'=> $idUsuarioLogueado

                    ]);
            elseif($tipo == 'exclu'){
                Fac_sujeto_excluido::
                where('id', $id_dte)
                ->update([
                    'marca_recibido' => true,
                    'updated_at' => $fechaActual,
                    'set_estado_6'=> $idUsuarioLogueado
                ]);;
            }

            $idsActualizados = [$id_dte];

            return response()->json([
                'message' => 'Actualizada correctamente',
                'idsActualizados' => $idsActualizados,
                "status"=> true
            ]);
        }
        
    }

    public function lista_facturas_f7(Request $request){
        $permisos= session('permisos');
        if(false) //$permisos == null)
            return redirect('/login');
        else if(empty(array_intersect(['f_p6', 'all'], $permisos))){
            return response()->json($response = [ 'message' => 'No tiene acceso', "status"=> false ]);
        }
        $reporte = $liquidaciones_proceso= $this->facturas_liquidation( null, null, true )->merge( $this->excluidos_liquidation( null, null, true ) );


        //dd($reporte);
        $reporte = count($reporte) == 0 ? null : $reporte;
       
        $no_render_columns= ['json_tributos', 'id_user_tesoreria', 'num_entrada_mercaderia', 'direccion_origen', 'json', 'fecha_correo', 'json_interno'];

        return view ('manejador_dte.informe_general',compact('reporte', 'no_render_columns'));
    }


    public function tabla_datos(request $request)
    {
        $permisos= session('permisos');
       
        //$fechaLimite = Carbon::now()->subDays(20)->toDateString(); //360 días
   

        $tipoDte = $request->get('tipo_dte');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $nit = $request->get('nit');
        $nombre = $request->get('nombre');

        $reporte = DB::table('factura_electronicas')
                        ->select(
                            "id",
                            "tipo_dte",
                            "nit",
                            "totalPagar",
                            "monto_sujeto_percepcion",
                            "valor_operaciones",
                            "iva_percibido",
                            "fechaEmision",
                            "json_tributos",
                            "pdf",
                            "json",
                            "numero_control",
                            "telefono",
                            "nombreComercial",
                            "codigoGeneracion",
                            "sello",
                            )
                        //->where('created_at','>=',$fechaLimite)
                        ->whereNull('id_user');
                        //->get();
        if (!empty($tipoDte)) {
            $reporte = $reporte->whereIn('tipo_dte', $tipoDte);
        }

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $reporte = $reporte->whereBetween('fechaEmision', [$fechaInicio, $fechaFin]);
        }

        if (!empty($nit)) {
            $reporte = $reporte->where('nit', 'like', "%$nit%");
        }

        if (!empty($nombre)) {
            $reporte = $reporte->where('nombreComercial', 'like', "%$nombre%");
        }
   
        return response()->json(['success' => false, 'data'=> $reporte->get(), 'message' => '']);
    }


}
