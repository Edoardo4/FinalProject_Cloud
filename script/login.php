<?php

$userName = $_POST['nomeUtente_log'];
$pass = $_POST['pass_log'];

session_start(); // dive essere la prima cosa nella pagina, aprire la sessione
include("dbconn.php"); // includo il file di connessione al database
// Recupero i valori inseriti nel form

if (!$user || !$password ) {
    echo 'Tutti i campi del modulo sono obbligatori!';    
}
else {
    $sql = "SELECT * FROM registrazione WHERE nomeUtente = '$userName'";

    $sth = $pdo->prepare($sql);

    //$sth->bindValue(':user', $userName, PDO::PARAM_STR);
    //$sth->bindValue(':pass', $passhash, PDO::PARAM_STR);

    $sth->execute();

    $result = $sth->fetch();

    $hash = $result[2];
    $UserNameDb = $result[1];

    if (password_verify($pass, $hash) && $UserNameDb == $userName){
        header("Location: /webPage/homepage.php");
        $_SESSION['userName'] = $UserNameDb;
        
    }else{
        echo 'login errato, non è presente un account con queste credenziali';
        session_destroy();
    }
   
}
 /*$sth->bindValue(':nomeUtente',$user);
 $sth->bindValue(':pass', $password);

 $user = $_POST['email'];
   $pass = $_POST['password'];
   $sql = "SELECT email, password
   FROM utenti
   WHERE email = :mail";
   $sth = $pdo->prepare($sql);
   $sth->bindValue(':mail', $user, PDO::PARAM_STR);
   $sth->execute();
   $result = $sth->fetch(PDO::FETCH_ASSOC);
   if (password_verify ($pass, $result['password']))
   {
       $_SESSION['mail']=$user;
       header("location: ./home.php");
   }
   else
   {
       header("HTTP/1.1 400");
       header("location: ./index.php");
   }*/