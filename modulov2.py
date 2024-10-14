# -*- coding: utf-8 -*-
import os
import email
from email.header import decode_header
from email.utils import parsedate
from datetime import datetime, timedelta
import json  # Agregar esta línea para importar el módulo json
import requests
import re
import time
import chardet
import random
import string
import asyncio  # Agregar esta línea para importar el módulo asyncio

# Ruta del directorio donde se encuentran los archivos de correo electrónico
directorio_correos = '/home/server/Documents/manejador_correos/mails_bomba/cur'
# Ruta del directorio donde se guardarán los archivos adjuntos
directorio_adjuntos = '/home/server/Documents/manejador_correos/Docker_lego/src/public/files_dte'
# URL de la solicitud POST
url_solicitud_post = 'http://localhost:8000/api/summary2/factura_electronica' #'https://almacenesbomba.com/summary2/api/factura_electronica'
url_lista_procesados = 'http://localhost:8000/api/summary2/get_list_all'
ahora = datetime.now() #dos_horas_atras = ahora - timedelta(hours=16000)
resultado_final = u''
CONTADOR = 0

def limpiar_texto(texto):
    global resultado_final  # Declarar resultado_final como global
    # Eliminar caracteres no alfanuméricos y etiquetas HTML
    limpio = re.sub(r'<.*?>|[^a-zA-Z0-9\s]', '', texto)
    return limpio.strip() 
        
def obtener_fecha(correo):
    global resultado_final  # Declarar resultado_final como global
    fecha_correo = correo.get("Date", None)
    if fecha_correo:
        fecha_correo_dt = parsedate(fecha_correo)
        if fecha_correo_dt:
            fecha_correo_str = datetime(*fecha_correo_dt[:6])
            return fecha_correo_str
    return None

def obtener_direccion_origen(correo):
    global resultado_final  # Declarar resultado_final como global
    direccion_origen = correo.get("From", None)
    return direccion_origen

def obtener_hora_actual():
    global resultado_final  # Declarar resultado_final como global = datetime.now()
    hora_minuto_segundo = '_'.join([str(ahora.hour), str(ahora.minute), str(ahora.second)])
    return hora_minuto_segundo

def extraer_datos_json(datos):
    global resultado_final  # Declarar resultado_final como global
    # Extraer los datos del JSON
    try:
        print('4.1.1__')
        section_identificacion = datos.get('identificacion', {})
        section_emisor = datos.get('emisor', {})
        datos_orig = datos 
        if section_identificacion == {} and section_emisor == {}:
            datos = datos.get('dteJson', {})
            section_identificacion = datos.get('identificacion', {})
            section_emisor = datos.get('emisor', {})
        
        print('4.1.2__')
        section_resumen = datos.get('resumen', {})
        codigo_generacion = section_identificacion.get('codigoGeneracion', 
                                datos_orig.get('codigoGeneracion', 
                                    datos_orig.get('responseMH', {}).get('codigoGeneracion', '-')
                                )
                            )

        sello = datos.get('respuestaHacienda',{}
                        ).get('selloRecibido', 
                        datos_orig.get('selloRecibido', 
                            datos_orig.get('responseMH', 
                            {}).get('selloRecibido', '-') 
                        )
                    )

        nombre_comercial = section_emisor.get('nombreComercial', section_emisor.get('nombre', '-'))
        nit = section_emisor.get('nit', '-')
        telefono = section_emisor.get('telefono', '-')
        total_pagar = section_resumen.get('totalPagar', 0 )
        fecha_emision = section_identificacion.get('fecEmi', None)
        tributos = section_resumen.get('tributos', {})
        
        
        sello = sello if sello != "-" else datos.get('sello', '-')
        tipoDte = section_identificacion.get('tipoDte', '-')
        print('4.1.3__')
      
        if nombre_comercial == "-":
            section_emisor.get('nombre', '-')
        
            
        return codigo_generacion, nombre_comercial, nit, telefono, total_pagar, fecha_emision, tributos, sello tipoDte
    except Exception as e:
        print("==================================== ============================== ==================== =========>>>Error al extraer datos del JSON:", e)
        resultado_final += "Error al extraer datos del JSON:"+ str(e)
        return '-', '-', '-', '-', '-'

def limpiar_contenido_json(contenido_json):
    global resultado_final  # Declarar resultado_final como global
    # Reemplazar cualquier salto de línea y otros caracteres no deseados con una cadena vacía
    contenido_limpio = contenido_json.replace('\n', '').replace('\r', '').replace('\t', '').strip()
    return contenido_limpio

def cargar_json_desde_archivo(ruta_archivo):
    global resultado_final  # Declarar resultado_final como global
    with open(ruta_archivo, 'rb') as archivo:
        contenido_binario = archivo.read()
        tiene_bom = contenido_binario.startswith(b'\xef\xbb\xbf')
        if tiene_bom:
            # Si tiene BOM, eliminar los primeros 3 bytes y decodificar el contenido
            contenido_decodificado = contenido_binario[3:].decode('utf-8')
            return json.loads(contenido_decodificado)
        else:
            # Si no tiene BOM, decodificar el contenido directamente
            try:
                contenido_decodificado = contenido_binario.decode('utf-8')
            except UnicodeDecodeError:
                try:
                    contenido_decodificado = contenido_binario.decode('latin-1')
                except UnicodeDecodeError as e:
                    #print("Error de decodificación:", e)
                    resultado_final += "Error de decodificación:" + str(e)
                    return None
            return json.loads(contenido_decodificado)

def obtener_fechades(correo):
    global resultado_final  # Declarar resultado_final como global
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
        if fecha_received:
            fecha_received_dt = datetime.strptime(fecha_received.group(), "%d %b %Y %H:%M:%S")
            return fecha_received_dt

    # Si no se pudo obtener la fecha de las cabeceras, intentar obtener la fecha de creación del archivo
    ruta_archivo = correo.get("Archivo", None)  # Reemplaza "Archivo" con la clave real si existe
    if ruta_archivo and os.path.exists(ruta_archivo):
        fecha_creacion_archivo = datetime.fromtimestamp(os.path.getctime(ruta_archivo))
        return fecha_creacion_archivo

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

def procesar_correo(correo, fecha_correo_str, message_id):
    global resultado_final  # Declarar resultado_final como global fecha_correo_str):
    paso = '1_'
    hora_act = obtener_hora_actual()
    print(paso)
    resultado_final += paso
    # Verificar si el correo es válido y fue recibido en las últimas 2 horas
    fecha_correo = parsedate(fecha_correo_str)
    if True: #datetime(*fecha_correo[:6]) >= dos_horas_atras: #True: # datetime(*fecha_correo[:6]) >= dos_horas_atras:
        time.sleep(0.3)
        print('2__')
        resultado_final += '2__'
        # Extraer archivos adjuntos
        archivos_pdf = []
        archivos_json = []
        for parte in correo.walk():
            content_disposition = parte.get("Content-Disposition")
            if content_disposition and content_disposition.startswith('attachment'):
                print('3___')
                resultado_final += '3__'
                nombre_archivo = ''
                try:
                    filename = parte.get_filename()
                    print('--------------------------> filename:', filename )
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
                    print('*** - Error en la metadata del archivo adjunto, crear manualmente')
                    return None
                    continue  # Intentar con la siguiente codificación si falla
                #print('==============================================>>>> encoding:', encoding)

                for encoding in ['utf-8', 'latin-1']:
                    try:
                        nombre_decodificado = nombre_archivo[0][0] #.decode(encoding)
                        break  # Salir del bucle si la decodificación fue exitosa
                    except UnicodeDecodeError:
                        nombre_decodificado = nombre_archivo[0][0].decode(encoding)
                        continue  # Intentar con la siguiente codificación si falla
                if nombre_archivo == "":
                    print('*** - No se pudo analizar el nombre del archivo')
                    return None
                print('--------------------------> nombre_decodificado:', nombre_decodificado)

                
                if type(nombre_decodificado) is bytes:
                    try:
                        nombre_decodificado = nombre_decodificado.decode("utf-8")
                    except UnicodeDecodeError:
                        print('*** - Error intentando otra forma')
                        nombre_decodificado = nombre_decodificado.decode("ISO-8859-1")
                        continue  # Intentar con la siguiente codificación si falla
                
                if nombre_decodificado.lower().endswith('.pdf'):
                    # Guardar el archivo PDF en el directorio de adjuntos
                    nombre_decodificado_ = nombre_decodificado[1:]
                    archivos_pdf.append(nombre_decodificado_+hora_act+'.pdf')
                    ruta_archivo = os.path.join(directorio_adjuntos, nombre_decodificado_+hora_act+'.pdf')
                    print('--------------------------> nombre_dec:', nombre_decodificado_+hora_act+'.pdf')
                    
                    with open(ruta_archivo, 'wb') as archivoPdf:
                        archivoPdf.write(parte.get_payload(decode=True))
                    print('-------------------------->pdf')    
                elif nombre_decodificado.lower().endswith('.json'):
                    # Guardar el archivo JSON en el directorio de adjuntos
                    nombre_decodificado_ = nombre_decodificado[1:]
                    archivos_json.append(nombre_decodificado_ + hora_act + '.json')
                    ruta_archivo = os.path.join(directorio_adjuntos, nombre_decodificado_ + hora_act + '.json')
                    print('-----> nombre_dec:', nombre_decodificado_ + hora_act + '.json')
                    with open(ruta_archivo, 'wb') as archivo:
                        archivo.write(parte.get_payload(decode=True))
                    print('-------------------------->json')
        # Procesar los archivos JSON
        for archivo_json in archivos_json:
            print('4____') 
            resultado_final += "\n" + '4____'
            ruta_json = os.path.join(directorio_adjuntos, archivo_json)
            with open(ruta_json, 'rb') as archivo:
                # Detectar la codificación del contenido
                resultado_det = chardet.detect(archivo.read())
                codificacion_esperada = resultado_det['encoding'] or 'ascii'
                print('Codificación esperada:', codificacion_esperada)

                # Volver al principio del archivo
                archivo.seek(0)

                contenido_binario = archivo.read()
                tiene_bom = contenido_binario.startswith(b'\xef\xbb\xbf')
                if tiene_bom:
                    # Si tiene BOM, eliminar los primeros 3 bytes y decodificar el contenido
                    contenido_decodificado = contenido_binario[3:].decode('utf-8')
                else:
                    # Si no tiene BOM, decodificar el contenido con la codificación detectada
                    try:
                        contenido_decodificado = contenido_binario.decode(codificacion_esperada)
                    except UnicodeDecodeError as e:
                        try:
                            contenido_decodificado = contenido_binario.decode('utf-8')
                            print('4.____INTENT DECODIFICACION' + str(e))
                        except UnicodeDecodeError as e:
                            try:
                                contenido_decodificado = contenido_binario.decode('ISO-8859-1')
                                print('4.____INTENT DECODIFICACION' + str(e))
                            except UnicodeDecodeError as e:
                                print('4.____ NO SE LOGRA DECODIFICAR' + str(e))
                                return None
                #Eliminar el BOM (3 primeros bytes) y decodificar el contenido con codificación UTF-8
                #contenido_decodificado = contenido_binario[3:].decode('utf-8')
                #contenido_limpio = limpiar_contenido_json(contenido_json)
                print('4.0____')
                try:
                    datos_json = cargar_json_desde_archivo(ruta_json)
                    print('4.1____')
                    print('datos json')
                    #print(datos_json) 
                    codigo_generacion, nombre_comercial, nit, telefono, total_pagar, fecha_emision, tributos, sello, tipo_dte = extraer_datos_json(datos_json)
                    #if codigo_generacion == "73A913FC-E464-4A8C-9D10-79B1FAC6FDBF":
                    fecha_correo = obtener_fecha(correo)
                    direccion_origen = obtener_direccion_origen(correo)
                    print('4.2____', message_id)
                    # Enviar solicitud GET con los datos extraídos
                    payload = {
                        'pdf': archivos_pdf[0] if archivos_pdf else '-',
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
                        'message_id': message_id
                    }
                    #print(tributos) 
                    print(sello) 
                    print(tipo_dte)
                    json_str = json.dumps(payload, indent=2)
                    # Imprime la cadena JSON
                    #print(json_str)
                    print('4.3____') 
                    response = requests.post(url_solicitud_post, params=payload)
                    imprimir_respuesta_archivo(response)
                    #print("5-------->response", response.text)
                    #with open("archivo_respuesta.txt", "w") as archivo_local:
                        #archivo_local.write(response.text.decode())
                    
                    print(response.text)
                      
                except Exception as e:
                    print("**************************************")
                    print("Error al procesar el archivo JSON:", e)
                    print("**************************************")
        print('5_____')

async def procesar_correos_async(message_ids):
    tasks = []

    for archivo_correo in os.listdir(directorio_correos):
        ruta_correo = os.path.join(directorio_correos, archivo_correo)

        if os.path.isfile(ruta_correo):
            try:
                with open(ruta_correo, 'rb') as archivo:
                    mensaje = email.message_from_binary_file(archivo)
                    title, encoding = decode_header(mensaje.get("Subject", "Sin título"))[0]

                    if isinstance(title, bytes):
                        try:
                            title = title.decode('utf-8', 'ignore')
                        except UnicodeDecodeError:
                            try:
                                title = title.decode('latin-1', 'ignore')
                            except UnicodeDecodeError as e:
                                print("**************************************")
                                print("Error de decodificación:", e)
                                print("**************************************")
                                resultado_final += "\n" + "Error de decodificación:" + str(e)
                                continue

                    fecha_correo = mensaje.get("Date", None)
                    if fecha_correo:
                        fecha_correo_dt = parsedate(fecha_correo)
                        fecha_correo_str = datetime(*fecha_correo_dt[:6]).strftime('%a, %d %b %Y %H:%M:%S %z')
                    else:
                        fecha_correo_str = "Fecha desconocida"
                        fecha_correo_str = obtener_fechades(mensaje).strftime('%a, %d %b %Y %H:%M:%S %z')

                    message_id = mensaje.get("Message-ID", '-')
                    if message_id not in message_ids:
                        tasks.append(procesar_correos_async( message_id))
                    else:
                        print("-----> Este correo ya fue procesado")

            except IOError as e:
                print("**************************************")
                print("Error de acceso:", e)
                print("**************************************")
                resultado_final += "\n" + 'Error de acceso:' + str(e)
                continue

    await asyncio.gather(*tasks)

# Obtener la lista de message_ids
response = requests.get(url_lista_procesados)
if response.status_code == 200:
    data = json.loads(response.text)
    message_ids = data.get('messageIds', [])
else:
    print(f'Error en la solicitud: {response.status_code}')
    print(response.text)

# Ejecutar el bucle de eventos de asyncio
asyncio.run(procesar_correos_async(message_ids))

print(resultado_final)
print(CONTADOR)
