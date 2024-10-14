#!/bin/bash

# Definir funciones para cada comando rsync
function rsync_command_1() {
    rsync -avz -e 'ssh -p 5427 -i /home/uceda/.ssh/id_mail_rsa' tiendaspr@hwsrv-452552.hostwindsdns.com:/home/tiendaspr/mail/tiendaspremiumcenter.com/disma/ /home/uceda/Documents/manejador_correos/mails_premium
}

function rsync_command_2() {
    rsync -avz -e 'ssh -p 5427' almacenesbomba@almacenesbomba.com:/home/almacenesbomba/mail/.almapa@almacenesbomba_com/ /home/uceda/Documents/manejador_correos/mails_bomba
}

function rsync_command_3() {
    rsync -avz -e 'ssh -p 5427 -i /home/uceda/.ssh/id_mail_rsa_inprocasa' inprocasa@inprocasa.com:/home/inprocasa/mail/inprocasa.com/fel /home/uceda/Documents/manejador_correos/mails_inprocasa
}

function rsync_command_4() {
    rsync -avz -e 'ssh -p 5427 -i /home/uceda/.ssh/id_mail_rsa_inportel' importelsv@importelsv.com:/home/importelsv/mail/importelsv.com/dte /home/uceda/Documents/manejador_correos/mails_importelas
}

# Lanzar los comandos en paralelo
rsync_command_1 &
rsync_command_2 &
rsync_command_3 &
rsync_command_4 &

# Esperar a que todos los comandos terminen
wait

echo "Todos los comandos han finalizado."
