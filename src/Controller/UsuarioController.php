<?php
// Namespace conforme o psr-4 do composer.json
namespace Luizeduardo\ListaTarefa\Controller;

// Caminho de conexão corrigido para o nível acima
require_once __DIR__ . '/../Database/Conexao.php';

class UsuarioController {
    /** @var \Twig\Environment */
    private \Twig\Environment $ambiente;

    /** @var \Twig\Loader\FilesystemLoader */
    private \Twig\Loader\FilesystemLoader $carregador;

    public function __construct(){
        // Inicialização do Twig 
        $this->carregador = new \Twig\Loader\FilesystemLoader("./src/View");
        $this->ambiente = new \Twig\Environment($this->carregador);
    }

    // Método para exibir o formulário de Cadastro (GET /cadastro)
    public function cadastro(array $dados) {
        session_start();
        $dados["title"] = "Cadastro";
        
        // Carrega e limpa mensagens de sessão (erro/sucesso)
        if (isset($_SESSION["error_message"])) {
            $dados["error_message"] = $_SESSION["error_message"];
            unset($_SESSION["error_message"]);
        }
        if (isset($_SESSION["success_message"])) {
            $dados["success_message"] = $_SESSION["success_message"];
            unset($_SESSION["success_message"]);
        }

        echo $this->ambiente->render("cadastro.html", $dados);
    }

    // Método para processar o Cadastro (POST /cadastro)
    public function cadastrar(array $dados) {
        global $pdo; 
        // REMOVIDO: global $URL; // Não precisamos disso se usarmos a constante URL
        session_start();

        // Receber e limpar dados do formulário
        $nome = trim($dados["nome"] ?? null);
        $email = trim($dados["email"] ?? null);
        $senha_pura = $dados["senha"] ?? null;

        // Validação de campos obrigatórios
        if (empty($nome) || empty($email) || empty($senha_pura)) {
            $_SESSION["error_message"] = "Todos os campos são obrigatórios";
            header("Location: " . URL . "/cadastro"); // USANDO A CONSTANTE URL
            exit;
        }

        // Verifica se o email ja existe 
        try {
            $sql_check = "SELECT idCli FROM cliente WHERE email = :email";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $_SESSION["error_message"] = "O email ja esta sendo utilizado";
                header("Location: " . URL . "/cadastro"); // USANDO A CONSTANTE URL
                exit;
            }
        } catch (\PDOException $e) {
            $_SESSION["error_message"] = "Erro interno na verificação de email. Tente novamente.";
            header("Location: " . URL . "/cadastro"); // USANDO A CONSTANTE URL
            exit;
        }

        // Criar Hash da Senha 
        $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);
        
        try {
            // Preparar e Executar a Inserção no Banco
            $sql_insert = "INSERT INTO cliente (email, senha, nome) VALUES (:email, :senha, :nome)";
            $stmt = $pdo->prepare($sql_insert);
            
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':nome', $nome);
            
            $stmt->execute();
            
            $_SESSION["success_message"] = "Cadastro realizado com sucesso! Faça login.";
            header("Location: " . URL . "/login"); // USANDO A CONSTANTE URL
            exit;
            
        } catch (\PDOException $e) {
            $_SESSION["error_message"] = "Erro ao cadastrar. Tente novamente. (Detalhe: " . $e->getMessage() . ")";
            header("Location: " . URL . "/cadastro"); // USANDO A CONSTANTE URL
            exit;
        }
    }

    // Método para exibir o formulário de Login (GET /login)
    public function login(array $dados) {
        session_start();
        $dados["title"] = "Login";
        
        // Carrega e limpa mensagens de sessão (erro/sucesso)
        if (isset($_SESSION["error_message"])) {
            $dados["error_message"] = $_SESSION["error_message"];
            unset($_SESSION["error_message"]);
        }
        if (isset($_SESSION["success_message"])) {
            $dados["success_message"] = $_SESSION["success_message"];
            unset($_SESSION["success_message"]);
        }

        echo $this->ambiente->render("login.html", $dados);
    }

    // Método para processar o Login (POST /login)
    public function autenticar(array $dados) {
        global $pdo;
        // REMOVIDO: global $URL; // Não precisamos disso se usarmos a constante URL
        session_start();

        $email = trim($dados["email"] ?? null);
        $senha_digitada = trim($dados["senha"] ?? null);

        if (empty($email) || empty($senha_digitada)) {
            $_SESSION["error_message"] = "Todos os campos são obrigatórios.";
            header("Location: " . URL . "/login"); // USANDO A CONSTANTE URL
            exit;
        }

        try {
            // BUSCA O USUÁRIO E O HASH ARMAZENADO NO BANCO
            $sql_select = "SELECT idCli, senha, nome FROM cliente WHERE email = :email";
            $stmt = $pdo->prepare($sql_select);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Lógica de Validação
            if ($cliente && password_verify($senha_digitada, $cliente['senha'])) {
                // LOGIN BEM-SUCEDIDO
                $_SESSION["idLogin"] = $cliente['idCli'];
                
                $_SESSION["success_message"] = "Login realizado com sucesso!";
                header("Location: " . URL . "/adicionar"); // USANDO A CONSTANTE URL
                exit; 

            } else {
                // FALHA: Email não encontrado OU Senha incorreta
                $_SESSION["error_message"] = "Email ou senha incorretos.";
                header("Location: " . URL . "/login"); // USANDO A CONSTANTE URL
                exit;
            }

        } catch (\PDOException $e) {
            $_SESSION["error_message"] = "Erro interno ao tentar autenticar. Tente novamente mais tarde.";
            header("Location: " . URL . "/login"); // USANDO A CONSTANTE URL
            exit;
        }
    }
}