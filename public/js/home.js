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


// education_cards.blade start
const tabs = document.querySelectorAll(".exam-tabs span");
const cards = document.querySelectorAll(".exam-card");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {

    tabs.forEach(t => t.classList.remove("active"));
    tab.classList.add("active");

    const filter = tab.dataset.filter;

    cards.forEach(card => {
      const category = card.dataset.category;

      if (filter === "All" || category.includes(filter)) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });
});
// education_cards.blade end