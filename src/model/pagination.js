import {
  numerosContainer,
  btnAnterior,
  btnProximo,
  botoesNumero,
  primeiraPaginaDoGrupo,
  tamanhoDoGrupo,
} from "./domElements.js";

/**
 * Atualiza os rótulos (textos) dos botões de número
 */
function atualizarNumeros() {
  for (let i = 0; i < tamanhoDoGrupo; i++) {
    const novoNumero = primeiraPaginaDoGrupo + i;
    // Verifica se o botão existe antes de tentar manipular
    if (botoesNumero[i]) {
      botoesNumero[i].textContent = novoNumero;
      botoesNumero[i].dataset.page = novoNumero;
    }
  }
}

// Lógica de Paginação
if (btnProximo) {
  btnProximo.addEventListener("click", () => {
    // Altera a variável global (apenas para este escopo)
    primeiraPaginaDoGrupo += tamanhoDoGrupo;
    atualizarNumeros();
  });
}

if (btnAnterior) {
  btnAnterior.addEventListener("click", () => {
    if (primeiraPaginaDoGrupo > 1) {
      // Altera a variável global (apenas para este escopo)
      primeiraPaginaDoGrupo -= tamanhoDoGrupo;
      atualizarNumeros();
    }
  });
}

// Adiciona listener para clique nos números
for (let i = 0; i < botoesNumero.length; i++) {
  botoesNumero[i].addEventListener("click", function () {
    // Remove a classe 'ativo' de todos
    for (let j = 0; j < botoesNumero.length; j++) {
      botoesNumero[j].classList.remove("ativo");
    }
    // Adiciona a classe 'ativo' apenas no botão clicado
    this.classList.add("ativo");

    // Carregamento real do conteúdo
    console.log(`Carregando conteúdo da página: ${this.textContent}`);
  });
}

// Inicializa a exibição (exporta a função ou chama na inicialização)
atualizarNumeros();
