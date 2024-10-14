# -*- coding: utf-8 -*-
import os
import email
from email.header import decode_header
from email.utils import parsedate
from datetime import datetime, timedelta
import json # Agregar esta línea para importar el módulo json
import requests
import re
import time
import chardet
import random
import string
import shutil
import dotenv
import os
import json
from json.decoder import JSONDecodeError

dotenv.load_dotenv()
# Ruta del directorio donde se encuentran los archivos de correo electrónico

directorio_correos      = os.environ.get("DIRECTORIO_CORREOS")
directorio_adjuntos      = os.environ.get("DIRECTORIO_ADJUNTOS")  
url_solicitud_post      = os.environ.get("URL_SOLICITUD_POST")  
url_lista_procesados    = os.environ.get("URL_LISTA_PROCESADOS")  
NIT                     = os.environ.get("NIT")
APP_URL                 = os.environ.get("APP_URL")
# Obtener la marca de tiempo actual
ahora = datetime.now()
# Calcular la marca de tiempo correspondiente a 2 horas antes
dos_horas_atras = ahora - timedelta(hours=16000)
resultado_final = u''

CONTADOR = 0
CONTADOR_CORREO = 0
CONTADOR_JSON = 0

def limpiar_texto(texto):
  global resultado_final # Declarar resultado_final como global
  # Eliminar caracteres no alfanuméricos y etiquetas HTML
  limpio = re.sub(r'<.*?>|[^a-zA-Z0-9\s]', '', texto)
  return limpio.strip()  

def enviar_texto_a_ruta(texto):
  global resultado_final # Declarar resultado_final como global
  try:
    payload = {'texto': texto} # Cambia 'texto' por el nombre del campo que quieras en tu solicitud POST
    response = requests.post('https://almacenesbomba.com/sendResponsescanner', data=payload)
    #print("Respuesta del servidor:", response.status_code)
    resultado_final += "Respuesta del servidor:" + str(response.status_code)
  except Exception as e:
    #print("Error al enviar el texto:", e)
    resultado_final += "Error al enviar el texto:" + str(e)

def obtener_fecha(correo):
  global resultado_final # Declarar resultado_final como global
  fecha_correo = correo.get("Date", None)
  if fecha_correo:
    fecha_correo_dt = parsedate(fecha_correo)
    if fecha_correo_dt:
      fecha_correo_str = datetime(*fecha_correo_dt[:6])
      return fecha_correo_str
  return None
def obtener_direccion_origen(correo):
  global resultado_final # Declarar resultado_final como global
  direccion_origen = correo.get("From", None)
  if direccion_origen:
    # Extraer la dirección de correo electrónico utilizando expresiones regulares
    match = re.search(r'<([^>]+)>', direccion_origen)
    if match:
      direccion_origen = match.group(1)
  return direccion_origen

def obtener_hora_actual():
  global resultado_final # Declarar resultado_final como global = datetime.now()
  hora_minuto_segundo = '_'.join([str(ahora.hour), str(ahora.minute), str(ahora.second)])
  return hora_minuto_segundo

def extraer_datos_json(datos):
  global resultado_final # Declarar resultado_final como global
  # Extraer los datos del JSON
  try:
    section_identificacion = datos.get('identificacion', {})
    section_emisor = datos.get('emisor', {})
    section_receptor = datos.get('receptor', {})
    codigo_generacion = ''
    motivo = datos.get('motivo', '')
    
    datos_orig = datos  
    if section_identificacion == {} and section_emisor == {}:
      datos = datos.get('dteJson', {})
      section_identificacion = datos.get('identificacion', {})
    if( section_emisor == {}):
        section_emisor = datos.get('emisor', {})
    if( section_receptor == {}):
        section_receptor = datos.get('receptor', {})
    
    nit_receptor = section_receptor.get('nit', {})
    if(nit_receptor == {} ):
        nit_receptor = section_receptor.get('numDocumento', {})
    

    if section_emisor.get('nit', 'sin_dato')=='sin_dato':
      print_json( 'ERROR_no_se_identifica_emisor','no se encontro informacion del emisor NOTIFICAR A TI', direccion_origen, None, '')
    
    section_resumen = datos.get('resumen', {})
    cuerpo = datos.get('cuerpoDocumento', {})
      
    if isinstance(cuerpo, list):
      # Si 'cuerpo' es una lista, recórrela
      elementos = 0
      if cuerpo is not None:
          
        for elemento in cuerpo:
          elementos = elementos+1
          monto_sujeto_percepcion = elemento.get('montoSujetoPercepcion', 0)
          iva_percibido = elemento.get('ivaPercibido', 0)
          valor_operaciones = elemento.get('valorOperaciones', 0)
        if elementos > 1:
            print_json( 'advertencia_multiples_lineas', 'verificar montos', direccion_origen, None, '')
      else:
        monto_sujeto_percepcion = cuerpo.get('montoSujetoPercepcion', 0)
        iva_percibido = cuerpo.get('ivaPercibido', 0)
        valor_operaciones = cuerpo.get('valorOperaciones', 0)
    else:
      monto_sujeto_percepcion = cuerpo.get('montoSujetoPercepcion', 0)
      iva_percibido = cuerpo.get('ivaPercibido', 0)
      valor_operaciones = cuerpo.get('valorOperaciones', 0)
        
    codigo_generacion = section_identificacion.get('codigoGeneracion',  
                datos_orig.get('codigoGeneracion',  
                  datos_orig.get('responseMH', {}).get('codigoGeneracion', '-')
                )
              )
    numero_control = section_identificacion.get('numeroControl',  
                datos_orig.get('numeroControl',  
                  datos_orig.get('responseMH', {}).get('numeroControl', '-')
                )
              )
    response_hacienda = datos.get('respuestaHacienda', datos.get('responseMH', {} ) )
      
    sello = response_hacienda.get('selloRecibido',
                datos_orig.get('selloRecibido',  
                          datos_orig.get(
                            'responseMH',{}
                          ).get('selloRecibido', '-')  
                        )
              )
    if sello == {}:
      print_json( 'OBSERVACION_sin_sello', 'no se identificó el sello', direccion_origen, None, '')
    
    if motivo != '':
      print_json( 'OBSERVACION_motivo', motivo.motivoAnulacion, direccion_origen, None, '')
      

    nombre_comercial = section_emisor.get('nombreComercial', section_emisor.get('nombre', '-'))
    nit = section_emisor.get('nit', '-')
    telefono = section_emisor.get('telefono', '-')
    total_pagar = section_resumen.get('totalPagar', 0 )
    fecha_emision = section_identificacion.get('fecEmi', None)
    tributos = section_resumen.get('tributos', {})
   

    sello = sello if sello != "-" else datos.get('sello', '-')
    tipoDte = section_identificacion.get('tipoDte', '-')
    if(section_receptor == {} and tipoDte != '14'):
      #print('ERROR: NO SE HA IDENTIFICADO RECEPTOR, REVISAR ANTES DE PROCESAR')
      print_json( 'ERROR_no_se_identifica_receptor','no se encontro el receptor, deben revisar si esta bien el json', direccion_origen, None, '')

    if nombre_comercial == "-":
      section_emisor.get('nombre', '-')

    if tipoDte == "-":
      print_json( 'Advertencia_no_tipo_dte','NO SE SABE QUE TIPO DE DTE ES', direccion_origen, None, '')
    return (
        codigo_generacion, nombre_comercial, nit, telefono,  
        total_pagar, fecha_emision, tributos, sello,  
        tipoDte, iva_percibido, valor_operaciones, monto_sujeto_percepcion,  
        numero_control, nit_receptor
    )
  except Exception as e:
    print_json( 'ERROR_AL_EXTRAER','REVISAR EL LOS ARCHIVOS'+str(e), direccion_origen, None, '')
    return '-', '-', '-', '-',    '-', '-', '-', '-',      '-', '-', '-', '-',      '-', '-'
    
def limpiar_contenido_json(contenido_json):
  global resultado_final # Declarar resultado_final como global
  contenido_limpio = contenido_json.replace('\n', '').replace('\r', '').replace('\t', '').strip()
  return contenido_limpio

def cargar_json_desde_archivo(ruta_archivo):
  json_data = None
  global resultado_final # Declarar resultado_final como global
  with open(ruta_archivo, 'rb') as archivo:
    contenido_binario = archivo.read()
    tiene_bom = contenido_binario.startswith(b'\xef\xbb\xbf')
    if tiene_bom:
      contenido_decodificado = contenido_binario[3:].decode('utf-8')
    else:
      try:
        contenido_decodificado = contenido_binario.decode('utf-8')
      except UnicodeDecodeError:
        try:
          contenido_decodificado = contenido_binario.decode('latin-1')
        except UnicodeDecodeError as e:
          #print("Error de decodificación:", e)
          #resultado_final += "Error de decodificación:" + str(e)
          print_json( "Error_decodificacion", str(e)+"solicitar nuevamente el archivo", None, None, None)
          return None
    try:
      json_data = json.loads(contenido_decodificado)
    except JSONDecodeError as e:
      # Se captura la excepción JSONDecodeError
      print_json( "ERROR_JSON", "El archivo no tiene un formato JSON válido", None, None, str(e))
      return None
    return json_data
      
def obtener_fechades(correo):
  global resultado_final # Declarar resultado_final como global
  fecha_correo = correo.get("Date", None)
  if fecha_correo:
    fecha_correo_dt = parsedate(fecha_correo)
    if fecha_correo_dt:
      fecha_correo_str = datetime(*fecha_correo_dt[:6])
      return fecha_correo_str

  # Intentar obtener la fecha desde la cabecera "Received"
  received_headers = correo.get_all("Received")
  if received_headers:
    last_received = received_headers[-1]
    fecha_received = re.search(r"\d{1,2}\s\w+\s\d{4}\s\d{2}:\d{2}:\d{2}", last_received)
    if fecha_received is not None:
      fecha_received_dt = datetime.strptime(fecha_received.group(), "%d %b %Y %H:%M:%S")
      return fecha_received_dt

  # Si no se pudo obtener la fecha de las cabeceras, intentar obtener la fecha de creación del archivo
  ruta_archivo = correo.get("Archivo", None) # Reemplaza "Archivo" con la clave real si existe
  if ruta_archivo and os.path.exists(ruta_archivo):
    fecha_creacion_archivo = datetime.fromtimestamp(os.path.getctime(ruta_archivo))
    return fecha_creacion_archivo
    
  # Si no se pudo obtener ninguna fecha, devuelve None
  return None

def imprimir_respuesta_archivo(response):
  if len(response.text) > 100:
    # Generar un nombre aleatorio para el archivo
    nombre_archivo = ''.join(random.choice(string.ascii_lowercase) for _ in range(10))

    # Obtener la ruta del directorio de trabajo actual
    ruta_actual = os.getcwd()

    # Combinar la ruta del directorio de trabajo actual con el nombre del archivo
    ruta_archivo = os.path.join(ruta_actual, 'errores_scaner_dte', nombre_archivo + '.html')

    # Abrir el archivo en modo de escritura
    with open(ruta_archivo, 'w') as archivo_local:
      # Escribir el texto de la respuesta en el archivo
      archivo_local.write(response.text)

def print_json(codigo=None, titulo=None, direccion_origen=None, fecha=None, error=None):
    # Crear un diccionario para almacenar los datos
    data = {}
    # Agregar los parámetros no nulos al diccionario
    if codigo is not None:
        data["codigo"] = codigo
    if titulo is not None:
        data["titulo"] = titulo
    if fecha is not None:
        # Comprobar si fecha es una instancia de datetime para convertirla a string
        if isinstance(fecha, datetime):
            data["fecha"] = fecha.strftime("%Y-%m-%d %H:%M:%S")
        else:
            # Si fecha no es una instancia de datetime, se asume que ya está en formato string adecuado
            data["fecha"] = fecha
    if error is not None:
        data["error"] = str(error)  # Convertir la excepción a string
    if direccion_origen is not None:
        data["direccion_origen"] = direccion_origen
    # Imprimir el diccionario como JSON
    print(json.dumps(data, indent=4), ',')


def procesar_correo(correo, fecha_correo_str, message_id):
  global CONTADOR_JSON
  global resultado_final # Declarar resultado_final como global fecha_correo_str):
  hora_act = obtener_hora_actual()
  paso = ''
  direccion_origen = obtener_direccion_origen(correo)
  # Verificar si el correo es válido y fue recibido en las últimas 2 horas
  fecha_correo = parsedate(fecha_correo_str)
  correos_permitidos = [
    'facturacionelectronica@redserfinsa.com',  
    'facturaelectronica@promerica.com.sv',
    'facturaelectronica@davivienda.com.sv',
    'jaime.gvperez@gmail.com',
    'facturadigital@redserfinsa.com'
  ]

  #if direccion_origen in correos_permitidos: #datetime(*fecha_correo[:6]) >= dos_horas_atras: #True: # datetime(*fecha_correo[:6]) >= dos_horas_atras:
  if True: #datetime(*fecha_correo[:6]) >= dos_horas_atras: #True: # datetime(*fecha_correo[:6]) >= dos_horas_atras:
    time.sleep(0.005)
    #print('2__')
    # Extraer archivos adjuntos
    archivos_pdf = []
    archivos_json = []
    # Recorriendo archivos adjuntos
    for parte in correo.walk():
      content_disposition = parte.get("Content-Disposition")
      content_type = parte.get_content_type()
      
      fecha_correo = obtener_fecha(correo)
      if content_disposition and content_disposition.startswith('attachment'):
        #print('3___')
        nombre_archivo = ''
        try:
          filename = parte.get_filename()
          if( filename is None):
            print_json( 'ERROR_NO_FILE_NAME','No se logró identificar el nombre del archivo', direccion_origen, None, '')
            filename = 'no_file'
          #print('--------------------------> filename:', filename )
          # Secuencias de escape Unicode de las letras tildadas vocales
          secuencias_unicode = ['\xe1', '\xe9', '\xed', '\xf3', '\xfa', '\xc1', '\xc9', '\xcd', '\xd3', '\xda']
          # Mapeo de secuencias Unicode a letras con tilde correspondientes
          mapeo = {'\xe1': 'á','\xe9': 'é','\xed': 'í','\xf3': 'ó','\xfa': 'ú','\xc1': 'Á','\xc9': 'É','\xcd': 'Í','\xd3': 'Ó','\xda': 'Ú'}
          # Función para reemplazar secuencias Unicode por letras con tilde

          def reemplazar_secuencias(texto):
            for secuencia in secuencias_unicode:
              if secuencia in texto:
                texto = texto.replace(secuencia, mapeo[secuencia])
            return texto
          # Llamar a la función para reemplazar las secuencias
          filename = reemplazar_secuencias(filename)
          nombre_archivo = decode_header(filename) #-----------------------
        except UnicodeDecodeError:
          print_json( 'ERROR_CODIFICACION','BUSCAR EL ARCHIVO Y REVISARLO MANUALMENTE, si no abre solicitar al emisor nuevamente', direccion_origen, None, '')
          return None
          continue # Intentar con la siguiente codificación si falla
        #print('==============================================>>>> encoding:', encoding)

        for encoding in ['utf-8', 'latin-1']:
          try:
            nombre_decodificado = nombre_archivo[0][0] #.decode(encoding)
            break # Salir del bucle si la decodificación fue exitosa
          except UnicodeDecodeError:
            nombre_decodificado = nombre_archivo[0][0].decode(encoding)
            continue # Intentar con la siguiente codificación si falla
        if nombre_archivo == "":
          print_json( 'ERROR_EN_NOMBRE_DE_ARCHIVO','No se pudo analizar el nombre del archivo', direccion_origen, None, '')
          return None

        if type(nombre_decodificado) is bytes:
          try:
            nombre_decodificado = nombre_decodificado.decode("utf-8")
          except UnicodeDecodeError:
            nombre_decodificado = nombre_decodificado.decode("ISO-8859-1")
            continue # Intentar con la siguiente codificación si falla
            
      # si el tipo de archivo es pdf o json, mover a la carpeta
        if nombre_decodificado.lower().endswith('.pdf'):
          nombre_decodificado_ = nombre_decodificado[1:]
          name_file = nombre_decodificado_+hora_act+'.pdf'
          archivos_pdf.append(name_file)
          print_json( 'INFO_PDF',APP_URL+'/PROD_files/'+name_file, direccion_origen, fecha_correo, '' )
          ruta_archivo = os.path.join(directorio_adjuntos, name_file)
          with open(ruta_archivo, 'wb') as archivoPdf:
            archivoPdf.write(parte.get_payload(decode=True))  
        elif nombre_decodificado.lower().endswith('.json'):
          nombre_decodificado_ = nombre_decodificado[1:]
          name_file = nombre_decodificado_ + hora_act + '.json'
          CONTADOR_JSON = CONTADOR_JSON +1
          archivos_json.append(name_file)
          print_json( 'INFO_JSON',APP_URL+'/PROD_files/'+name_file, direccion_origen, fecha_correo, '' )
          ruta_archivo = os.path.join(directorio_adjuntos, name_file)
          with open(ruta_archivo, 'wb') as archivo:
            archivo.write(parte.get_payload(decode=True))
        elif nombre_decodificado.lower().endswith('.txt'):
          nombre_decodificado_ = nombre_decodificado[1:]
          name_file = nombre_decodificado_ + hora_act + '.json'
          archivos_json.append(name_file)
          print_json( 'INFO_JSON',APP_URL+'/PROD_files/'+name_file, direccion_origen, fecha_correo, '' )
          ruta_archivo = os.path.join(directorio_adjuntos, name_file)
          with open(ruta_archivo, 'wb') as archivo:
            archivo.write(parte.get_payload(decode=True))
        else:
          nombre_decodificado_ = nombre_decodificado[1:]
          name_file = hora_act+'desconocido'+ nombre_decodificado_
          extension_alterna = ''
          if( content_type == 'application/pdf'): 
            extension_alterna = '.pdf'
            name_file = name_file +extension_alterna
            archivos_pdf.append(name_file)
             
          elif(content_type == 'application/json'):
            extension_alterna = '.json'
            name_file = name_file +extension_alterna
            archivos_json.append(name_file)
            
          else:
            print_json( 'ARCHIVO_DESCONOCIDO',APP_URL+'/PROD_files/'+name_file+extension_alterna, direccion_origen, fecha_correo, '' )
          
          
          if(extension_alterna != ''):
            print_json( 'INFO_FILE',APP_URL+'/PROD_files/'+name_file, direccion_origen, fecha_correo, '' )
            ruta_archivo = os.path.join(directorio_adjuntos, name_file)
            with open(ruta_archivo, 'wb') as archivo:
              archivo.write(parte.get_payload(decode=True))
            
          ruta_archivo = os.path.join(directorio_adjuntos, name_file)
          with open(ruta_archivo, 'wb') as archivo:
            try:
              payload = parte.get_payload(decode=True)
              if payload is not None:
                archivo.write(payload)
            except Exception as e:
              # Handle decoding error or other exceptions
              print_json( 'ARCHIVO_CORRUPTO',APP_URL+'/PROD_files/'+name_file, direccion_origen, fecha_correo, e )
              
    multples_documentos = False
    if(len(archivos_pdf) >1 or len(archivos_json)>1):
      multples_documentos = True

    if(len(archivos_pdf) ==0 and len(archivos_pdf) >0 ):
      print_json( 'SIN_PDF','correo sin archivos pdf', direccion_origen, fecha_correo, 'correo sin PDF' )

    if(len(archivos_json) > 1 ):
      print_json( 'archivos JSON','4.0', direccion_origen, fecha_correo, 'ERROR: muchos archivos JSON NO SE ADJUNTARA EL PDF' )

    # Procesar los archivos JSON
    codigo_generacion = ''
    for archivo_json in archivos_json:
      ruta_json = os.path.join(directorio_adjuntos, archivo_json)
      with open(ruta_json, 'rb') as archivo:
        # Detectar la codificación del contenido
        resultado_det = chardet.detect(archivo.read())
        codificacion_esperada = resultado_det['encoding'] or 'ascii'
        archivo.seek(0)
        contenido_binario = archivo.read()
        tiene_bom = contenido_binario.startswith(b'\xef\xbb\xbf')
        paso = '1_'      
        try:
          datos_json = cargar_json_desde_archivo(ruta_json)
          codigo_generacion, nombre_comercial, nit, telefono, total_pagar, fecha_emision, tributos, sello, tipo_dte, iva_percibido, valor_operaciones, monto_sujeto_percepcion, numero_control, nit_receptor = extraer_datos_json(datos_json)

            
          if codigo_generacion not in codigos_generacion and (nit_receptor == NIT or tipo_dte== '14' ) : #True: #direccion_origen in correos_permitidos: #
            codigos_generacion.append(codigo_generacion)  
            json_obj = tributos
            if iva_percibido == 0 and tributos is not None :
              for item in json_obj:
                if item['codigo'] == '20':
                  iva_percibido = item['valor']
            paso= paso + '4_'    
            pdf_name = ''    
            if multples_documentos:
              pdf_name = '-'    
            elif len(archivos_pdf) == 1:
              pdf_name = archivos_pdf[0]  
            else:
              '-'

            payload = {
              'pdf': pdf_name,
              'json': archivo_json,
              'codigo_generacion': codigo_generacion if codigo_generacion is not None else '-',
              'nombre_comercial': nombre_comercial if nombre_comercial is not None else '-',
              'nit': nit if nit is not None else '-',
              'telefono': telefono if telefono is not None else '-',
              'total_pagar': total_pagar if total_pagar is not None else 0,
              'fecha_emision': fecha_emision if fecha_emision is not None else '',
              'fecha_correo': fecha_correo.strftime('%Y-%m-%d %H:%M:%S') if fecha_correo is not None else None,
              'direccion_origen': limpiar_texto(direccion_origen) if direccion_origen is not None else None,
              'tributos': json.dumps(tributos),
              'sello': sello,
              'tipo_dte': tipo_dte,
              'message_id': message_id,
              'iva_percibido': iva_percibido, #ivaPercibido
              'valor_operaciones': valor_operaciones, #valorOperaciones
              'monto_sujeto_percepcion': monto_sujeto_percepcion, #montoSujetoPercepcion
              'numero_control': numero_control,
              'nit_receptor': nit_receptor
            }
      
            #print('4.1.35__')nit in ('06142810921030') and "fechaEmision" BETWEEN '2024-01-01' and '2024-01-31'
            #json_str = json.dumps(payload, indent=2)
            paso= paso + '5_'    
            #print('4.1.36__')
            response = None
            # Imprime la cadena JSON
            if( True ): #nit in ['05110402951018', '06141709940015', '06142810921030']):
              response = requests.post(url_solicitud_post, params=payload)
              
            else:
              response = 'de otras'
            #imprimir_respuesta_archivo(response)
          elif( nit_receptor != NIT and tipo_dte !='14' and tipo_dte !='01' ):
            print_json( 'REVISAR_EMPRESA_EQUIVOCADA_O_INVALIDACION','CONTACTAR CON LA EMPRESA PARA QUE ENVIE AL CORREO CORRECTO NIT RECEPTOR:' + str(nit_receptor), codigo_generacion, codigo_generacion, None)
          elif( nit_receptor != None and  tipo_dte =='01' ):
            print_json( 'ERROR_EMPRESA_INDEFINIDA','FACTURA SIN DATOS DE RECEPTOR, MANEJAR DE MANERA INTERNA' + str(nit_receptor), codigo_generacion, None, None)
          else:
           mensaje = 'codigo repetido se omitira en el reporte'
            #print_json( 'codigo repetido','50.3', codigo_generacion, None, None)
              
        except Exception as e:
          import traceback
          print("Ocurrió un error:")
          traceback.print_exc()
          try:
            if isinstance(fecha_correo, tuple):
              print_json('ERROR_indefinido1','algo anda mal', codigo_generacion, '', str(e))
            else:
              print_json( 'ERROR_indefinido2','algo anda mal', codigo_generacion, '', str(e))
          except Exception as e:
            print_json( 'ERROR_indefinido3','algo anda mal', direccion_origen, None, str(e))
        

# PROCESO GENERAL

print('[')

response = requests.get(url_lista_procesados)
if response.status_code == 200:
  data = json.loads(response.text)
  message_ids = data.get('messageIds', [])
  codigos_generacion = data.get('codigos_generacion', [])

else:
  print(f'Error en la solicitud: {response.status_code}')
  print(response.text)

# Crear una lista de tuplas (ruta_correo, fecha_modificacion)
archivos_fechas = [(os.path.join(directorio_correos, archivo), os.path.getmtime(os.path.join(directorio_correos, archivo))) for archivo in os.listdir(directorio_correos) if os.path.isfile(os.path.join(directorio_correos, archivo))]

# Ordenar la lista de tuplas por fecha de modificación en orden descendente
archivos_ordenados = sorted(archivos_fechas, key=lambda x: x[1], reverse=True)

# Recorrer los archivos de correo electrónico en el directorio
for archivo_correo, _ in archivos_ordenados:
  ruta_correo = os.path.join(directorio_correos, archivo_correo)
  # Verificar si el archivo es un archivo regular (no un directorio)
  contador_serfinsa = 0
  if os.path.isfile(ruta_correo):
    try:
      with open(ruta_correo, 'rb') as archivo:
        mensaje = email.message_from_binary_file(archivo) # Utilizar message_from_file en Python 2
        direccion_origen = obtener_direccion_origen(mensaje)
          
        titulo_correo, encoding = decode_header(mensaje.get("Subject", "Sin título"))[0]
        if titulo_correo is None:
          titulo_correo = "titulo NO IDENTIFICADO"
        fecha_cor = mensaje.get("Date", None)
          
        if fecha_cor:
          fecha_correo_dt = parsedate(fecha_cor)
          fecha_correo_str = datetime(*fecha_correo_dt[:6]).strftime('%a, %d %b %Y %H:%M:%S %z')
        else:
          fecha_correo_str = "Fecha desconocida"
          fecha_dt = obtener_fechades(mensaje)
          fecha_correo_str = fecha_dt.strftime('%a, %d %b %Y %H:%M:%S %z') if fecha_dt is not None else fecha_dt

        # Modifica esta parte para manejar el título como bytes y decodificarlo
        if isinstance(titulo_correo, bytes):
          try:
            titulo_correo = titulo_correo.decode('utf-8', 'ignore')
          except UnicodeDecodeError:
            try:
              titulo_correo = titulo_correo.decode('latin-1', 'ignore')
            except UnicodeDecodeError as e:
              print_json( 'decodificación','0.1', direccion_origen, fecha_correo_dt, e )
              #print("Error de decodificación:", e)
              continue
        titulo_correo = titulo_correo.strip()
        titulo_correo = titulo_correo.replace("\n", "")
        titulo_correo = titulo_correo.replace("\t", "")
        titulo_correo = titulo_correo.replace("\r", "")

        #print('_________________________________________________________________________________________________________________')
        #print('fecha:', fecha_correo_str, 'titulo:', titulo_correo)
        # Procesar el correo
        message_id = mensaje.get("Message-ID", '-')
        correos_permitidos = [
          'facturacionelectronica@redserfinsa.com',  
          'facturaelectronica@promerica.com.sv',
          'facturaelectronica@davivienda.com.sv',
          'jaime.gvperez@gmail.com',
          'facturadigital@redserfinsa.com'
        ]
          
        #if direccion_origen in correos_permitidos:
        if True:
          contador_serfinsa = contador_serfinsa + 1
        if message_id not in message_ids: #True: #direccion_origen in correos_permitidos: #
          message_ids.append(message_id)  
          print('{ "origen": "', direccion_origen, '", "fecha": "', fecha_correo_dt, '", "titulo":"', titulo_correo, '" , "files": [')
          if fecha_correo_dt[0] > 2022:
            CONTADOR_CORREO = CONTADOR_CORREO + 1
            if(fecha_correo_str is not None):
              procesar_correo(mensaje, fecha_correo_str, message_id)
            else:
              procesar_correo(mensaje, fecha_correo_dt, message_id)
          else:
            print_json( 'Advertencia','6.0', direccion_origen, None, "correo antiguo")
          print('{"fin":"fin"}]},')
          CONTADOR = CONTADOR +1
          time.sleep(0.001)
        else:
          vddddd = 0
          #print("-----> Este correo ya fue procesado")

    except IOError as e:
      #print("******Error de acceso===================================================================================================="+ str(e))
      print_json( 'decodificación','0.1', direccion_origen, fecha_correo_dt, e )
      continue

print('{"origen": "fin de proceso","fecha": "","titulo": "json:'+str(CONTADOR_JSON)+', CORREOS:'+str(CONTADOR_CORREO)+' ","files":[]}')

print(']')

'''
now = datetime.now()
timestamp = now.strftime("%Y_%m_%d_%H")
# Nombre del archivo de destino
nombre_archivo_destino = f"src/public/BITACORA_JSON/{timestamp}.json"
# Copiar el archivo results.txt a la carpeta de destino con el nuevo nombre
shutil.copy("results.txt", nombre_archivo_destino)
'''




