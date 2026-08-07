// Wrap in DOMContentLoaded to ensure DOM is fully ready
document.addEventListener("DOMContentLoaded", function () {
  console.log("[Theme Toggle] DOMContentLoaded fired");

  const toggleBtn = document.getElementById("darkToggle");
  const icon = document.getElementById("themeIcon");

  console.log("[Theme Toggle] Elements found:", {
    toggleBtn: !!toggleBtn,
    icon: !!icon,
  });

  // Only proceed if elements exist
  if (!toggleBtn || !icon) {
    console.error("[Theme Toggle] ERROR: Theme toggle elements not found!");
    return;
  }

  function setTheme(mode, animate = true) {
    console.log("[Theme Toggle] Setting theme to:", mode, "animate:", animate);

    if (animate && icon) {
      // Remove animation class if it exists
      icon.classList.remove("animate");

      // FORCE browser reflow
      void icon.offsetWidth;

      // Start animation
      icon.classList.add("animate");
    }

    setTimeout(() => {
      if (mode === "dark") {
        document.body.classList.add("dark-mode");
        if (icon) icon.classList.replace("fa-moon", "fa-sun");
        localStorage.setItem("theme", "dark");
        // Persist to database
        saveThemeToDatabase("dark");
      } else {
        document.body.classList.remove("dark-mode");
        if (icon) icon.classList.replace("fa-sun", "fa-moon");
        localStorage.setItem("theme", "light");
        // Persist to database
        saveThemeToDatabase("light");
      }

      // End animation
      if (icon) icon.classList.remove("animate");
      console.log("[Theme Toggle] Theme applied:", mode);
    }, 200);
  }

  // Function to save theme to database
  function saveThemeToDatabase(theme) {
    fetch("../../auth/saveTheme.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ theme: theme }),
    })
      .then((response) => response.json())
      .catch((error) =>
        console.log(
          "[Theme Toggle] Database save skipped (optional):",
          error.message,
        ),
      );
  }

  // Toggle on click
  toggleBtn.addEventListener("click", function (e) {
    console.log("[Theme Toggle] Button clicked!");
    e.preventDefault();
    e.stopPropagation();

    const isDark = document.body.classList.contains("dark-mode");
    setTheme(isDark ? "light" : "dark");
  });

  // Make button focusable for better UX
  toggleBtn.setAttribute("tabindex", "0");
  toggleBtn.style.cursor = "pointer";

  // Load saved theme WITHOUT animation
  const savedTheme = localStorage.getItem("theme") || "light";
  console.log("[Theme Toggle] Saved theme from localStorage:", savedTheme);
  setTheme(savedTheme, false);
});
