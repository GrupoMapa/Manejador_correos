#!/bin/bash

# Obtener la fecha actual en el formato deseado (año-mes-día)
fecha=$(date "+%Y-%m-%d")

# Ejecutar el primer comando y redirigir la salida a un archivo de texto con el nombre de archivo basado en la fecha
#python3 /home/server/Documents/manejador_correos/MANEJADOR_IMPROCASA/src/mover_archivos_factura.py > /home/server/Documents/manejador_correos/MANEJADOR_IMPROCASA/src/public/BIT_INPROCASA/$fechacasa.json

# Ejecutar el segundo comando y redirigir la salida a un archivo de texto con el nombre de archivo basado en la fecha
#python3 /home/server/Documents/manejador_correos/MANEJADOR_IMPORTELAS/src/mover_archivos_factura.py > /home/server/Documents/manejador_correos/MANEJADOR_IMPORTELAS/src/public/BIT_IMPORTELAS/$fechatelas.json

# Ejecutar el tercer comando y redirigir la salida a un archivo JSON con el nombre de archivo basado en la fecha
#python3 /home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/mover_archivos_factura.py > /home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/public/BIT_PREMIUMCENTER/$fechaum.json
#python3 /home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/mover_archivos_factura.py > /home/server/Documents/manejador_correos/MANEJADOR_PREMIUM/src/public/BIT_PREMIUM/$fechaum.json

# Ejecutar el cuarto comando y redirigir la salida a un archivo de texto con el nombre de archivo basado en la fecha
python3 /home/server/Documents/manejador_correos/MANEJADOR_BOMBA/src/mover_archivos_factura.py > /home/server/Documents/manejador_correos/MANEJADOR_BOMBA/src/public/BIT_BOMBA/$fecha.json