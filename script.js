document.getElementById("getStarted").addEventListener("click", () => {
  const container = document.querySelector(".welcome-container");
  container.classList.add("fade-out");

  // Wait for animation to finish, then navigate
  setTimeout(() => {
    window.location.href = "portal.html";
  }, 1000);
});
