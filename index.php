<?php

require_once 'vendor/autoload.php';
require'src/Database/Conexao.php';

// Para ser acessível via 'global $URL', é melhor definir como variável ou usar DEFINE
define("URL", "http://localhost/lista_tarefa"); 
const URL_CONST = "http://localhost/lista_tarefa"; // Mantendo sua constante original

//criar o roteador para paginas
$roteador = new CoffeeCode\Router\Router(URL_CONST);

$roteador->namespace("Luizeduardo\ListaTarefa\Controller");

// Rotas
$roteador->group(null);

$roteador->get("/", "HomeController:home");

$roteador->get("/cadastro", "UsuarioController:cadastro");
$roteador->post("/cadastro", "UsuarioController:cadastrar");

$roteador->get("/login", "UsuarioController:login");
$roteador->post("/login", "UsuarioController:autenticar");


$roteador->get("/adicionar", "TarefaController:adicionar");
$roteador->get("/vizualizar", "TarefaController:vizualizar");

$roteador->dispatch();