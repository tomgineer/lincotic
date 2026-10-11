(() => {
  // theme/js/app.js
  var linkFilters = document.querySelector("[data-link-filters]");
  if (linkFilters instanceof HTMLFormElement) {
    linkFilters.querySelectorAll("select").forEach((select) => {
      select.addEventListener("change", () => linkFilters.requestSubmit());
    });
  }
})();
