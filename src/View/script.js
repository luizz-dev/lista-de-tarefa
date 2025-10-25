const input = document.getElementById("tarefaVlr");
const tarefaLista = document.querySelector(".tarefaLista");
let contador = 0;

function adicionarTarefa() {
  const tarefaVlr = input.value;
  if (tarefaVlr.trim() !== "") {
    contador++;

    let novoItem = `
        <section id="tarefa-${contador}" class="tarefa">
            <div onclick="clicouInpt(${contador})" class="tarefa-icone">
                <img id="icone-${contador}" src="img/circle-outline.svg" alt="icone">
            </div>
            <div onclick="clicouInpt(${contador})" class="tarefa-txt">
                <h2>${tarefaVlr}</h2>
            </div>
            <div onclick="removerTarefa(${contador})" class="tarefa-del">
                <button>Deletar</button>
            </div>
        </section>`;

    tarefaLista.innerHTML += novoItem;

    input.value = "";
    input.focus();
  }
}

function clicouInpt(id) {
  const tarefa = document.getElementById(`tarefa-${id}`);
  const icone = document.getElementById(`icone-${id}`);

  tarefa.classList.toggle("clicado");

  if (tarefa.classList.contains("clicado")) {
    icone.src = "img/accept.png";
  } else {
    icone.src = "img/circle-outline.svg";
  }
}

function removerTarefa(id) {
  const tarefa = document.getElementById(`tarefa-${id}`);
  tarefa.remove();
}

input.addEventListener("keydown", (k) => {
  if (k.key === "Enter") {
    adicionarTarefa();
  }
});
