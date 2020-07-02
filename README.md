"# FinalProject_Cloud"

-necessario per l'utilizzo:

php version 7.2

php library: cURL (cURL and microsoft Azure Storage service have problems, see this guide to resolve https://github.com/Azure/azure-storage-php/issues/70) php7.2-xml

mysql 

composer

comandi da eseguire dopo git clone, entrare nella cartella: composer install

-librerie python:

1)simple json: simplejson è un codificatore e decodificatore JSON < http://json.org > semplice, veloce, completo, corretto ed estensibile per Python 3.3+ con supporto legacy per Python 2.5+. È puro codice Python senza dipendenze, ma include un'estensione C opzionale per un notevole aumento di velocità.

guida: https://pypi.org/project/simplejson/#:~:text=simplejson%20is%20a%20simple%2C%20fast,for%20a%20serious%20speed%20boost.

2)mysql-connector-python: MySQL Connector / Python 8.0 è altamente raccomandato per l'uso con MySQL Server 8.0, 5.7 e 5.6. Effettua l'aggiornamento a MySQL Connector / Python 8.0.

guida: https://dev.mysql.com/doc/connector-python/en/

3)Pillow

guida: https://pillow.readthedocs.io/en/stable/

4)Requests: Requests è una libreria HTTP con licenza Apache2

guida: https://requests.readthedocs.io/projects/it/it/latest/

5)flask: routing delle api

guida: https://flask.palletsprojects.com/en/1.1.x/

6)uWSGI

guida: https://uwsgi-docs.readthedocs.io/en/latest/WSGIquickstart.html

Dopo queste librerie:

installa nginx

seguire questa guida per configurare il socket python con nginx: digitalocean.com/community/tutorials/how-to-serve-flask-applications-with-uswgi-and-nginx-on-ubuntu-18-04

sostituire wsgi.py della guida con quella che trovi dentro FinalProject_Cloud/server_configs/nginx_server_config

-------------------------------------------------------------------------------------------------

Database: 

eseguire query in: 
FinalProject_Cloud/backupDatabases/databaseSchema.sql

Inserire user e password database in:
script/Python_Api/DbManager.py
script/dbconn.php

Inserire connection string con api key di azure blob storage:
script/BlobStorageManager.php

Inserire key del Cognitive Service di azure e cambiare endpoint se diverso:
script/Python_Api/Route.py