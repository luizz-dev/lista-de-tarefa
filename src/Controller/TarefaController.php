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
    // NOVO: Método para lidar com as requisições AJAX/Fetch do JavaScript
    public function handleApi() {
        // Garante que o PHP retorne JSON e não HTML
        header('Content-Type: application/json');
        
        global $pdo; // Usando a conexão PDO do seu Conexao.php
        $method = $_SERVER['REQUEST_METHOD'];
        $data = json_decode(file_get_contents('php://input'), true);

        // Função de resposta JSON
        $enviarResposta = function($sucesso, $mensagem = '', $dados = null) {
            echo json_encode(['success' => $sucesso, 'message' => $mensagem, 'data' => $dados]);
            exit;
        };
        
        try {
            switch ($method) {
                case 'POST': // ADICIONAR TAREFA
                    $descricao = trim($data['descricao'] ?? '');
                    if (empty($descricao)) {
                        $enviarResposta(false, "Descrição não pode ser vazia.");
                    }
                    $sql = "INSERT INTO tarefas (descricao) VALUES (:descricao)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':descricao', $descricao);
                    $stmt->execute();
                    $enviarResposta(true, "Tarefa adicionada com sucesso!", ['id' => $pdo->lastInsertId(), 'descricao' => $descricao, 'feita' => 0]);
                    break;
                    
                case 'GET': // LISTAR TAREFAS
                    // Implementação básica: lista todas as tarefas
                    $sql = "SELECT * FROM tarefas ORDER BY criada_em DESC";
                    $stmt = $pdo->query($sql);
                    $tarefas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $enviarResposta(true, "Tarefas carregadas", $tarefas);
                    break;
                    
                case 'PUT': // MARCAR/DESMARCAR TAREFA
                    $id = intval($data['id'] ?? 0);
                    $feita = intval($data['feita'] ?? 0); 
                    $sql = "UPDATE tarefas SET feita = :feita WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':feita', $feita, \PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
                    $stmt->execute();
                    $enviarResposta(true, "Tarefa atualizada!");
                    break;
                    
                case 'DELETE': // EXCLUIR TAREFA
                    $id = intval($data['id'] ?? 0);
                    $sql = "DELETE FROM tarefas WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
                    $stmt->execute();
                    $enviarResposta(true, "Tarefa excluída!");
                    break;
                    
                default:
                    $enviarResposta(false, "Método não suportado.", null, 405);
            }
        } catch (\PDOException $e) {
            $enviarResposta(false, "Erro no Banco de Dados: " . $e->getMessage());
        }
    }
}