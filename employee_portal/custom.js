// const toggleBtn = document.getElementById("darkToggle");
// const icon = document.getElementById("themeIcon");

// function setTheme(mode) {
//   icon.classList.add("animate");

//   setTimeout(() => {
//     if (mode === "dark") {
//       document.body.classList.add("dark-mode");
//       icon.classList.remove("fa-moon");
//       icon.classList.add("fa-sun");
//       localStorage.setItem("theme", "dark");
//     } else {
//       document.body.classList.remove("dark-mode");
//       icon.classList.remove("fa-sun");
//       icon.classList.add("fa-moon");
//       localStorage.setItem("theme", "light");
//     }

//     // Reset animation
//     icon.classList.remove("animate");
//     icon.style.transform = "rotate(0) scale(1)";
//     icon.style.opacity = "1";
//   }, 200);
// }

// // Toggle on click
// toggleBtn.addEventListener("click", function (e) {
//   e.preventDefault();

//   if (document.body.classList.contains("dark-mode")) {
//     setTheme("light");
//   } else {
//     setTheme("dark");
//   }
// });

// // Load saved theme on page load
// const savedTheme = localStorage.getItem("theme") || "light";
// if (savedTheme === "dark") {
//   document.body.classList.add("dark-mode");
//   icon.classList.replace("fa-moon", "fa-sun");
// }
const toggleBtn = document.getElementById("darkToggle");
const icon = document.getElementById("themeIcon");

function setTheme(mode, animate = true) {
  if (animate) {
    // Remove animation class if it exists
    icon.classList.remove("animate");

    // FORCE browser reflow (this is the key fix)
    void icon.offsetWidth;

    // Start animation
    icon.classList.add("animate");
  }

  setTimeout(() => {
    if (mode === "dark") {
      document.body.classList.add("dark-mode");
      icon.classList.replace("fa-moon", "fa-sun");
      localStorage.setItem("theme", "dark");
    } else {
      document.body.classList.remove("dark-mode");
      icon.classList.replace("fa-sun", "fa-moon");
      localStorage.setItem("theme", "light");
    }

    // End animation
    icon.classList.remove("animate");
  }, 200);
}

// Toggle on click
toggleBtn.addEventListener("click", function (e) {
  e.preventDefault();

  const isDark = document.body.classList.contains("dark-mode");
  setTheme(isDark ? "light" : "dark");
});

// Load saved theme WITHOUT animation
const savedTheme = localStorage.getItem("theme") || "light";
setTheme(savedTheme, false);
