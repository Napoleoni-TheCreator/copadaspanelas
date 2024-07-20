
var player;
var videoId; // Variável global para armazenar o ID do vídeo
document.addEventListener("DOMContentLoaded", function() {
  // Requisição GET para buscar o link da live
  fetch('../../app/actions/lives/get_link.php')
    .then(response => response.text())
    .then(link_live => {
      console.log("Link retornado do banco de dados: ", link_live);
      if (link_live && link_live !== "Nenhum link encontrado") {
        videoId = link_live; // Atualizar a variável global com o ID do vídeo do banco de dados
        if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
          // Carregar a API do YouTube se ainda não estiver carregada
          var tag = document.createElement('script');
          tag.src = "https://www.youtube.com/iframe_api";
          var firstScriptTag = document.getElementsByTagName('script')[0];
          firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
          
          // A função onYouTubeIframeAPIReady será chamada quando a API do YouTube estiver carregada
          window.onYouTubeIframeAPIReady = function() {
            onYouTubeIframeAPIReady();
          };
        } else {
          onYouTubeIframeAPIReady();
        }
      }
    }).catch(error => {
      console.error('Erro ao carregar o link:', error);
    });

  var adcLiveButton = document.querySelector(".adc_live");
  if (adcLiveButton) {
    adcLiveButton.addEventListener("click", link_live);
  }
});

function onYouTubeIframeAPIReady() {
  if (videoId) {
    player = new YT.Player('player', {
      videoId: videoId,
      playerVars: {
        autoplay: 1,
        controls: 1,
        showinfo: 1,
        vq: 'hd720'
      }
    });
  }
}
function link_live() {
  var link_live = window.prompt("Por favor, digite o link atual da live", "");
  if (link_live) {
    link_live = link_live.replace("https://www.youtube.com/watch?v=", "");
    console.log("Enviando link para salvar: ", link_live);
    fetch('../../app/actions/lives/save_link.php', {
      method: 'POST', // Certifique-se de que o método é POST
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded', // Certifique-se de que o Content-Type está correto
      },
      body: 'linklive=' + encodeURIComponent(link_live) // Certifique-se de que o corpo da requisição está correto
    }).then(response => {
      if (response.ok) {
        return response.text();
      } else {
        throw new Error('Erro ao salvar o link.');
      }
    }).then(text => {
      console.log("Resposta do servidor: ", text);
      videoId = link_live; // Atualizar a variável global com o novo ID do vídeo
      if (player) {
        player.loadVideoById(videoId);
      } else {
        // Chamar diretamente a inicialização do player
        onYouTubeIframeAPIReady();
      }
    }).catch(error => {
      console.error("Erro ao salvar o link: ", error);
    });
  }
}


