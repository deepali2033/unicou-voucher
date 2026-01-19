const hamburger = document.getElementById("hamburger");
const nav = document.getElementById("nav");
const submenuParent = document.querySelector(".has-submenu");

hamburger.addEventListener("click", () => {
  nav.classList.toggle("active");
  hamburger.classList.toggle("active");
});

submenuParent.addEventListener("click", () => {
  submenuParent.classList.toggle("active");
});
