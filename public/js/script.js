  // Função para inicializar o player
  function onYouTubeIframeAPIReady() {
    // Cria um novo player do YouTube
    var player = new YT.Player('player', {
        // ID do vídeo que deseja reproduzir
        videoId: 'K8FVmLT94WQ',
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