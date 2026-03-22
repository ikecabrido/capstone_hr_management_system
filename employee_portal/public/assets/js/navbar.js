// search button
const searchIcon = document.getElementById('searchIcon');
const searchInput = document.getElementById('searchInput');

searchIcon.addEventListener('click', function () {
    searchInput.classList.toggle('w-48');
    searchInput.classList.toggle('opacity-100');
    searchInput.focus();
});

document.addEventListener('click', function (event) {
    if (!searchIcon.contains(event.target) && !searchInput.contains(event.target)) {
        searchInput.classList.remove('w-48', 'opacity-100');
        searchInput.value = '';
    }
});



// full screen button
const fullscreenBtn = document.getElementById('fullscreenBtn');
const docEl = document.documentElement;

const requestFullscreen = () => {
    if (docEl.requestFullscreen) docEl.requestFullscreen();
    else if (docEl.mozRequestFullScreen) docEl.mozRequestFullScreen();
    else if (docEl.webkitRequestFullscreen) docEl.webkitRequestFullscreen();
    else if (docEl.msRequestFullscreen) docEl.msRequestFullscreen();
};

const exitFullscreen = () => {
    if (document.exitFullscreen) document.exitFullscreen();
    else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
    else if (document.msExitFullscreen) document.msExitFullscreen();
};

const applyZoomEffect = () => document.body.style.transform = "scale(0.9)";
const removeZoomEffect = () => document.body.style.transform = "scale(1)";

const updateButtonIcon = (isFullscreen) => {
    fullscreenBtn.innerHTML = isFullscreen ? '<i class="fas fa-compress"></i>' : '<i class="fas fa-expand"></i>';
};

const toggleFullscreen = () => {
    const isFullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
    if (isFullscreen) {
        exitFullscreen();
        removeZoomEffect();
    } else {
        requestFullscreen();
        applyZoomEffect();
    }
    updateButtonIcon(!isFullscreen);
};

fullscreenBtn.addEventListener('click', toggleFullscreen);

['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'msfullscreenchange'].forEach(event => {
    document.addEventListener(event, () => {
        const isFullscreen = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
        updateButtonIcon(isFullscreen);
        if (!isFullscreen) removeZoomEffect();
    });
});

// dark mode toggle


