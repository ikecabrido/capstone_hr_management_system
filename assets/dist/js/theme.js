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

  // save locally
  localStorage.setItem("theme", mode);
  localStorage.setItem("darkMode", mode === "dark" ? "true" : "false");

  // save in database
  fetch("../update_theme.php", {
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

// Restore the user's choice immediately on every page, before the page is used.
// The server-side theme may be unavailable or may lag behind the last toggle.
const savedTheme = localStorage.getItem("theme");
const savedDarkMode = localStorage.getItem("darkMode");
const initialTheme = savedTheme || (savedDarkMode === "true" ? "dark" : "light");

if (initialTheme === "dark") {
  document.body.classList.add("dark-mode");
} else if (initialTheme === "light") {
  document.body.classList.remove("dark-mode");
}

if (toggleBtn) {
  toggleBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const isDark = document.body.classList.contains("dark-mode");

    setTheme(isDark ? "light" : "dark");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const iconEl = document.getElementById("themeIcon");

  if (iconEl) {
    if (document.body.classList.contains("dark-mode")) {
      iconEl.classList.replace("fa-moon", "fa-sun");
    } else {
      iconEl.classList.replace("fa-sun", "fa-moon");
    }
  }
});
