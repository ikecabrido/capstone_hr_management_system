function updateClock() {
  const now = new Date();
  let h = now.getHours();
  let m = now.getMinutes();
  let s = now.getSeconds();

  const ampm = h >= 12 ? "PM" : "AM";
  h = h % 12 || 12;

  m = m < 10 ? "0" + m : m;
  s = s < 10 ? "0" + s : s;

  document.getElementById("clock").textContent = `${h}:${m}:${s} ${ampm}`;
}

setInterval(updateClock, 1000);
updateClock();
