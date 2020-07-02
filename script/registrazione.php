<?php

$email = $_POST['email_reg'];
$username = $_POST['nomeUtente_reg'];
$pass = $_POST['pass_reg'];
$passhash=password_hash($pass,PASSWORD_DEFAULT);

session_start(); // dive essere la prima cosa nella pagina, aprire la sessione
include("dbconn.php"); // includo il file di connessione al database
// Recupero i valori inseriti nel form

if(!$email || !$username || !$pass){
    echo 'tutti i campi sono obbligatori';

    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo('email non valida');
    }
else {
    try{
        $sql = 'INSERT INTO registrazione (email, nomeUtente, pass) VALUE (:email , :username , :pass)';

        $sth = $pdo->prepare($sql);


        $sth->execute(array(
            'email' => $email,
            'username' => $username,
            'pass' => $passhash,

            ));
            header("Location: /webPage/homepage.php");
        }
    catch(Exception $e) {
            //echo 'Exception -> ';
            echo('è presente già un account con queste credienziali');
    }
}
//}
 // avvisa utente

// chiusura della connessione
//$pdo->close();


