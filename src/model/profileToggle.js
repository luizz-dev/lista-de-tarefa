// profileToggle.js

import { btnPerfil, perfilSection } from "./domElements.js";

function perfilToggle() {
  console.log("Clique no Perfil detectado!");

  // Altera a classe 'show' no elemento de SEÇÃO DO PERFIL
  if (perfilSection) {
    // CORREÇÃO: Trocar 'on' por 'show' para corresponder ao CSS
    perfilSection.classList.toggle("show");
  }
}

// Garante que o elemento btnPerfil existe antes de adicionar o listener
if (btnPerfil) {
  btnPerfil.addEventListener("click", perfilToggle);
} else {
  console.error("Erro: Elemento com ID 'btn-perfil' não encontrado no DOM.");
  // Comentado para evitar erro se o botão for opcional.
}
