# PHP-Ecommerce
Codice sorgente della serie YouTube "Creare un Ecommerce con PHP, MySQL e Bootstrap 4 da zero"

# Live Demo
https://php-ecommerce2.000webhostapp.com/

# Installazione:
1) copiare la cartella "php-ecommerce" nella root del webserver: 
nel caso si usa xampp mettere la cartella nel percorso: "C:\xampp\htdocs"

2) rinominare il file "inc/config-sample.php" in "config.php" impostando i valori per le costanti, quali url del sito, parametri di connessione al db eccetera.
(Nel caso si usa un ambiente locale i valori presenti dovrebbero essere gia ok).

3) in phpmyadmin creare un nuovo database e chiamarlo "php-ecommerce", quindi aprire la tab "sql" e copiare il contenuto del file "phpecommerce-db-script.php" ed eseguire i comandi. 
La struttura di base e i dati di prova saranno così inseriti.

Saranno inseriti due utenti di partenza:

User: admin@email.com 
Password: password

User: regular@email.com 
Password: password

4) installare le dipendenze di terze parti tramite "composer": in dettaglio eseguire i seguenti step

4.1) scaricare "Composer" dal sito getcomposer.org e installare il pacchetto cliccando sempre su "avanti".
4.2) verificare la corretta installazione aprendo un command prompt e digitando il comando "composer --version"
4.3) navigare la cartella del progetto in cui si trova il file "composer.json" (utilizzando il comando "cd" per spostarsi nel file system)
4.4) eseguire il comando "composer install" e verificare che venga creata la carella "vendor" nella sottocartella interna "php-ecommerce"

5) per utilizzare il pagamento con PayPal o Stripe occorre creare un account di test (sandbox) in particolare:

5.1) Paypal: collegarsi al sito https://developer.paypal.com/ e creare un account sandbox. Inserire poi le informazioni della app di test creata nel file "config.php" valorizzando le costanti PAYPAL_CLIENT_ID e PAYPAL_CLIENT_SECRET

5.2) Stripe: collegarsi al sito https://stripe.com/ e creare un account. Cliccare sul link "Scarica le tue chiavi API di test", quindi inserire le informazioni  nel file "config.php" valorizzando le costanti STRIPE_PUBLISHABLE_KEY e STRIPE_SECRET_KEY

NOTA: per far funzionare l'invio mail occorre intervenire sul file "php.ini" 
(cercare su google come inviare email da localhost con xampp) 

5) aprire l'url locale del sito, ad esempio http://localhost/php-ecommerce e il sito funzionerà 

