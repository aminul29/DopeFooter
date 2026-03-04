(function () {
  "use strict";

  function updateButtons(buttons) {
    var isVisible = window.scrollY > 260;

    buttons.forEach(function (button) {
      button.classList.toggle("is-visible", isVisible);
    });
  }

  function initBackToTop() {
    var buttons = Array.prototype.slice.call(
      document.querySelectorAll("[data-df-backtotop]")
    );

    if (!buttons.length) {
      return;
    }

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        window.scrollTo({
          top: 0,
          behavior: "smooth",
        });
      });
    });

    updateButtons(buttons);
    window.addEventListener("scroll", function () {
      updateButtons(buttons);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initBackToTop);
  } else {
    initBackToTop();
  }
})();

