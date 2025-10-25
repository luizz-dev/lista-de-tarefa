<?php
// Namespace conforme o psr-4 do composer.json
namespace Luizeduardo\ListaTarefa\Controller;

// Caminho de conexão corrigido (volta para a pasta src/)
require_once __DIR__ . '/../Database/Conexao.php';

class TarefaController {
    /** @var \Twig\Environment */
    private \Twig\Environment $ambiente;

    /** @var \Twig\Loader\FilesystemLoader */
    private \Twig\Loader\FilesystemLoader $carregador;

    public function __construct() {
        // Inicialização do Twig
        $this->carregador = new \Twig\Loader\FilesystemLoader("./src/View");
        $this->ambiente = new \Twig\Environment($this->carregador);
    }

    // Método para exibir a página de adicionar tarefas (GET /adicionar)
    public function adicionar(array $dados) {
        session_start();
        
        // Lógica de verificação de login (IMPORTANTE)
        if (!isset($_SESSION["idLogin"])) {
            $_SESSION["error_message"] = "Você precisa estar logado para acessar esta página.";
            // Usando a constante URL (definida no index.php)
            header("Location: " . URL . "/login");
            exit;
        }
        
        $dados["title"] = "Pagina Adicionar";
        $dados["pagina"] = "Pagina Adicionar";
        
        // Carrega e limpa mensagens de sucesso/erro após login/operação
        if (isset($_SESSION["success_message"])) {
            $dados["success_message"] = $_SESSION["success_message"];
            unset($_SESSION["success_message"]);
        }

        echo $this->ambiente->render("adicionar.html", $dados);
    }

    public function vizualizar(array $dados) {
        echo $this->ambiente->render("vizualizar.html", $dados);

    }
    // Futuros métodos: salvarTarefa(), listarTarefas(), etc.
}