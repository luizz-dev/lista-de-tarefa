<?php
// Namespace conforme o psr-4 do composer.json
namespace Luizeduardo\ListaTarefa\Controller;

// Caminho de conexão corrigido (volta para a pasta src/)
require_once __DIR__ . '/../Database/Conexao.php'; 

class HomeController {
    /** @var \Twig\Environment */
    private \Twig\Environment $ambiente;

    /** @var \Twig\Loader\FilesystemLoader */
    private \Twig\Loader\FilesystemLoader $carregador;

    public function __construct() {
        // Inicialização do Twig
        $this->carregador = new \Twig\Loader\FilesystemLoader("./src/View");
        $this->ambiente = new \Twig\Environment($this->carregador);
    }

    // Método para a página inicial (GET /)
    public function home(array $dados) {
        session_start();
        $dados["title"] = "Home - Lista de Tarefas";
        
        // Aqui você pode adicionar lógica para verificar se o usuário está logado
        // e, se sim, redirecionar para /adicionar, ou apenas renderizar a home.html
        
        // Exemplo:
        // if (isset($_SESSION["idLogin"])) {
        //     header("Location: " . URL . "/adicionar");
        //     exit;
        // }
        
        // Altere para a sua view inicial (pode ser index.html ou home.html)
        echo $this->ambiente->render("home.html", $dados); 
    }
}