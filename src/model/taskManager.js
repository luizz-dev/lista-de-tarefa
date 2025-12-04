// taskManager.js

import { inputTarefa, tarefaLista, statusMensagem } from "./domElements.js";

// URL base da sua API. AJUSTE ISSO conforme a sua rota no index.php
// Vou usar um caminho que espero que seu roteador do PHP capture:
const API_URL = "api/tarefas";

// --- Funções Auxiliares de UI ---
function exibirStatus(mensagem, sucesso = true) {
  if (statusMensagem) {
    statusMensagem.textContent = mensagem;
    statusMensagem.style.color = sucesso ? "green" : "red";
    // Limpa a mensagem após 3 segundos
    setTimeout(() => {
      statusMensagem.textContent = "";
    }, 3000);
  }
}

/**
 * Cria o elemento DOM para uma tarefa vinda do Banco de Dados.
 * @param {object} tarefa - Objeto { id, descricao, feita }
 */
function criarItemTarefa(tarefa) {
  const item = document.createElement("section");
  item.id = `tarefa-${tarefa.id}`;
  item.classList.add("tarefa");

  // Converte 1/0 do DB para classes/src
  if (tarefa.feita == 1) {
    item.classList.add("clicado");
  }

  item.innerHTML = `
        <div class="tarefa-icone">
            <img id="icone-${tarefa.id}" src="${
    tarefa.feita == 1 ? "img/accept.png" : "img/circle-outline.svg"
  }" alt="icone">
        </div>
        <div class="tarefa-txt">
            <h2>${tarefa.descricao}</h2>
        </div>
        <div class="tarefa-del">
            <button data-id="${tarefa.id}">Deletar</button>
        </div>
    `;

  // ANEXA LISTENERS (para as ações CRUD no front-end)
  const marcar = () => marcarTarefa(tarefa.id, item);
  item.querySelector(".tarefa-icone").addEventListener("click", marcar);
  item.querySelector(".tarefa-txt").addEventListener("click", marcar);

  item
    .querySelector(".tarefa-del button")
    .addEventListener("click", () => excluirTarefa(tarefa.id, item));

  return item;
}

// --- Funções de Ação no Banco de Dados (CRUD) ---

/** * ADICIONAR TAREFA (Usado em adicionar.html)
 * Apenas envia a requisição e exibe o status.
 */
export async function adicionarTarefa() {
  const descricao = inputTarefa.value.trim();
  if (!descricao) {
    exibirStatus("A descrição não pode ser vazia.", false);
    return;
  }

  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ descricao }),
    });
    const result = await response.json();

    if (result.success) {
      // SUCESSO! Não adiciona ao DOM.
      exibirStatus("✅ Tarefa adicionada com sucesso no Banco de Dados!", true);
      inputTarefa.value = ""; // Limpa o input
    } else {
      exibirStatus(`❌ Erro: ${result.message}`, false);
    }
  } catch (error) {
    exibirStatus("❌ Erro de conexão ao servidor.", false);
  }
}

/** * CARREGAR TAREFAS (Usado em vizualizar.html)
 * Busca todas as tarefas no DB e popula o DOM.
 */
export async function carregarTarefas() {
  if (!tarefaLista) return;

  try {
    const response = await fetch(API_URL, { method: "GET" });
    const result = await response.json();

    if (result.success && Array.isArray(result.data)) {
      tarefaLista.innerHTML = ""; // Limpa antes de recarregar
      result.data.forEach((tarefa) => {
        tarefaLista.appendChild(criarItemTarefa(tarefa));
      });
    } else {
      exibirStatus("Nenhuma tarefa encontrada.", true);
    }
  } catch (error) {
    exibirStatus(
      "Erro de conexão ao carregar tarefas. Verifique a API.",
      false
    );
  }
}

/** * MARCAR/DESMARCAR TAREFA
 */
async function marcarTarefa(id, itemElement) {
  // Determina o próximo status. Se está clicado (feita=1), o próximo é 0.
  const isFeita = itemElement.classList.contains("clicado") ? 0 : 1;

  try {
    const response = await fetch(API_URL, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, feita: isFeita }),
    });
    const result = await response.json();

    if (result.success) {
      // Atualiza o DOM localmente
      itemElement.classList.toggle("clicado");
      const icone = itemElement.querySelector(`#icone-${id}`);
      icone.src = isFeita ? "img/accept.png" : "img/circle-outline.svg";
    } else {
      alert(`Erro ao marcar tarefa: ${result.message}`);
    }
  } catch (error) {
    alert("Erro de conexão ao marcar tarefa.");
  }
}

/** * EXCLUIR TAREFA
 */
async function excluirTarefa(id, itemElement) {
  if (!confirm("Tem certeza que deseja excluir esta tarefa?")) return;

  try {
    const response = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id }),
    });
    const result = await response.json();

    if (result.success) {
      itemElement.remove(); // Remove o item do DOM
    } else {
      alert(`Erro ao excluir tarefa: ${result.message}`);
    }
  } catch (error) {
    alert("Erro de conexão ao excluir tarefa.");
  }
}
