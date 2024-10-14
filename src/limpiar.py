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
import psycopg2
from psycopg2 import sql
# Ruta del directorio donde se encuentran los archivos de correo electrónico
from psycopg2 import sql
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


dotenv.load_dotenv()

directorio_adjuntos = os.environ.get("DIRECTORIO_ADJUNTOS")
db_connection = os.environ.get("DB_CONNECTION")
db_host = os.environ.get("DB_HOST")
db_port = os.environ.get("DB_PORT")
db_database = os.environ.get("DB_DATABASE")
db_username = os.environ.get("DB_USERNAME")
db_password = os.environ.get("DB_PASSWORD")

def listar_y_eliminar_archivos(directorio):
    try:
        # Conectar a la base de datos PostgreSQL
        conexion = psycopg2.connect(
            dbname=os.environ.get("DB_DATABASE"),
            user=os.environ.get("DB_USERNAME"),
            password=os.environ.get("DB_PASSWORD"),
            host=os.environ.get("DB_HOST"),
            port=os.environ.get("DB_PORT")
        )
        cursor = conexion.cursor()

        # Listar los archivos en el directorio
        archivos = os.listdir(directorio)
        # Filtrar solo los archivos (excluir subdirectorios)
        archivos = [archivo for archivo in archivos if os.path.isfile(os.path.join(directorio, archivo))]

        for archivo in archivos:
            # Consultar si el archivo está en la base de datos
            cursor.execute(
                sql.SQL("SELECT 1 FROM factura_electronicas WHERE json = %s OR pdf = %s"),
                (archivo, archivo)
            )
            resultado = cursor.fetchone()
            if resultado:
                print(f"{archivo}: Está en la base de datos")
            else:
                # Eliminar el archivo si no está en la base de datos
                archivo_path = os.path.join(directorio, archivo)
                os.remove(archivo_path)
                print(f"{archivo}: NO está en la base de datos y ha sido eliminado")

        cursor.close()
        conexion.close()
    except Exception as e:
        print(f"Error al procesar archivos en el directorio {directorio}: {e}")

# Llamar a la función
listar_y_eliminar_archivos(directorio_adjuntos)