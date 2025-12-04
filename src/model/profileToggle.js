import { btnPerfil, perfilSection } from "./domElements.js";

console.log("ola");

function perfilToggle() {
  console.log("Clique no Perfil detectado!");

  // Alterna a classe 'on' no elemento de SEÇÃO DO PERFIL
  if (perfilSection) {
    perfilSection.classList.toggle("on");
  }
}

// Garante que o elemento btnPerfil existe antes de adicionar o listener
if (btnPerfil) {
  btnPerfil.addEventListener("click", perfilToggle);
} else {
  // console.error("Erro: Elemento com ID 'btn-perfil' não encontrado no DOM.");
  // Comentado para evitar erro se o botão for opcional.
}
