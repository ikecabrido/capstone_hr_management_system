const toggleBtn = document.getElementById("darkToggle");
const icon = document.getElementById("themeIcon");

function setTheme(mode) {
  if (mode === "dark") {
    document.body.classList.add("dark-mode");
    icon.classList.replace("fa-moon", "fa-sun");
  } else {
    document.body.classList.remove("dark-mode");
    icon.classList.replace("fa-sun", "fa-moon");
  }

  // save locally
  localStorage.setItem("theme", mode);

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

toggleBtn.addEventListener("click", function (e) {
  e.preventDefault();

  const isDark = document.body.classList.contains("dark-mode");

  setTheme(isDark ? "light" : "dark");
});

document.addEventListener("DOMContentLoaded", function () {
  const icon = document.getElementById("themeIcon");

  if (document.body.classList.contains("dark-mode")) {
    icon.classList.replace("fa-moon", "fa-sun");
  }
});
