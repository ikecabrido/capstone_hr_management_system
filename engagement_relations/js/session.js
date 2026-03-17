// ============================================
// SESSION VALIDATION & SECURITY
// Used for pages requiring session verification
// ============================================

// Validate user session
function validateSession() {
    const rootPath = window.location.pathname.replace(/\/engagement_relations\/.*/, '');
    const apiUrl = `${window.location.origin}${rootPath}/engagement_relations/api/session-check.php`;
    const loginUrl = `${window.location.origin}${rootPath}/login.php`;

    fetch(apiUrl)
        .then(response => {
            if (response.status === 401) {
                window.location.href = loginUrl;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && !data.authenticated) {
                window.location.href = loginUrl;
            }
        })
        .catch(error => {
            // Silently fail on session check - don't break UI
            console.error('Session validation error:', error);
        });
}

// Initial session validation (non-blocking)
validateSession();

// Prevent browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.pushState(null, null, location.href);
};

// Validate session every 30 seconds
setInterval(validateSession, 30000);

// Prevent browser back button
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.pushState(null, null, location.href);
};
