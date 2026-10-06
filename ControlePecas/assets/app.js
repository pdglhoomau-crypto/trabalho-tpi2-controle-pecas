"use strict";
const form = document.querySelector("form[data-entidade]");
const resultado = document.querySelector("#resultado");
if (form) {
  form.addEventListener("reset", () => { resultado.hidden = true; resultado.textContent = ""; });
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const botao = form.querySelector('[type="submit"]');
    botao.disabled = true;
    resultado.hidden = false;
    resultado.className = "";
    resultado.textContent = "Validando...";
    try {
      const resposta = await fetch("api/validar.php?entidade=" + encodeURIComponent(form.dataset.entidade), {
        method: "POST", body: new FormData(form), headers: { "Accept": "application/json" }
      });
      const dados = await resposta.json();
      if (!resposta.ok) throw new Error((dados.erros || [dados.mensagem || "Erro no servidor."]).join("\n"));
      resultado.textContent = dados.mensagem + "\n" + dados.resultado;
    } catch (erro) {
      resultado.className = "erro";
      resultado.textContent = "Não foi possível validar.\n" + erro.message + "\nAbra o projeto por um servidor PHP (XAMPP ou php -S), conforme o README.";
    } finally { botao.disabled = false; }
  });
}
