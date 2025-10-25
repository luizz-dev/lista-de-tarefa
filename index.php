<?php
require_once 'vendor/autoload.php';
require'src/Database/Conexao.php';
const URL = "http://localhost/lista_tarefa";

//criar o roteador para paginas
$roteador = new CoffeeCode\Router\Router(URL);


$roteador->namespace("Luizeduardo\ListaTarefa\Controller");

//rotas

$roteador->group(null);
$roteador->get("/","Principal:home");

$roteador->get("/cadastro","Principal:cadastro");
$roteador->post("/cadastro","Principal:cadastrar");

$roteador->get("/login","Principal:login");
$roteador->post("/login","Principal:autenticar");

$roteador->get("/adicionar","Principal:adicionar");


$roteador->dispatch();