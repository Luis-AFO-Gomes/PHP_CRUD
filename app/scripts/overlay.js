const overlay = document.getElementById("overlay");

// Abre a janela flutuante ao clicar no botão de login (Objecto DOM "open")
document.getElementById("open").addEventListener("click", () => {
  overlay.classList.remove("hidden");
});

// Fecha a janela flutuante ao clicar no botão [X] (Objecto DOM "close")
document.getElementById("close").addEventListener("click", () => {
  overlay.classList.add("hidden");
});

// Fecha a janela flutuante ao clicar fora dela
overlay.addEventListener("click", (e) => {
  if (e.target === overlay) overlay.classList.add("hidden");
});
