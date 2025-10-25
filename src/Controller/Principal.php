<?php
// Namespace conforme o psr-4 do composer.json
namespace Luizeduardo\ListaTarefa\Controller;

require'src/Database/Conexao.php';

class Principal{
    /**
     * A classe Environment serve como gerenciadora
     * dos dados que vem do template e do controlador 
     * O papel dela é combinar os dados e gerar o html final
     * * @var \Twig\Environment
     */
    private \Twig\Environment $ambiente;

    /** * O carregador tem a função de ler o template
     * de alguma origem. Neste caso carregaremos o
     * template do sistema de arquivos (ou seja,
     * disco ou armazenamento local)
     *
     * @var \Twig\Loader\FilesystemLoader
     */
    private \Twig\Loader\FilesystemLoader $carregador;

    public function __construct(){
        // Abre o diretório onde se encontram os templates
        // O caminho foi ajustado para considerar que Principal.php está em src/Controller
        $this->carregador = new \Twig\Loader\FilesystemLoader("./src/View");
        $this->ambiente = new \Twig\Environment($this->carregador);
    }

    public function adicionar(array $dados){
        $dados["title"] = "Pagina Adicionar";

        echo $this->ambiente->render("adicionar.html",$dados);
    }

    // No seu Principal.php, dentro da classe Principal

    public function login(array $dados)
    {
        session_start();
        $dados["title"] = "Login";
        
        //VERIFICA E PASSA A MENSAGEM DE ERRO (e/ou sucesso)
        if (isset($_SESSION["error_message"])) {
            $dados["error_message"] = $_SESSION["error_message"];
            unset($_SESSION["error_message"]); // 2. LIMPA A SESSÃO APÓS PEGAR A MENSAGEM
        }
        
        if (isset($_SESSION["success_message"])) {
            $dados["success_message"] = $_SESSION["success_message"];
            unset($_SESSION["success_message"]); // Limpa a mensagem de sucesso
        }

        echo $this->ambiente->render("login.html", $dados);
    }

    // Na classe Principal.php

    public function autenticar(array $dados)
    {
        global $pdo;
        session_start();

        //Recebe os dados
        $email = trim($dados["email"] ?? null);
        $senha_digitada = trim($dados["senha"] ?? null);

        // so fiz para ter certeza o require do html me garente isso
        if (empty($email) || empty($senha_digitada)) {
            $_SESSION["error_message"] = "Todos os campos são obrigatórios.";
            header("Location: " . URL . "/login"); // Redireciona para o login
            exit;
        }

       try {
        //  BUSCA O USUÁRIO E O HASH ARMAZENADO NO BANCO
        $sql_select = "SELECT idCli, senha, nome FROM cliente WHERE email = :email";
        $stmt = $pdo->prepare($sql_select);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

        //Lógica de Validação
        // Verifica se o cliente foi encontrado E se a senha digitada corresponde ao hash
        if ($cliente && password_verify($senha_digitada, $cliente['senha'])) {
            // LOGIN BEM-SUCEDIDO
            $_SESSION["idLogin"] = $cliente['idCli'];
            
            $_SESSION["success_message"] = "Login realizado com sucesso!";
            header("Location: " . URL . "/adicionar");
            exit; 

        } else {
            // FALHA: Cliente não encontrado OU Senha incorreta
            $_SESSION["error_message"] = "Email ou senha incorretos.";
            header("Location: " . URL . "/login");
            exit;
            }

        } catch (\PDOException $e) {
            // Erro de comunicação com o banco de dados
            $_SESSION["error_message"] = "Erro interno ao tentar autenticar. Tente novamente mais tarde.";
            header("Location: " . URL . "/login");
            exit;
        }
    }
    public function cadastro(array $dados){
        echo $this->ambiente->render("cadastro.html", $dados);
    }

    public function cadastrar(array $dados)
     {
        global $pdo; 
        session_start();

        // Receber e limpar dados do formulário
        $nome = trim($dados["nome"] ?? null);
        $email = trim($dados["email"] ?? null);
        $senha_pura = $dados["senha"] ?? null;

        //tem o require mas coloquei so pra garantir
        if (empty($nome) || empty($email) || empty($senha_pura)) {
            $_SESSION["error_message"] = "Todos os campos são obrigatórios";
            header("Location: " . URL . "/cadastro");
            exit;
        }

        //verifica se o email ja existe 
        try {
            $sql_check = "SELECT idCli FROM cliente WHERE email = :email";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();
        
            // Se a contagem de linhas for maior que zero, o email já existe
            if ($stmt_check->rowCount() > 0) {
                $_SESSION["error_message"] = "O email ja esta sendo utilizado";
                header("Location: " . URL . "/cadastro");
                exit;
            }
        } catch (\PDOException $e) {
            // Erro durante a consulta de verificação
            $_SESSION["error_message"] = "Erro interno na verificação de email. Tente novamente.";
            header("Location: " . URL . "/cadastro");
            exit;
        }

        // Criar Hash da Senha 
        $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);
        
        try {
            // Preparar e Executar a Inserção no Banco
            $sql_insert = "INSERT INTO cliente (email, senha, nome) VALUES (:email, :senha, :nome)";
            $stmt = $pdo->prepare($sql_insert);
            
            //tratamento do bind para segurança
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':nome', $nome);
            
            $stmt->execute();
            
            $_SESSION["success_message"] = "Cadastro realizado com sucesso! Faça login.";
            header("Location: " . URL . "/login"); // Redireciona para o login
            exit;
            
        } catch (\PDOException $e) {
            $_SESSION["error_message"] = "Erro ao cadastrar. Tente novamente. (Detalhe: " . $e->getMessage() . ")";
            header("Location: " . URL . "/cadastro");
            exit;
        }
    }
}