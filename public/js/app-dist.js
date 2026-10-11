(() => {
  // theme/js/components/link-filters.js
  var FILTER_DEFAULTS = {
    collection: "all",
    flag: "all",
    importance: "all",
    sort: "title"
  };
  function initLinkFilters() {
    const form = document.querySelector("[data-link-filters]");
    if (!(form instanceof HTMLFormElement)) {
      return;
    }
    const search = form.elements.namedItem("q");
    const resetFilters = () => {
      Object.entries(FILTER_DEFAULTS).forEach(([name, value]) => {
        const select = form.elements.namedItem(name);
        if (select instanceof HTMLSelectElement) {
          select.value = value;
        }
      });
    };
    form.querySelectorAll("select").forEach((select) => {
      select.addEventListener("change", () => {
        if (search instanceof HTMLInputElement) {
          search.value = "";
        }
        form.requestSubmit();
      });
    });
    if (!(search instanceof HTMLInputElement)) {
      return;
    }
    form.addEventListener("submit", () => {
      if (search.value.trim() !== "") {
        resetFilters();
      }
    });
    search.addEventListener("input", () => {
      if (search.value === "") {
        form.requestSubmit();
      }
    });
  }

  // theme/js/app.js
  initLinkFilters();
})();
