var player;

function link_live() {
  var link_live = window.prompt("Por favor, digite o link atual da live", "");
  if (link_live) {
    link_live = link_live.replace("https://www.youtube.com/watch?v=", "");
    if (player) {
      player.loadVideoById(link_live);
    } else {
      onYouTubeIframeAPIReady(link_live);
    }
  }
}

document.addEventListener("DOMContentLoaded", function() {
  var adcLiveButton = document.querySelector(".adc_live");
  if (adcLiveButton) {
    adcLiveButton.addEventListener("click", link_live);
  }
});

function onYouTubeIframeAPIReady(link_live) {
  player = new YT.Player('player', {
    videoId: link_live,
    playerVars: {
      autoplay: 1,
      controls: 1,
      showinfo: 1,
      vq: 'hd720'
    }
  });
}
