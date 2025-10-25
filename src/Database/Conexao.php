<?php
$server = "localhost";
$usuario = "root";
$senha ="";
$banco = "listadetarefas";

try{
    $pdo = new PDO("mysql:host=$server;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
}catch(Exception $e){
    echo "Erro: ".$e->getMessage();
}
?>