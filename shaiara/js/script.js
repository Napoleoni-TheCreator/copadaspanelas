document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    form.addEventListener("submit", function(event) {
        const nomeTime = document.getElementById("nome_time").value;
        const nomeTecnico = document.getElementById("nome_tecnico").value;
        const quantidadeJogadores = document.getElementById("quantidade_jogadores").value;




        if (!nomeTime || !nomeTecnico || !quantidadeJogadores) {
            event.preventDefault(); // Impede o envio do formulário se algum campo estiver vazio
            alert("Por favor, preencha todos os campos obrigatórios.");
        }
    });
});


