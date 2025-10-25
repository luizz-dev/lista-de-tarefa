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

    public function home(array $dados){
        $dados["title"] = "Pagina inical";
        $dados["message"] = "Seja bem vindo!";

        echo $this->ambiente->render("home.html",$dados);
    }
    
    public function cadastro(array $dados){
       echo $this->ambiente->render("cadastro.html", $dados);
    }
    
    // Assumindo que o ambiente do Router injete a variável $this->ambiente para o motor de templates
// e que a classe Principal é onde estas funções estão.

    public function login(array $dados)
    {
        $dados["title"] = "Login";
        echo $this->ambiente->render("login.html", $dados);
    }

    public function autenticar(array $dados)
    {
        session_start();

        $login = trim($dados["email"]);
        $senha = trim($dados["senha"]);

        // 2. Lógica de Validação e Sucesso
        if ($login === "mouse@gmail.com" && $senha === 'gamer') {
            // Sucesso
            $_SESSION["idLogin"] = 80;
            $dados["nome"] = "Cliente logado";
            $dados["mensagem"] = "Login realizado com sucesso!";
            
            // Renderiza a página de autenticação/área logada
            echo $this->ambiente->render("adicionar.html", $dados);
            return; // Termina a execução após a renderização
        }

       
        // Renderiza o login novamente com uma mensagem de erro ou redireciona
        $_SESSION["error_message"] = "Email ou senha incorretos.";
        header("Location: /login");
        exit;
    }

    
}