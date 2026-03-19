// Theme persistence - MUST load early to prevent flash of wrong theme
(function() {
  // Check localStorage immediately
  var savedTheme = localStorage.getItem("theme");
  
  // Apply saved theme BEFORE page renders (prevent flash)
  if (savedTheme === "light") {
    document.body.classList.remove("dark-mode");
  } else if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
  }
  // If no saved theme, default to dark-mode (which is already in body class)
})();

// Theme toggle functionality
const toggleBtn = document.getElementById("darkToggle");
const icon = document.getElementById("themeIcon");

function setTheme(mode) {
  if (mode === "dark") {
    document.body.classList.add("dark-mode");
    if (icon) icon.classList.replace("fa-moon", "fa-sun");
  } else {
    document.body.classList.remove("dark-mode");
    if (icon) icon.classList.replace("fa-sun", "fa-moon");
  }

  // Save locally
  localStorage.setItem("theme", mode);

  // Determine the correct path based on current location
  var updatePath = "../update_theme.php";
  // If we're in a subdirectory (like legal_compliance/views/), use correct path
  if (window.location.pathname.includes("/views/")) {
    updatePath = "../../update_theme.php";
  }

  // Save in database
  fetch(updatePath, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "theme=" + mode,
  })
    .then((res) => res.text())
    .then((data) => console.log("Theme updated:", data))
    .catch((err) => console.error("Theme error:", err));
}

if (toggleBtn) {
  toggleBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const isDark = document.body.classList.contains("dark-mode");

    setTheme(isDark ? "light" : "dark");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const icon = document.getElementById("themeIcon");

  if (document.body.classList.contains("dark-mode")) {
    if (icon) icon.classList.replace("fa-moon", "fa-sun");
  } else {
    if (icon) icon.classList.replace("fa-sun", "fa-moon");
  }
});
