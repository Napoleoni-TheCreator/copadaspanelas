function adicionarTime() {
    const nomeTime = prompt('Digite o nome do time (máximo de 25 caracteres):');
    if (!nomeTime || nomeTime.length > 25) {
        alert("O nome do time deve ter no máximo 25 caracteres. Por favor, tente novamente.");
        return;
    }
    const nomeTimeLimite = nomeTime.slice(0, 25); // Limita o nome do time a 25 caracteres
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.onchange = function(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(event) {
            const logoURL = event.target.result;
            let tabelaSelecionada = document.querySelector('#selecaoTabela').value;
            let tabelaTimes = document.getElementById(tabelaSelecionada);
            if (!tabelaTimes) {
                console.error("Tabela de times não encontrada!");
                return;
            }
            let numLinhas = tabelaTimes.querySelectorAll('tr.times').length + 1;
            let borda = '5px solid';
            const corBorda = numLinhas <= 2 ? 'blue' : numLinhas <= 4 ? 'orange' : 'transparent';
            const novoTimeHTML = `
                <tr class="times" id="${tabelaSelecionada}-time${numLinhas}" style="border-left: ${borda} ${corBorda}; border-bottom: 1px solid lightgray;">
                    <td class="td_time">
                        <div class="clube">
                            <span class="num_time">${numLinhas}</span> 
                            <img src="${logoURL}" alt="" class="logTime">
                            <span class="nomeTime">${nomeTimeLimite}</span>
                        </div>
                    </td>
                    <td class="td_time ponto">0</td>
                    <td class="td_time partidas">0</td>
                    <td class="td_time vitoria">0</td>
                    <td class="td_time empate">0</td>
                    <td class="td_time derrota">0</td>
                    <td class="td_time golMarcado">0</td>
                    <td class="td_time golContra">0</td>
                    <td class="td_time saldodeGol">0</td>
                    <td id="td_ultimas">

                    </td>
                </tr>
            `;
            tabelaTimes.innerHTML += novoTimeHTML;
            let select = document.getElementById('selecaoTime');
            let option = document.createElement('option');
            option.value = `${tabelaSelecionada}-time${numLinhas}`;
            option.textContent = nomeTimeLimite;
            select.appendChild(option);
        };
        reader.readAsDataURL(file);
    };
    fileInput.click();
}

function adicionarTabela() {
    const newTable = document.createElement('table');
    let group = String.fromCharCode('A'.charCodeAt(0) + document.querySelectorAll('#tabelas table').length);
   innerHTML = `
        <br>
        <h1 class="grupos">GRUPO ${group}</h1>
        <tr>
            <th>Clube</th>
            <th>Pts</th>
            <th>pj</th> 
            <th>VIT</th>
            <th>E</th>
            <th>DER</th>
            <th>GM</th>
            <th>GC</th>
            <th>SG</th>
            <th>Últimas 5</th>
        </tr>
    `; newTable.
    newTable.id = `tabela-${group}`;
    let tabelas = document.getElementById('tabelas');
    tabelas.appendChild(newTable);
    
    let tables = Array.from(tabelas.querySelectorAll('table'));
    tables.sort(function(a, b) {
        let textA = a.querySelector('.grupos').textContent.toUpperCase();
        let textB = b.querySelector('.grupos').textContent.toUpperCase();
        return textA.localeCompare(textB);
    });
    tabelas.innerHTML = '';
    tables.forEach(function(table) {
        tabelas.appendChild(table);
    });
    let select = document.getElementById('selecaoTabela');
    let option = document.createElement('option');
    option.value = newTable.id;
    option.textContent = `Grupo ${group}`;
    select.appendChild(option);

    // Mostra a div com a classe "container"
    document.getElementById("containerDiv").style.display = "block";
    document.getElementById("containerDiv2").style.display = "block";
}

function adicionarVitoria() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let vitorias = document.getElementById(selecaoTime).querySelector('.vitoria');
    let pontos = document.getElementById(selecaoTime).querySelector('.ponto');
    vitorias.textContent = parseInt(vitorias.textContent) + 1;
    pontos.textContent = parseInt(pontos.textContent) + 3;
    adicionarUltimaPartidaImagem('vitoria'); // Adiciona imagem de vitória
    adicionarPartidaJogada() 
    ordenarPorPontos();
}

function adicionarEmpate() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let empates = document.getElementById(selecaoTime).querySelector('.empate');
    let pontos = document.getElementById(selecaoTime).querySelector('.ponto');
    empates.textContent = parseInt(empates.textContent) + 1;
    pontos.textContent = parseInt(pontos.textContent) + 1;
    adicionarUltimaPartidaImagem('empate');
    adicionarPartidaJogada() 
    ordenarPorPontos();
}

function adicionarDerrota() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let derrotas = document.getElementById(selecaoTime).querySelector('.derrota');
    derrotas.textContent = parseInt(derrotas.textContent) + 1;
    adicionarUltimaPartidaImagem('derrota');
    adicionarPartidaJogada() 
    ordenarPorPontos();
}

// Crie um objeto para armazenar os resultados das últimas partidas de cada time
const ultimosResultados = {};
function adicionarUltimaPartidaImagem(resultado) {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;

    let tabelaSelecionada = document.querySelector('#selecaoTabela').value;
    let tabelaTimes = document.getElementById(tabelaSelecionada);
    if (!tabelaTimes) {
        console.error("Tabela de times não encontrada!");
        return;
    }
    let time = document.getElementById(selecaoTime);
    if (!time) {
        console.error("Time não encontrado!");
        return;
    }
    let ultimasPartidasDiv = time.querySelector('#td_ultimas');
    if (!ultimasPartidasDiv) {
        console.error("Div das últimas partidas não encontrada!");
        return;
    }

    // Verifica se já existem 5 imagens de resultados
    let imagensResultados = ultimasPartidasDiv.querySelectorAll('img');
    if (imagensResultados.length >= 5) {
        ultimasPartidasDiv.removeChild(imagensResultados[0]); // Remove a primeira imagem
    }

    // Adiciona a imagem específica com base no resultado da última partida
    let imagem = document.createElement('img');
    if (resultado === 'vitoria') {
        imagem.src = "img/vitoria.svg";
    } else if (resultado === 'empate') {
        imagem.src = "img/empate.svg";
    } else if (resultado === 'derrota') {
        imagem.src = "img/derrota.svg";
    }
    // Adiciona a classe à imagem
    imagem.classList.add('imagem-ultima-partida');
    // Armazena o resultado da última partida no array de resultados
    if (!ultimosResultados[selecaoTime]) {
        ultimosResultados[selecaoTime] = [];
    }
    ultimosResultados[selecaoTime].push(resultado);
    if (ultimosResultados[selecaoTime].length > 5) {
        ultimosResultados[selecaoTime].shift(); // Remove o resultado mais antigo se houver mais de 5 resultados armazenados
    }

    // Adiciona a imagem do resultado mais recente
    ultimasPartidasDiv.appendChild(imagem); // Adiciona a nova imagem no final da lista
}

function adicionar_golsContra() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let golsContra = document.getElementById(selecaoTime).querySelector('.golContra');
    let saldo = document.getElementById(selecaoTime).querySelector('.saldodeGol');
    golsContra.textContent = parseInt(golsContra.textContent) + 1;
    saldo.textContent = parseInt(saldo.textContent) - 1;
    ordenarPorPontos();
}

function adicionarGol() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let golsMarcados = document.getElementById(selecaoTime).querySelector('.golMarcado');
    let saldo = document.getElementById(selecaoTime).querySelector('.saldodeGol');
    golsMarcados.textContent = parseInt(golsMarcados.textContent) + 1;
    saldo.textContent = parseInt(saldo.textContent) + 1;
    ordenarPorPontos();
}

function adicionarPartidaJogada() {
    let selecaoTime = document.getElementById('selecaoTime').value;
    if (!selecaoTime) return;
    let partidas = document.getElementById(selecaoTime).querySelector('.partidas');
    partidas.textContent = parseInt(partidas.textContent) + 1;
}

function ordenarPorPontos() {
    let tabelas = document.querySelectorAll('#tabelas table');
    tabelas.forEach(function(table) {
        let linhas = Array.from(table.querySelectorAll('tr.times'));
        linhas.sort(function(a, b) {
            let pontosA = parseInt(a.querySelector('.ponto').textContent);
            let pontosB = parseInt(b.querySelector('.ponto').textContent);
            let golsMarcadosA = parseInt(a.querySelector('.golMarcado').textContent);
            let golsMarcadosB = parseInt(b.querySelector('.golMarcado').textContent);
            let saldoGolsA = golsMarcadosA - parseInt(a.querySelector('.golContra').textContent);
            let saldoGolsB = golsMarcadosB - parseInt(b.querySelector('.golContra').textContent);
            if (pontosA !== pontosB) {
                return pontosB - pontosA; // Ordenar por pontos
            } else {
                return saldoGolsB - saldoGolsA; // Em caso de empate, ordenar pelo saldo de gols
            }
        });
        linhas.forEach(function(row, index) { // Atualizar o número da posição
            row.querySelector('.num_time').textContent = index + 1;
            table.appendChild(row);
        });
    });
}
