(() => {

    const TIMEOUT = 15 * 60 * 1000; // 15 minutes

    const logoutUrl = document.body.dataset.logoutUrl;

    let inactivityTimer;

    function logout() {
        window.location.href = logoutUrl;
    }

    function resetTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(logout, TIMEOUT);
    }

    [
        "mousemove",
        "mousedown",
        "click",
        "keydown",
        "scroll",
        "touchstart"
    ].forEach(event => {
        document.addEventListener(event, resetTimer, true);
    });

    resetTimer();

})();