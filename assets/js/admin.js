(function () {
  "use strict";

  function copyWithFallback(text) {
    var input = document.createElement("input");
    input.value = text;
    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, 99999);
    var ok = false;

    try {
      ok = document.execCommand("copy");
    } catch (err) {
      ok = false;
    }

    document.body.removeChild(input);
    return ok;
  }

  function setFeedback(feedback, message, isError) {
    if (!feedback) {
      return;
    }

    feedback.textContent = message;
    feedback.classList.toggle("is-error", !!isError);
  }

  function copyTextToClipboard(text, onSuccess, onError) {
    if (!text) {
      if (typeof onError === "function") {
        onError();
      }
      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function () {
        if (typeof onSuccess === "function") {
          onSuccess();
        }
      }).catch(function () {
        if (copyWithFallback(text)) {
          if (typeof onSuccess === "function") {
            onSuccess();
          }
          return;
        }

        if (typeof onError === "function") {
          onError();
        }
      });
      return;
    }

    if (copyWithFallback(text)) {
      if (typeof onSuccess === "function") {
        onSuccess();
      }
      return;
    }

    if (typeof onError === "function") {
      onError();
    }
  }

  function initCopyShortcode() {
    var input = document.getElementById("dope-footer-shortcode-field");
    var button = document.getElementById("dope-footer-copy-shortcode");
    var feedback = document.getElementById("dope-footer-copy-feedback");

    if (!input || !button || !feedback) {
      return;
    }

    button.addEventListener("click", function () {
      var copyLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.copyLabel) || "Copy";
      var copiedLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.copiedLabel) || "Copied";
      var successText = (window.dopeFooterAdmin && window.dopeFooterAdmin.copySuccess) || "Shortcode copied to clipboard.";
      var errorText = (window.dopeFooterAdmin && window.dopeFooterAdmin.copyError) || "Unable to copy shortcode.";
      var value = input.value;

      if (!value) {
        setFeedback(feedback, errorText, true);
        return;
      }

      var onSuccess = function () {
        var labelNode = button.querySelector(".dope-footer-copy-label");
        if (labelNode) {
          labelNode.textContent = copiedLabel;
        }
        setFeedback(feedback, successText, false);

        window.setTimeout(function () {
          if (labelNode) {
            labelNode.textContent = copyLabel;
          }
        }, 1200);
      };

      var onError = function () {
        setFeedback(feedback, errorText, true);
      };

      copyTextToClipboard(value, onSuccess, onError);
    });
  }

  function initFooterListShortcodeCopy() {
    var buttons = document.querySelectorAll(".dope-footer-list-copy");
    if (!buttons.length) {
      return;
    }

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        var copyLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.copyLabel) || "Copy";
        var copiedLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.copiedLabel) || "Copied";
        var successText = (window.dopeFooterAdmin && window.dopeFooterAdmin.copySuccess) || "Shortcode copied to clipboard.";
        var errorText = (window.dopeFooterAdmin && window.dopeFooterAdmin.copyError) || "Unable to copy shortcode.";
        var value = button.getAttribute("data-shortcode") || "";
        var feedbackId = button.getAttribute("aria-describedby") || "";
        var feedback = feedbackId ? document.getElementById(feedbackId) : null;

        var onSuccess = function () {
          var labelNode = button.querySelector(".dope-footer-copy-label");
          if (labelNode) {
            labelNode.textContent = copiedLabel;
          }
          setFeedback(feedback, successText, false);

          window.setTimeout(function () {
            if (labelNode) {
              labelNode.textContent = copyLabel;
            }
          }, 1200);
        };

        var onError = function () {
          setFeedback(feedback, errorText, true);
        };

        copyTextToClipboard(value, onSuccess, onError);
      });
    });
  }

  function initPublishCard() {
    var saveDraftBtn = document.getElementById("dope-footer-save-draft");
    var publishBtn = document.getElementById("dope-footer-publish-now");
    var statusNode = document.getElementById("dope-footer-status-value");
    var topSaveButton = document.querySelector(".csf-buttons .csf-save");
    var editLinks = document.querySelectorAll(".dope-footer-inline-edit");

    if (!saveDraftBtn || !publishBtn || !topSaveButton) {
      return;
    }

    editLinks.forEach(function (link) {
      link.addEventListener("click", function (event) {
        event.preventDefault();
      });
    });

    function toggleState(isSaving) {
      saveDraftBtn.disabled = !!isSaving;
      publishBtn.disabled = !!isSaving;
      saveDraftBtn.classList.toggle("is-busy", !!isSaving);
      publishBtn.classList.toggle("is-busy", !!isSaving);
    }

    function runSave() {
      var savingLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.savingLabel) || "Saving...";
      var savedLabel = (window.dopeFooterAdmin && window.dopeFooterAdmin.savedLabel) || "Draft";

      toggleState(true);

      if (statusNode) {
        statusNode.textContent = savingLabel;
      }

      topSaveButton.click();

      window.setTimeout(function () {
        toggleState(false);
        if (statusNode) {
          statusNode.textContent = savedLabel;
        }
      }, 1200);
    }

    saveDraftBtn.addEventListener("click", runSave);
    publishBtn.addEventListener("click", runSave);
  }

  function mountHowToWidget() {
    var sidebar = document.getElementById("dope-footer-sidebar");
    if (!sidebar) {
      return;
    }

    var optionsPage = document.querySelector(".csf-options");
    if (optionsPage) {
      optionsPage.classList.add("dope-footer-has-sidebar");
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      mountHowToWidget();
      initCopyShortcode();
      initFooterListShortcodeCopy();
      initPublishCard();
    });
  } else {
    mountHowToWidget();
    initCopyShortcode();
    initFooterListShortcodeCopy();
    initPublishCard();
  }
})();
