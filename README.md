# PHP-Ecommerce
Codice sorgente della serie YouTube "Creare un Ecommerce con PHP, MySQL e Bootstrap 4 da zero"

# Installazione:
1) copiare la cartella "php-ecommerce" nella root del webserver: 
nel caso si usa xampp mettere la cartella nel percorso: "C:\xampp\htdocs"

2) modificare il file "inc/init.php" impostando i valori per le costanti, quali url del sito, parametri di connessione al db eccetera.
(Nel caso si usa un ambiente locale i valori presenti dovrebbero essere gia ok).

3) in phpmyadmin creare un nuovo database e chiamarlo "phpecommerce", quindi aprire la tab "sql" e copiare il contenuto del file "phpecommerce-db-script.php" ed eseguire i comandi. 
La struttura di base e i dati di prova saranno così inseriti.

Saranno inseriti due utenti di partenza:

User: admin@email.com 
Password: password

User: regular@email.com 
Password: password

4) per far funzionare l'invio mail occorre intervenire sul file "php.ini" 
(cercare su google come inviare email da localhost con xampp) 
