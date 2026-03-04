(function () {
  "use strict";

  var isScrollBound = false;

  function getButtons() {
    return Array.prototype.slice.call(
      document.querySelectorAll("[data-df-backtotop]")
    );
  }

  function updateButtons() {
    var buttons = getButtons();

    if (!buttons.length) {
      return;
    }

    var isVisible = window.scrollY > 260;

    buttons.forEach(function (button) {
      button.classList.toggle("is-visible", isVisible);
    });
  }

  function bindButton(button) {
    if (button.dataset.dfBound === "1") {
      return;
    }

    button.dataset.dfBound = "1";
    button.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  function initBackToTop(scope) {
    var root = scope && scope[0] ? scope[0] : document;
    var buttons = Array.prototype.slice.call(
      root.querySelectorAll("[data-df-backtotop]")
    );

    if (!buttons.length) {
      return;
    }

    buttons.forEach(function (button) {
      bindButton(button);
    });

    updateButtons();

    if (!isScrollBound) {
      window.addEventListener("scroll", updateButtons);
      isScrollBound = true;
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initBackToTop();
    });
  } else {
    initBackToTop();
  }

  if (
    window.elementorFrontend &&
    window.elementorFrontend.hooks &&
    typeof window.elementorFrontend.hooks.addAction === "function"
  ) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/dope_footer_widget.default",
      initBackToTop
    );
  }
})();
