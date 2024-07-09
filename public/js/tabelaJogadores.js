document.getElementById('addPlayer').addEventListener('click', function() {
    var playerName = document.getElementById('playerName').value;
    var playerPosition = document.getElementById('playerPosition').value;
    var playerNumber = document.getElementById('playerNumber').value;
    var playerTeam = document.getElementById('playerTeam').value;
    var playerImage = document.getElementById('playerImage').files[0];

    var jogadorDiv = document.createElement('div');
    jogadorDiv.className = 'jogador';

    var img = document.createElement('img');
    img.className = 'img-jogador';
    img.src = URL.createObjectURL(playerImage);

    var infoDiv = document.createElement('div');
    infoDiv.className = 'informacoes-jogador';

    var h3 = document.createElement('h3');
    h3.textContent = playerName;

    var p1 = document.createElement('p');
    p1.textContent = playerPosition;

    var p2 = document.createElement('p');
    p2.textContent = playerNumber;

    var p3 = document.createElement('p');
    p3.textContent = playerTeam;

    var deleteButton = document.createElement('button');
    deleteButton.textContent = 'Apagar';
    deleteButton.addEventListener('click', function() {
        jogadorDiv.remove();
    });

    infoDiv.appendChild(h3);
    infoDiv.appendChild(p1);
    infoDiv.appendChild(p2);
    infoDiv.appendChild(p3);
    infoDiv.appendChild(deleteButton);

    jogadorDiv.appendChild(img);
    jogadorDiv.appendChild(infoDiv);

    var containerDiv = document.getElementById('container');
    containerDiv.appendChild(jogadorDiv);
});
