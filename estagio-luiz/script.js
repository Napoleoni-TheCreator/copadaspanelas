  // Função para inicializar o player

  function link_live(){
    var link_live = window.prompt("Por favor digite o link atual da live","")
    link_live = link_live.replace("https://www.youtube.com/watch?v=","")
    onYouTubeIframeAPIReady(link_live)
  }
  
  
  document.getElementById("adc_live").addEventListener("click",link_live);
  
  
  
  
    function onYouTubeIframeAPIReady(link_live) {
      // Cria um novo player do YouTube
      var player = new YT.Player('player', {
          // ID do vídeo que deseja reproduzir
          videoId: link_live,
          // Opções do player
          playerVars: {
              // Define a reprodução automática
              autoplay: 1,
              // Controles do player
              controls: 1,
              // Mostra o título do vídeo
              showinfo: 1,
              // Qualidade do vídeo
              vq: 'hd720'
          }
      });
  }
  
  // Carrega a API do YouTube
  window.onload = function() {
      onYouTubeIframeAPIReady();
  };