var player;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('player', {
        height: '390',
        width: '640',
        videoId: 'jfKfPfyJRdk',
        playerVars: {
            'autoplay': 1,
            'controls': 1   
        }


    });
}
// Adicione este código para selecionar a div específica
var divPlayer = document.getElementById('player');
player.setSize(divPlayer.clientWidth, divPlayer.clientHeight);