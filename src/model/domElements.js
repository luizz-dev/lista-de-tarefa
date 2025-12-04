// domElements.js

// --- Gerenciamento de Tarefas ---
export const inputTarefa = document.getElementById("tarefaVlr");
// NOVO: Referência para o botão de Adicionar
export const btnAdicionarTarefa = document.getElementById("adicionar");
export const tarefaLista = document.querySelector(".tarefaLista");
export const statusMensagem = document.getElementById("status-mensagem");
export let contador = 0;

export function incrementarContador() {
  contador++;
  return contador;
}

// --- Paginação ---
export const numerosContainer = document.getElementById("numeros-container");
export const btnAnterior = document.getElementById("btn-anterior");
export const btnProximo = document.getElementById("btn-proximo");
export const botoesNumero = numerosContainer
  ? numerosContainer.getElementsByClassName("numero-btn")
  : [];
export let primeiraPaginaDoGrupo = 1;
export const tamanhoDoGrupo = 3;

// --- Perfil Toggle ---
export const btnPerfil = document.getElementById("btn-perfil");
export const perfilSection = document.querySelector(".perfil");
