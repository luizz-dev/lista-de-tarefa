<?php
$server = "localhost";
$usuario = "root";
$senha ="";
$banco = "lista_tarefa";

try{
    $pdo = new PDO("mysql:host=$server;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // echo "banco conectado com sucesso!";
}catch(Exception $e){
    echo "Erro: ".$e->getMessage();
}
?>