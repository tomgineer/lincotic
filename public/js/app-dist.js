(() => {
  // theme/js/lib/clipboard.js
  var resetTimers = /* @__PURE__ */ new WeakMap();
  async function copyInputValue(input) {
    if (navigator.clipboard?.writeText) {
      try {
        await navigator.clipboard.writeText(input.value);
        return;
      } catch {
      }
    }
    input.focus({ preventScroll: true });
    input.select();
    input.setSelectionRange(0, input.value.length);
    if (!document.execCommand("copy")) {
      throw new Error("Copy failed");
    }
    input.setSelectionRange(0, 0);
  }
  function showCopyFeedback(root, button, status, message) {
    root.querySelectorAll('[data-copied="true"]').forEach((copiedButton) => {
      copiedButton.removeAttribute("data-copied");
    });
    button.dataset.copied = "true";
    if (status instanceof HTMLElement) {
      status.textContent = message;
    }
    const activeTimer = resetTimers.get(button);
    window.clearTimeout(activeTimer);
    resetTimers.set(button, window.setTimeout(() => {
      button.removeAttribute("data-copied");
      resetTimers.delete(button);
    }, 1600));
  }

  // theme/js/components/datetime.js
  var SECOND = 1e3;
  var MINUTE = 60 * SECOND;
  var HOUR = 60 * MINUTE;
  var DAY = 24 * HOUR;
  var DEBT_BASELINES = {
    "debt-us": {
      date: /* @__PURE__ */ new Date("2025-10-23T00:00:00Z"),
      amount: 38e12,
      perSecond: 63e3,
      currency: "USD"
    },
    "debt-de": {
      date: /* @__PURE__ */ new Date("2025-03-31T00:00:00Z"),
      amount: 252e10,
      perSecond: 1800,
      currency: "EUR"
    },
    "debt-gr": {
      date: /* @__PURE__ */ new Date("2025-06-30T00:00:00Z"),
      amount: 403e9,
      perSecond: 160,
      currency: "EUR"
    }
  };
  var integerFormatter = new Intl.NumberFormat("en-US", { maximumFractionDigits: 0 });
  function isoWeek(date) {
    const utcDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const weekday = utcDate.getUTCDay() || 7;
    utcDate.setUTCDate(utcDate.getUTCDate() + 4 - weekday);
    const yearStart = new Date(Date.UTC(utcDate.getUTCFullYear(), 0, 1));
    return Math.ceil(((utcDate - yearStart) / DAY + 1) / 7);
  }
  function dayOfYear(date) {
    const currentDay = Date.UTC(date.getFullYear(), date.getMonth(), date.getDate());
    const yearStart = Date.UTC(date.getFullYear(), 0, 1);
    return Math.floor((currentDay - yearStart) / DAY) + 1;
  }
  function elapsedParts(start, end) {
    const difference = Math.max(0, end - start);
    return {
      years: Math.floor(difference / (365.25 * DAY)),
      days: Math.floor(difference / DAY),
      hours: Math.floor(difference / HOUR),
      minutes: Math.floor(difference / MINUTE),
      seconds: Math.floor(difference / SECOND)
    };
  }
  function debtEstimate(baseline, now) {
    const elapsedSeconds = (now - baseline.date) / SECOND;
    const amount = baseline.amount + baseline.perSecond * elapsedSeconds;
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: baseline.currency,
      maximumFractionDigits: 0
    }).format(amount);
  }
  function metricValue(key, now) {
    const startOfYear = new Date(now.getFullYear(), 0, 1);
    const startOfNextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
    const startOfNextYear = new Date(now.getFullYear() + 1, 0, 1);
    const untilReset = elapsedParts(now, /* @__PURE__ */ new Date("2030-01-01T00:00:00Z"));
    const sinceEvent = elapsedParts(/* @__PURE__ */ new Date("2018-06-11T10:30:00Z"), now);
    const monthRemaining = elapsedParts(now, startOfNextMonth);
    const yearRemaining = elapsedParts(now, startOfNextYear);
    const yearElapsed = elapsedParts(startOfYear, now);
    const values = {
      "current-time": now.toLocaleTimeString("en-GB"),
      "current-date": now.toLocaleDateString("en-GB", {
        weekday: "long",
        day: "2-digit",
        month: "long",
        year: "numeric"
      }),
      "current-week": `Week ${isoWeek(now)}`,
      "current-timestamp": String(Math.floor(now.getTime() / SECOND)),
      "day-of-year": integerFormatter.format(dayOfYear(now)),
      "minute-of-year": integerFormatter.format(yearElapsed.minutes),
      "second-of-year": integerFormatter.format(yearElapsed.seconds),
      "reset-days": integerFormatter.format(untilReset.days),
      "reset-hours": integerFormatter.format(untilReset.hours),
      "reset-seconds": integerFormatter.format(untilReset.seconds),
      "month-days": integerFormatter.format(monthRemaining.days),
      "month-hours": integerFormatter.format(monthRemaining.hours),
      "month-seconds": integerFormatter.format(monthRemaining.seconds),
      "year-days": integerFormatter.format(yearRemaining.days),
      "year-hours": integerFormatter.format(yearRemaining.hours),
      "year-seconds": integerFormatter.format(yearRemaining.seconds),
      "event-years": integerFormatter.format(sinceEvent.years),
      "event-days": integerFormatter.format(sinceEvent.days),
      "event-seconds": integerFormatter.format(sinceEvent.seconds),
      "unix-seconds": String(Math.floor(now.getTime() / SECOND))
    };
    if (DEBT_BASELINES[key]) {
      return debtEstimate(DEBT_BASELINES[key], now);
    }
    return values[key] ?? "\u2014";
  }
  function initDateTime() {
    const root = document.querySelector("[data-datetime-utility]");
    if (!(root instanceof HTMLElement)) {
      return;
    }
    const heroTime = root.querySelector("[data-datetime-hero-time]");
    const timezone = root.querySelector("[data-datetime-timezone]");
    const status = root.querySelector("[data-datetime-status]");
    const fields = root.querySelectorAll("[data-datetime-value]");
    if (timezone instanceof HTMLElement) {
      timezone.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone || "Local timezone";
    }
    const update = () => {
      const now = /* @__PURE__ */ new Date();
      if (heroTime instanceof HTMLTimeElement) {
        heroTime.dateTime = now.toISOString();
        heroTime.textContent = now.toLocaleTimeString("en-GB");
      }
      fields.forEach((field) => {
        if (field instanceof HTMLInputElement) {
          field.value = metricValue(field.dataset.datetimeValue, now);
        }
      });
    };
    root.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-copy-datetime]");
      if (!(button instanceof HTMLButtonElement)) {
        return;
      }
      const input = button.closest("[data-copy-entry]")?.querySelector("[data-datetime-value]");
      if (!(input instanceof HTMLInputElement)) {
        return;
      }
      try {
        await copyInputValue(input);
        showCopyFeedback(root, button, status, `${input.value} copied.`);
      } catch {
        if (status instanceof HTMLElement) {
          status.textContent = "Unable to copy this value. Select it and copy manually.";
        }
      }
    });
    update();
    window.setInterval(update, SECOND);
  }

  // theme/js/components/hashing.js
  function initHashing() {
    const root = document.querySelector("[data-hashing-utility]");
    if (!(root instanceof HTMLElement)) {
      return;
    }
    const input = root.querySelector("[data-hash-input]");
    const trim = root.querySelector("[data-hash-trim]");
    const status = root.querySelector("[data-hash-status]");
    const byteCount = root.querySelector("[data-hash-byte-count]");
    const outputs = root.querySelectorAll("[data-hash-output]");
    const endpoint = root.dataset.hashUrl;
    const csrfHeader = root.dataset.csrfHeader;
    let csrfToken = root.dataset.csrfToken;
    let debounceTimer;
    let activeRequest;
    if (!(input instanceof HTMLTextAreaElement) || !endpoint || !csrfHeader || !csrfToken) {
      return;
    }
    const clearOutputs = () => {
      outputs.forEach((output) => {
        if (output instanceof HTMLInputElement) {
          output.value = "";
          output.closest("[data-copy-entry]")?.querySelector("[data-copy-hash]")?.setAttribute("disabled", "");
        }
      });
    };
    const updateByteCount = () => {
      if (byteCount instanceof HTMLElement) {
        const bytes = new TextEncoder().encode(input.value).length;
        byteCount.textContent = `${bytes.toLocaleString("en-US")} ${bytes === 1 ? "byte" : "bytes"}`;
      }
    };
    const generate = async () => {
      const value = trim instanceof HTMLInputElement && trim.checked ? input.value.trim() : input.value;
      updateByteCount();
      if (value === "") {
        activeRequest?.abort();
        clearOutputs();
        if (status instanceof HTMLElement) {
          status.textContent = "Waiting for input.";
        }
        return;
      }
      activeRequest?.abort();
      activeRequest = new AbortController();
      if (status instanceof HTMLElement) {
        status.textContent = "Generating hashes\u2026";
      }
      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            [csrfHeader]: csrfToken
          },
          body: JSON.stringify({ value }),
          signal: activeRequest.signal
        });
        const result = await response.json();
        if (!response.ok || !result.hashes) {
          throw new Error(result.error || "Unable to generate hashes.");
        }
        if (typeof result.csrf === "string" && result.csrf !== "") {
          csrfToken = result.csrf;
          root.dataset.csrfToken = result.csrf;
        }
        outputs.forEach((output) => {
          if (!(output instanceof HTMLInputElement)) {
            return;
          }
          output.value = result.hashes[output.dataset.hashOutput] ?? "";
          const button = output.closest("[data-copy-entry]")?.querySelector("[data-copy-hash]");
          if (button instanceof HTMLButtonElement) {
            button.disabled = output.value === "";
          }
        });
        if (status instanceof HTMLElement) {
          status.textContent = "All five hashes are ready.";
        }
      } catch (error) {
        if (error.name === "AbortError") {
          return;
        }
        clearOutputs();
        if (status instanceof HTMLElement) {
          status.textContent = error.message || "Unable to generate hashes.";
        }
      }
    };
    const scheduleGeneration = (delay = 220) => {
      window.clearTimeout(debounceTimer);
      debounceTimer = window.setTimeout(generate, delay);
    };
    input.addEventListener("input", () => {
      updateByteCount();
      scheduleGeneration();
    });
    if (trim instanceof HTMLInputElement) {
      trim.addEventListener("change", () => scheduleGeneration(0));
    }
    root.addEventListener("click", async (event) => {
      const button = event.target.closest("[data-copy-hash]");
      if (!(button instanceof HTMLButtonElement)) {
        return;
      }
      const output = button.closest("[data-copy-entry]")?.querySelector("[data-hash-output]");
      if (!(output instanceof HTMLInputElement) || output.value === "") {
        return;
      }
      try {
        await copyInputValue(output);
        showCopyFeedback(root, button, status, `${button.dataset.copyHash.toUpperCase()} copied.`);
      } catch {
        if (status instanceof HTMLElement) {
          status.textContent = "Unable to copy this hash. Select it and copy manually.";
        }
      }
    });
    updateByteCount();
  }

  // theme/js/components/link-filters.js
  function initLinkFilters() {
    const filters = document.querySelector("[data-link-filters]");
    if (filters instanceof HTMLFormElement) {
      filters.querySelectorAll("select").forEach((select) => {
        select.addEventListener("change", () => filters.requestSubmit());
      });
    }
  }

  // theme/js/components/link-search.js
  function initLinkSearch() {
    const search = document.querySelector("[data-link-search-form]");
    if (!(search instanceof HTMLFormElement)) {
      return;
    }
    const input = search.elements.namedItem("q");
    const clearSearch = search.querySelector("[data-clear-search]");
    if (!(input instanceof HTMLInputElement)) {
      return;
    }
    if (clearSearch instanceof HTMLButtonElement) {
      input.addEventListener("input", () => {
        clearSearch.disabled = input.value === "";
      });
      clearSearch.addEventListener("click", () => {
        input.value = "";
        search.requestSubmit();
      });
    }
    if (input.autofocus) {
      requestAnimationFrame(() => {
        input.focus({ preventScroll: true });
        input.setSelectionRange(input.value.length, input.value.length);
      });
    }
  }

  // theme/js/components/password-generator.js
  function initPasswordGenerator() {
    const generator = document.querySelector("[data-password-generator]");
    if (!(generator instanceof HTMLElement)) {
      return;
    }
    const status = generator.querySelector("[data-copy-status]");
    generator.querySelectorAll("[data-copy-password]").forEach((button) => {
      if (!(button instanceof HTMLButtonElement)) {
        return;
      }
      button.addEventListener("click", async () => {
        const entry = button.closest("[data-password-entry]");
        const input = entry?.querySelector("[data-password-value]");
        if (!(input instanceof HTMLInputElement)) {
          return;
        }
        try {
          await copyInputValue(input);
          showCopyFeedback(
            generator,
            button,
            status,
            `${input.value.length} character password copied.`
          );
        } catch {
          if (status instanceof HTMLElement) {
            status.textContent = "Unable to copy the password. Select it and copy manually.";
          }
        }
      });
    });
  }

  // theme/js/components/worktime.js
  var STORAGE_KEY = "lincotic.workTimeStartAt";
  var SECOND2 = 1e3;
  var MINUTE2 = 60 * SECOND2;
  var HOUR2 = 60 * MINUTE2;
  var TARGET_NET_HOURS = 8;
  var pad = (value) => String(value).padStart(2, "0");
  function formatDuration(milliseconds) {
    const totalSeconds = Math.floor(Math.max(0, milliseconds) / SECOND2);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor(totalSeconds % 3600 / 60);
    const seconds = totalSeconds % 60;
    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
  }
  function formatTime(date) {
    return `${pad(date.getHours())}:${pad(date.getMinutes())}`;
  }
  function breakDuration(elapsed) {
    let minutes = 0;
    if (elapsed >= 6 * HOUR2) {
      minutes += 30;
    }
    if (elapsed >= 9 * HOUR2) {
      minutes += 15;
    }
    return minutes * MINUTE2;
  }
  function parseStartTime(value) {
    if (!/^\d{2}:\d{2}$/.test(value)) {
      return null;
    }
    const [hours, minutes] = value.split(":").map(Number);
    if (hours > 23 || minutes > 59) {
      return null;
    }
    const now = /* @__PURE__ */ new Date();
    const start = new Date(now);
    start.setHours(hours, minutes, 0, 0);
    if (start - now > 12 * HOUR2) {
      start.setDate(start.getDate() - 1);
    } else if (start > now) {
      return null;
    }
    return start;
  }
  function storedStartTime() {
    try {
      const value = localStorage.getItem(STORAGE_KEY);
      const date = value ? new Date(value) : null;
      return date && !Number.isNaN(date.getTime()) ? date : null;
    } catch {
      return null;
    }
  }
  function initWorkTime() {
    const root = document.querySelector("[data-worktime-utility]");
    if (!(root instanceof HTMLElement)) {
      return;
    }
    const input = root.querySelector("[data-worktime-start]");
    const save = root.querySelector("[data-worktime-save]");
    const reset = root.querySelector("[data-worktime-reset]");
    const display = root.querySelector("[data-worktime-display]");
    const progress = root.querySelector("[data-worktime-progress]");
    const progressText = root.querySelector("[data-worktime-progress-text]");
    const percent = root.querySelector("[data-worktime-percent]");
    const end = root.querySelector("[data-worktime-end]");
    const breakOutput = root.querySelector("[data-worktime-break]");
    const state = root.querySelector("[data-worktime-state]");
    let startTime = storedStartTime();
    let persisted = startTime !== null;
    let timer;
    if (!(input instanceof HTMLInputElement) || !(save instanceof HTMLButtonElement)) {
      return;
    }
    const setText = (element, value) => {
      if (element instanceof HTMLElement) {
        element.textContent = value;
      }
    };
    const showIdle = () => {
      setText(display, "00:00:00");
      setText(progressText, "Set a start time to begin tracking.");
      setText(percent, "0%");
      setText(end, "--:--");
      setText(breakOutput, "0 min");
      setText(state, "No start time saved.");
      if (progress instanceof HTMLProgressElement) {
        progress.value = 0;
      }
    };
    const update = () => {
      if (!startTime) {
        showIdle();
        return;
      }
      const elapsed = Math.max(0, Date.now() - startTime.getTime());
      const deductedBreak = breakDuration(elapsed);
      const netWorked = Math.max(0, elapsed - deductedBreak);
      const target = TARGET_NET_HOURS * HOUR2;
      const progressValue = Math.min(100, netWorked / target * 100);
      const roundedProgress = Math.round(progressValue);
      const targetEnd = new Date(startTime.getTime() + 8 * HOUR2 + 30 * MINUTE2);
      setText(display, formatDuration(netWorked));
      setText(percent, `${roundedProgress}%`);
      setText(end, formatTime(targetEnd));
      setText(breakOutput, `${deductedBreak / MINUTE2} min`);
      setText(
        state,
        persisted ? `Started at ${formatTime(startTime)} \xB7 saved in this browser.` : `Started at ${formatTime(startTime)} \xB7 active in this tab only.`
      );
      if (progress instanceof HTMLProgressElement) {
        progress.value = progressValue;
      }
      if (roundedProgress >= 100) {
        setText(progressText, "Workday complete. Eight net hours reached.");
      } else if (roundedProgress >= 75) {
        setText(progressText, `Almost there\u2014${roundedProgress}% of your workday is complete.`);
      } else if (roundedProgress >= 50) {
        setText(progressText, `Good progress\u2014${roundedProgress}% of your workday is complete.`);
      } else {
        setText(progressText, `${roundedProgress}% of your eight-hour workday is complete.`);
      }
    };
    const run = () => {
      window.clearInterval(timer);
      update();
      timer = window.setInterval(update, SECOND2);
    };
    save.addEventListener("click", () => {
      const parsed = parseStartTime(input.value);
      if (!parsed) {
        setText(state, "Enter a valid start time.");
        return;
      }
      startTime = parsed;
      try {
        localStorage.setItem(STORAGE_KEY, startTime.toISOString());
        persisted = true;
      } catch {
        persisted = false;
      }
      run();
    });
    if (reset instanceof HTMLButtonElement) {
      reset.addEventListener("click", () => {
        startTime = null;
        persisted = false;
        window.clearInterval(timer);
        try {
          localStorage.removeItem(STORAGE_KEY);
        } catch {
        }
        showIdle();
      });
    }
    if (startTime) {
      input.value = formatTime(startTime);
      run();
    } else {
      showIdle();
    }
  }

  // theme/js/lib/confirmation.js
  function confirmDialog({
    title = "Are you sure?",
    message = "Please confirm that you want to continue.",
    confirmLabel = "Confirm"
  } = {}) {
    const dialog = document.querySelector("[data-confirmation-modal]");
    const titleElement = dialog?.querySelector("[data-confirmation-title]");
    const messageElement = dialog?.querySelector("[data-confirmation-message]");
    const confirmButton = dialog?.querySelector("[data-confirmation-confirm]");
    const cancelButtons = dialog?.querySelectorAll("[data-confirmation-cancel]");
    if (!(dialog instanceof HTMLDialogElement) || !(titleElement instanceof HTMLElement) || !(messageElement instanceof HTMLElement) || !(confirmButton instanceof HTMLButtonElement) || !cancelButtons?.length) {
      return Promise.resolve(false);
    }
    if (dialog.open) {
      return Promise.resolve(false);
    }
    titleElement.textContent = title;
    messageElement.textContent = message;
    confirmButton.textContent = confirmLabel;
    return new Promise((resolve) => {
      let settled = false;
      const finish = (confirmed) => {
        if (settled) return;
        settled = true;
        confirmButton.removeEventListener("click", confirm);
        cancelButtons.forEach((button) => button.removeEventListener("click", cancel));
        dialog.removeEventListener("close", cancel);
        dialog.removeEventListener("cancel", cancel);
        if (dialog.open) {
          dialog.close();
        }
        resolve(confirmed);
      };
      const confirm = () => finish(true);
      const cancel = (event) => {
        event?.preventDefault();
        finish(false);
      };
      confirmButton.addEventListener("click", confirm);
      cancelButtons.forEach((button) => button.addEventListener("click", cancel));
      dialog.addEventListener("close", cancel);
      dialog.addEventListener("cancel", cancel);
      dialog.showModal();
      confirmButton.focus();
    });
  }

  // theme/js/edit/edit-collection.js
  var editableControlSelector = "[data-edit-collection-control]";
  function formControl(form, name) {
    return form.elements.namedItem(name);
  }
  function setControlValue(form, name, value) {
    const control = formControl(form, name);
    if (control instanceof HTMLInputElement || control instanceof HTMLTextAreaElement) {
      control.value = value;
    }
  }
  function clearValidation(form) {
    form.querySelectorAll("[name]").forEach((control) => {
      control.setCustomValidity?.("");
    });
  }
  function applyValidation(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
      formControl(form, name)?.setCustomValidity?.(message);
    });
  }
  function initEditCollection() {
    const dialog = document.querySelector("[data-edit-collection-modal]");
    if (!(dialog instanceof HTMLDialogElement)) {
      return;
    }
    const form = dialog.querySelector("[data-edit-collection-form]");
    const modalTitle = dialog.querySelector("[data-edit-collection-title]");
    const modalDescription = dialog.querySelector("[data-edit-collection-description]");
    const status = dialog.querySelector("[data-edit-collection-status]");
    const saveButton = dialog.querySelector("[data-edit-collection-save]");
    const deleteButton = dialog.querySelector("[data-edit-collection-delete]");
    const createEndpoint = dialog.dataset.createEndpoint;
    const endpointTemplate = dialog.dataset.endpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let loadController = null;
    let loadSequence = 0;
    let mode = "edit";
    if (!(form instanceof HTMLFormElement) || !(modalTitle instanceof HTMLElement) || !(modalDescription instanceof HTMLElement) || !(saveButton instanceof HTMLButtonElement) || !createEndpoint || !endpointTemplate || !csrfHeader || !csrfToken) {
      return;
    }
    const endpointFor = (id) => endpointTemplate.replace(
      "__COLLECTION_ID__",
      encodeURIComponent(id)
    );
    const setStatus = (message, isError = false) => {
      if (!(status instanceof HTMLElement)) return;
      status.textContent = message;
      status.classList.toggle("text-error", isError);
      status.classList.toggle("text-base-content/60", !isError);
    };
    const defaultSaveLabel = () => mode === "create" ? "Add collection" : "Save changes";
    const setBusy = (busy, label = defaultSaveLabel()) => {
      form.setAttribute("aria-busy", String(busy));
      form.querySelectorAll(editableControlSelector).forEach((control) => {
        control.disabled = busy;
      });
      saveButton.disabled = busy;
      saveButton.textContent = label;
    };
    const setMode = (nextMode) => {
      mode = nextMode;
      const creating = mode === "create";
      modalTitle.textContent = creating ? "Add collection" : "Edit collection";
      modalDescription.textContent = creating ? "Create a new collection for organizing links." : "Update how this collection is named and presented.";
      if (deleteButton instanceof HTMLButtonElement) {
        deleteButton.hidden = creating;
      }
      saveButton.textContent = defaultSaveLabel();
    };
    const updateCsrf = (result) => {
      if (typeof result.csrf === "string" && result.csrf !== "") {
        csrfToken = result.csrf;
        dialog.dataset.csrfToken = result.csrf;
      }
    };
    const readResponse = async (response) => {
      let result = {};
      try {
        result = await response.json();
      } catch {
      }
      updateCsrf(result);
      return result;
    };
    const populateForm = (collection) => {
      setControlValue(form, "id", String(collection.id));
      setControlValue(form, "name", collection.name ?? "");
      setControlValue(form, "description", collection.description ?? "");
      setControlValue(form, "icon", collection.icon ?? "");
      const featured = formControl(form, "is_featured");
      const hidden = formControl(form, "is_hidden");
      if (featured instanceof HTMLInputElement) {
        featured.checked = Boolean(collection.is_featured);
      }
      if (hidden instanceof HTMLInputElement) {
        hidden.checked = Boolean(collection.is_hidden);
      }
    };
    const openEditor = async (trigger) => {
      if (!(trigger instanceof HTMLButtonElement) || !/^[1-9]\d*$/.test(trigger.dataset.collectionId ?? "")) {
        return;
      }
      loadController?.abort();
      loadController = new AbortController();
      const sequence = ++loadSequence;
      const id = trigger.dataset.collectionId;
      form.reset();
      clearValidation(form);
      setMode("edit");
      setStatus("Loading collection\u2026");
      setBusy(true, "Loading\u2026");
      if (!dialog.open) {
        dialog.showModal();
      }
      try {
        const response = await fetch(endpointFor(id), {
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest"
          },
          signal: loadController.signal
        });
        const result = await readResponse(response);
        if (sequence !== loadSequence) return;
        if (!response.ok || !result.collection) {
          throw new Error(result.error || "Unable to load this collection.");
        }
        populateForm(result.collection);
        setStatus("");
        setBusy(false);
        formControl(form, "name")?.focus();
      } catch (error) {
        if (error.name === "AbortError" || sequence !== loadSequence) return;
        setStatus(error.message || "Unable to load this collection.", true);
        setBusy(false);
      }
    };
    const openCreator = (trigger) => {
      if (!(trigger instanceof HTMLButtonElement)) {
        return;
      }
      loadSequence++;
      loadController?.abort();
      loadController = null;
      form.reset();
      clearValidation(form);
      setMode("create");
      setControlValue(form, "id", "");
      setStatus("");
      setBusy(false);
      if (!dialog.open) {
        dialog.showModal();
      }
      formControl(form, "name")?.focus();
    };
    document.querySelectorAll("[data-edit-collection]").forEach((trigger) => {
      trigger.addEventListener("click", () => void openEditor(trigger));
    });
    document.querySelectorAll("[data-add-collection]").forEach((trigger) => {
      trigger.addEventListener("click", () => openCreator(trigger));
    });
    form.addEventListener("input", () => clearValidation(form));
    if (deleteButton instanceof HTMLButtonElement) {
      deleteButton.addEventListener("click", async () => {
        if (mode !== "edit") {
          return;
        }
        const idControl = formControl(form, "id");
        const nameControl = formControl(form, "name");
        if (!(idControl instanceof HTMLInputElement) || idControl.value === "") {
          setStatus("No collection is selected.", true);
          return;
        }
        const name = nameControl instanceof HTMLInputElement ? nameControl.value.trim() : "";
        const confirmed = await confirmDialog({
          title: "Delete collection?",
          message: name ? `The collection \u201C${name}\u201D will be removed. Do you want to continue?` : "This collection will be removed. Do you want to continue?",
          confirmLabel: "Delete collection"
        });
        if (!confirmed) return;
        setStatus("Deleting collection\u2026");
        setBusy(true);
        try {
          const response = await fetch(endpointFor(idControl.value), {
            method: "DELETE",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
              [csrfHeader]: csrfToken
            }
          });
          const result = await readResponse(response);
          if (!response.ok) {
            throw new Error(result.error || "Unable to delete this collection.");
          }
          dialog.hidden = true;
          dialog.close();
          window.location.reload();
        } catch (error) {
          setStatus(error.message || "Unable to delete this collection.", true);
          setBusy(false);
        }
      });
    }
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      clearValidation(form);
      const data = new FormData(form);
      const id = data.get("id");
      const creating = mode === "create";
      if (!creating && (typeof id !== "string" || id === "")) {
        setStatus("No collection is selected.", true);
        return;
      }
      const payload = {
        name: data.get("name"),
        description: data.get("description"),
        icon: data.get("icon"),
        is_featured: data.get("is_featured") === "1",
        is_hidden: data.get("is_hidden") === "1"
      };
      setStatus(creating ? "Adding collection\u2026" : "Saving changes\u2026");
      setBusy(true, creating ? "Adding\u2026" : "Saving\u2026");
      try {
        const response = await fetch(
          creating ? createEndpoint : endpointFor(id),
          {
            method: "POST",
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              [csrfHeader]: csrfToken
            },
            body: JSON.stringify(payload)
          }
        );
        const result = await readResponse(response);
        if (!response.ok) {
          setBusy(false);
          if (result.errors && typeof result.errors === "object") {
            applyValidation(form, result.errors);
            form.reportValidity();
          }
          throw new Error(result.error || (creating ? "Unable to add this collection." : "Unable to save this collection."));
        }
        dialog.hidden = true;
        dialog.close();
        window.location.reload();
      } catch (error) {
        setStatus(error.message || (creating ? "Unable to add this collection." : "Unable to save this collection."), true);
        setBusy(false);
      }
    });
    dialog.addEventListener("close", () => {
      loadSequence++;
      loadController?.abort();
      loadController = null;
      form.reset();
      clearValidation(form);
      setStatus("");
      setMode("edit");
      setBusy(false);
    });
  }

  // theme/js/edit/edit-mode.js
  var editableLinkSelector = ".link-card";
  var editModeStorageKey = "lincotic.editMode";
  var editLinkRequestEvent = "lincotic:edit-link";
  function restoreEditMode() {
    try {
      return sessionStorage.getItem(editModeStorageKey) === "1";
    } catch {
      return false;
    }
  }
  function persistEditMode(enabled) {
    try {
      if (enabled) {
        sessionStorage.setItem(editModeStorageKey, "1");
      } else {
        sessionStorage.removeItem(editModeStorageKey);
      }
    } catch {
    }
  }
  function isVisible(element) {
    return !element.hidden && element.getClientRects().length > 0 && window.getComputedStyle(element).visibility !== "hidden";
  }
  function initEditMode() {
    const toggle = document.querySelector("[data-edit-mode-toggle]");
    if (!(toggle instanceof HTMLInputElement)) {
      return;
    }
    const syncEditableLinks = () => {
      document.querySelectorAll(editableLinkSelector).forEach((link) => {
        link.classList.toggle("edit-mode", toggle.checked && isVisible(link));
      });
    };
    toggle.checked = restoreEditMode();
    toggle.addEventListener("change", () => {
      persistEditMode(toggle.checked);
      syncEditableLinks();
    });
    document.addEventListener("click", (event) => {
      if (!toggle.checked || !(event.target instanceof Element)) {
        return;
      }
      const link = event.target.closest(editableLinkSelector);
      if (!(link instanceof HTMLAnchorElement) || !link.classList.contains("edit-mode")) {
        return;
      }
      event.preventDefault();
      link.dispatchEvent(new CustomEvent(editLinkRequestEvent, {
        bubbles: true,
        detail: { link }
      }));
    });
    syncEditableLinks();
  }

  // theme/js/edit/edit-link.js
  var editableControlSelector2 = "[data-edit-link-control]";
  function formControl2(form, name) {
    return form.elements.namedItem(name);
  }
  function setControlValue2(form, name, value) {
    const control = formControl2(form, name);
    if (control instanceof RadioNodeList) {
      control.value = value;
    } else if (control instanceof HTMLInputElement || control instanceof HTMLSelectElement || control instanceof HTMLTextAreaElement) {
      control.value = value;
    }
  }
  function setCheckboxValue(form, name, checked) {
    const control = formControl2(form, name);
    if (control instanceof HTMLInputElement) {
      control.checked = checked;
    }
  }
  function clearValidation2(form) {
    form.querySelectorAll("[name]").forEach((control) => {
      if (typeof control.setCustomValidity === "function") {
        control.setCustomValidity("");
      }
    });
  }
  function applyValidation2(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
      const control = formControl2(form, name);
      if (control instanceof RadioNodeList) {
        const firstControl = control[0];
        firstControl?.setCustomValidity?.(message);
      } else {
        control?.setCustomValidity?.(message);
      }
    });
  }
  function initEditLink() {
    const dialog = document.querySelector("[data-edit-link-modal]");
    if (!(dialog instanceof HTMLDialogElement)) {
      return;
    }
    const form = dialog.querySelector("[data-edit-link-form]");
    const modalTitle = dialog.querySelector("[data-edit-link-title]");
    const modalDescription = dialog.querySelector("[data-edit-link-description]");
    const status = dialog.querySelector("[data-edit-link-status]");
    const saveButton = dialog.querySelector("[data-edit-link-save]");
    const deleteButton = dialog.querySelector("[data-edit-link-delete]");
    const pasteUrlButton = dialog.querySelector("[data-edit-link-paste-url]");
    const openUrlButton = dialog.querySelector("[data-edit-link-open-url]");
    const createEndpoint = dialog.dataset.createEndpoint;
    const endpointTemplate = dialog.dataset.endpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let loadController = null;
    let loadSequence = 0;
    let mode = "edit";
    if (!(form instanceof HTMLFormElement) || !(modalTitle instanceof HTMLElement) || !(modalDescription instanceof HTMLElement) || !(saveButton instanceof HTMLButtonElement) || !createEndpoint || !endpointTemplate || !csrfHeader || !csrfToken) {
      return;
    }
    const setStatus = (message, isError = false) => {
      if (!(status instanceof HTMLElement)) {
        return;
      }
      status.textContent = message;
      status.classList.toggle("text-error", isError);
      status.classList.toggle("text-base-content/60", !isError);
    };
    const defaultSaveLabel = () => mode === "create" ? "Add link" : "Save changes";
    const setBusy = (busy, label = defaultSaveLabel()) => {
      form.setAttribute("aria-busy", String(busy));
      form.querySelectorAll(editableControlSelector2).forEach((control) => {
        control.disabled = busy;
      });
      saveButton.disabled = busy;
      saveButton.textContent = label;
    };
    const setMode = (nextMode) => {
      mode = nextMode;
      const creating = mode === "create";
      modalTitle.textContent = creating ? "Add link" : "Edit link";
      modalDescription.textContent = creating ? "Add a new destination." : "Update the destination, organization, and status of this link.";
      const collectionControl = formControl2(form, "collection_id");
      if (collectionControl instanceof HTMLSelectElement) {
        const unassignedOption = collectionControl.querySelector(
          "[data-edit-link-unassigned-option]"
        );
        if (creating) {
          const option = unassignedOption ?? new Option("[Unassigned]", "", true, true);
          option.dataset.editLinkUnassignedOption = "";
          if (!unassignedOption) {
            collectionControl.prepend(option);
          }
          option.selected = true;
          collectionControl.required = false;
        } else {
          unassignedOption?.remove();
          collectionControl.required = true;
        }
      }
      if (deleteButton instanceof HTMLButtonElement) {
        deleteButton.hidden = creating;
      }
      saveButton.textContent = defaultSaveLabel();
    };
    const updateCsrf = (result) => {
      if (typeof result.csrf === "string" && result.csrf !== "") {
        csrfToken = result.csrf;
        dialog.dataset.csrfToken = result.csrf;
      }
    };
    const endpointFor = (id) => endpointTemplate.replace("__LINK_ID__", encodeURIComponent(id));
    const readResponse = async (response) => {
      let result = {};
      try {
        result = await response.json();
      } catch {
      }
      updateCsrf(result);
      return result;
    };
    const populateForm = (link) => {
      setControlValue2(form, "id", String(link.id));
      setControlValue2(form, "title", link.title ?? "");
      setControlValue2(form, "url", link.url ?? "");
      setControlValue2(form, "collection_id", String(link.collection_id ?? ""));
      setControlValue2(form, "importance", link.importance ?? "");
      setControlValue2(form, "description", link.description ?? "");
      setCheckboxValue(form, "is_favorite", Boolean(link.is_favorite));
      setCheckboxValue(form, "is_pending_review", Boolean(link.is_pending_review));
      setCheckboxValue(form, "is_hidden", Boolean(link.is_hidden));
    };
    const openEditor = async (link) => {
      if (!(link instanceof HTMLAnchorElement) || !link.dataset.linkId) {
        return;
      }
      loadController?.abort();
      loadController = new AbortController();
      const sequence = ++loadSequence;
      const id = link.dataset.linkId;
      form.reset();
      clearValidation2(form);
      setMode("edit");
      setStatus("Loading link\u2026");
      setBusy(true, "Loading\u2026");
      if (!dialog.open) {
        dialog.showModal();
      }
      try {
        const response = await fetch(endpointFor(id), {
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest"
          },
          signal: loadController.signal
        });
        const result = await readResponse(response);
        if (sequence !== loadSequence) {
          return;
        }
        if (!response.ok || !result.link) {
          throw new Error(result.error || "Unable to load this link.");
        }
        populateForm(result.link);
        setStatus("");
        setBusy(false);
        formControl2(form, "title")?.focus();
      } catch (error) {
        if (error.name === "AbortError" || sequence !== loadSequence) {
          return;
        }
        setStatus(error.message || "Unable to load this link.", true);
        setBusy(false);
      }
    };
    const openCreator = (trigger) => {
      if (!(trigger instanceof HTMLButtonElement)) {
        return;
      }
      loadSequence++;
      loadController?.abort();
      loadController = null;
      form.reset();
      clearValidation2(form);
      setMode("create");
      setControlValue2(form, "id", "");
      const collectionId = trigger.dataset.collectionId ?? "";
      if (/^[1-9]\d*$/.test(collectionId)) {
        setControlValue2(form, "collection_id", collectionId);
      }
      setStatus("");
      setBusy(false);
      if (!dialog.open) {
        dialog.showModal();
      }
      formControl2(form, "title")?.focus();
    };
    document.addEventListener(editLinkRequestEvent, (event) => {
      if (!(event instanceof CustomEvent)) {
        return;
      }
      void openEditor(event.detail?.link);
    });
    document.querySelectorAll("[data-add-link]").forEach((trigger) => {
      trigger.addEventListener("click", () => openCreator(trigger));
    });
    form.addEventListener("input", () => clearValidation2(form));
    if (pasteUrlButton instanceof HTMLButtonElement) {
      pasteUrlButton.addEventListener("click", async () => {
        const urlControl = formControl2(form, "url");
        if (!(urlControl instanceof HTMLInputElement)) {
          return;
        }
        if (!navigator.clipboard?.readText) {
          setStatus("Clipboard access is not available in this browser.", true);
          return;
        }
        try {
          const clipboardValue = (await navigator.clipboard.readText()).trim();
          if (clipboardValue === "") {
            setStatus("The clipboard is empty.", true);
            return;
          }
          urlControl.value = clipboardValue;
          urlControl.dispatchEvent(new Event("input", { bubbles: true }));
          setStatus("");
          urlControl.focus();
        } catch {
          setStatus("Clipboard access was denied.", true);
          urlControl.focus();
        }
      });
    }
    if (openUrlButton instanceof HTMLButtonElement) {
      openUrlButton.addEventListener("click", () => {
        const urlControl = formControl2(form, "url");
        if (!(urlControl instanceof HTMLInputElement)) {
          return;
        }
        let url;
        try {
          url = new URL(urlControl.value.trim());
          if (!["http:", "https:"].includes(url.protocol)) {
            throw new TypeError("Unsupported URL protocol.");
          }
        } catch {
          urlControl.setCustomValidity("Enter a valid HTTP or HTTPS URL.");
          urlControl.reportValidity();
          urlControl.focus();
          return;
        }
        urlControl.setCustomValidity("");
        window.open(url.href, "_blank", "noopener,noreferrer");
      });
    }
    if (deleteButton instanceof HTMLButtonElement) {
      deleteButton.addEventListener("click", async () => {
        if (mode !== "edit") {
          return;
        }
        const idControl = formControl2(form, "id");
        const titleControl = formControl2(form, "title");
        if (!(idControl instanceof HTMLInputElement) || idControl.value === "") {
          setStatus("No link is selected.", true);
          return;
        }
        const title = titleControl instanceof HTMLInputElement ? titleControl.value.trim() : "";
        const confirmed = await confirmDialog({
          title: "Delete link?",
          message: title ? `The link \u201C${title}\u201D will be removed. Do you want to continue?` : "This link will be removed. Do you want to continue?",
          confirmLabel: "Delete link"
        });
        if (!confirmed) {
          return;
        }
        setStatus("Deleting link\u2026");
        setBusy(true);
        try {
          const response = await fetch(endpointFor(idControl.value), {
            method: "DELETE",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
              [csrfHeader]: csrfToken
            }
          });
          const result = await readResponse(response);
          if (!response.ok) {
            throw new Error(result.error || "Unable to delete this link.");
          }
          dialog.hidden = true;
          dialog.close();
          window.location.reload();
        } catch (error) {
          setStatus(error.message || "Unable to delete this link.", true);
          setBusy(false);
        }
      });
    }
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      clearValidation2(form);
      const data = new FormData(form);
      const id = data.get("id");
      const creating = mode === "create";
      if (!creating && (typeof id !== "string" || id === "")) {
        setStatus("No link is selected.", true);
        return;
      }
      const payload = {
        title: data.get("title"),
        url: data.get("url"),
        collection_id: data.get("collection_id"),
        importance: data.get("importance"),
        description: data.get("description"),
        is_favorite: data.get("is_favorite") === "1",
        is_pending_review: data.get("is_pending_review") === "1",
        is_hidden: data.get("is_hidden") === "1"
      };
      setStatus(creating ? "Adding link\u2026" : "Saving changes\u2026");
      setBusy(true, creating ? "Adding\u2026" : "Saving\u2026");
      try {
        const response = await fetch(
          creating ? createEndpoint : endpointFor(id),
          {
            method: "POST",
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              [csrfHeader]: csrfToken
            },
            body: JSON.stringify(payload)
          }
        );
        const result = await readResponse(response);
        if (!response.ok) {
          setBusy(false);
          if (result.errors && typeof result.errors === "object") {
            applyValidation2(form, result.errors);
            form.reportValidity();
          }
          throw new Error(result.error || (creating ? "Unable to add this link." : "Unable to save this link."));
        }
        dialog.hidden = true;
        dialog.close();
        window.location.reload();
      } catch (error) {
        setStatus(error.message || (creating ? "Unable to add this link." : "Unable to save this link."), true);
        setBusy(false);
      }
    });
    dialog.addEventListener("close", () => {
      loadSequence++;
      loadController?.abort();
      loadController = null;
      form.reset();
      clearValidation2(form);
      setStatus("");
      setMode("edit");
      setBusy(false);
    });
  }

  // theme/js/edit/edit-user.js
  var editableControlSelector3 = "[data-edit-user-control]";
  function formControl3(form, name) {
    return form.elements.namedItem(name);
  }
  function clearValidation3(form) {
    form.querySelectorAll("[name]").forEach((control) => {
      control.setCustomValidity?.("");
    });
  }
  function applyValidation3(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
      const control = formControl3(form, name);
      if (control instanceof RadioNodeList) {
        control[0]?.setCustomValidity?.(message);
      } else {
        control?.setCustomValidity?.(message);
      }
    });
  }
  function initEditUser() {
    const dialog = document.querySelector("[data-edit-user-modal]");
    if (!(dialog instanceof HTMLDialogElement)) {
      return;
    }
    const form = dialog.querySelector("[data-edit-user-form]");
    const status = dialog.querySelector("[data-edit-user-status]");
    const saveButton = dialog.querySelector("[data-edit-user-save]");
    const createEndpoint = dialog.dataset.createEndpoint;
    const updateEndpointTemplate = dialog.dataset.updateEndpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let currentUserId = "";
    if (!(form instanceof HTMLFormElement) || !(saveButton instanceof HTMLButtonElement) || !createEndpoint || !updateEndpointTemplate || !csrfHeader || !csrfToken) {
      return;
    }
    const setStatus = (message, isError = false) => {
      if (!(status instanceof HTMLElement)) {
        return;
      }
      status.textContent = message;
      status.classList.toggle("text-error", isError);
      status.classList.toggle("text-base-content/60", !isError);
    };
    const setBusy = (busy, label = currentUserId === "" ? "Add user" : "Save changes") => {
      form.setAttribute("aria-busy", String(busy));
      form.querySelectorAll(editableControlSelector3).forEach((control) => {
        control.disabled = busy;
      });
      saveButton.disabled = busy;
      saveButton.textContent = label;
    };
    const prepareCreate = () => {
      currentUserId = "";
      form.reset();
      clearValidation3(form);
      setStatus("");
      const id = formControl3(form, "id");
      const password = formControl3(form, "password");
      const title = dialog.querySelector("[data-edit-user-title]");
      const description = dialog.querySelector("[data-edit-user-description]");
      const passwordHint = dialog.querySelector("[data-edit-user-password-hint]");
      if (id instanceof HTMLInputElement) id.value = "";
      if (password instanceof HTMLInputElement) password.required = true;
      if (title instanceof HTMLElement) title.textContent = "Add user";
      if (description instanceof HTMLElement) description.textContent = "Create a new account and choose its access level.";
      if (passwordHint instanceof HTMLElement) {
        passwordHint.textContent = `Use at least ${password?.minLength || 8} characters.`;
      }
      setBusy(false);
    };
    const prepareEdit = (trigger) => {
      currentUserId = trigger.dataset.userId || "";
      form.reset();
      clearValidation3(form);
      setStatus("");
      const id = formControl3(form, "id");
      const username = formControl3(form, "username");
      const email = formControl3(form, "email");
      const password = formControl3(form, "password");
      const role = formControl3(form, "role");
      const active = formControl3(form, "active");
      const title = dialog.querySelector("[data-edit-user-title]");
      const description = dialog.querySelector("[data-edit-user-description]");
      const passwordHint = dialog.querySelector("[data-edit-user-password-hint]");
      if (id instanceof HTMLInputElement) id.value = currentUserId;
      if (username instanceof HTMLInputElement) username.value = trigger.dataset.userUsername || "";
      if (email instanceof HTMLInputElement) email.value = trigger.dataset.userEmail || "";
      if (password instanceof HTMLInputElement) {
        password.value = "";
        password.required = false;
      }
      if (role instanceof RadioNodeList) role.value = trigger.dataset.userRole || "user";
      if (active instanceof HTMLInputElement) active.checked = trigger.dataset.userActive === "1";
      if (title instanceof HTMLElement) title.textContent = "Edit user";
      if (description instanceof HTMLElement) description.textContent = "Update this account and its access level.";
      if (passwordHint instanceof HTMLElement) passwordHint.textContent = "Leave blank to keep the current password.";
      setBusy(false);
    };
    const updateCsrf = (result) => {
      if (typeof result.csrf === "string" && result.csrf !== "") {
        csrfToken = result.csrf;
        dialog.dataset.csrfToken = result.csrf;
      }
    };
    const readResponse = async (response) => {
      let result = {};
      try {
        result = await response.json();
      } catch {
      }
      updateCsrf(result);
      return result;
    };
    document.querySelectorAll("[data-add-user]").forEach((trigger) => {
      trigger.addEventListener("click", () => {
        prepareCreate();
        if (!dialog.open) {
          dialog.showModal();
        }
        formControl3(form, "username")?.focus();
      });
    });
    document.querySelectorAll("[data-edit-user]").forEach((trigger) => {
      trigger.addEventListener("click", () => {
        prepareEdit(trigger);
        document.getElementById("users-popover")?.hidePopover?.();
        if (!dialog.open) {
          dialog.showModal();
        }
        formControl3(form, "username")?.focus();
      });
    });
    form.addEventListener("input", () => clearValidation3(form));
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      clearValidation3(form);
      if (!form.reportValidity()) {
        return;
      }
      const data = new FormData(form);
      const payload = {
        username: data.get("username"),
        email: data.get("email"),
        password: data.get("password"),
        role: data.get("role"),
        active: data.get("active") === "1"
      };
      const endpoint = currentUserId === "" ? createEndpoint : updateEndpointTemplate.replace("__USER_ID__", encodeURIComponent(currentUserId));
      const action = currentUserId === "" ? "Adding" : "Saving";
      setStatus(`${action} user\u2026`);
      setBusy(true, `${action}\u2026`);
      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            [csrfHeader]: csrfToken
          },
          body: JSON.stringify(payload)
        });
        const result = await readResponse(response);
        if (!response.ok) {
          setBusy(false);
          if (result.errors && typeof result.errors === "object") {
            applyValidation3(form, result.errors);
            form.reportValidity();
          }
          throw new Error(result.error || "Unable to add this user.");
        }
        dialog.hidden = true;
        dialog.close();
        window.location.reload();
      } catch (error) {
        setStatus(error.message || "Unable to add this user.", true);
        setBusy(false);
      }
    });
    dialog.addEventListener("close", () => {
      prepareCreate();
    });
    prepareCreate();
  }

  // theme/js/edit/sort-collections.js
  function collectionItems(list) {
    return [...list.querySelectorAll("[data-sort-collection]")];
  }
  function updatePositions(list) {
    collectionItems(list).forEach((item, index) => {
      const position = item.querySelector("[data-sort-collection-position]");
      if (position instanceof HTMLElement) {
        position.textContent = String(index + 1);
      }
    });
  }
  function initSortCollections() {
    const dialog = document.querySelector("[data-sort-collections-modal]");
    if (!(dialog instanceof HTMLDialogElement)) {
      return;
    }
    const list = dialog.querySelector("[data-sort-collections-list]");
    const saveButton = dialog.querySelector("[data-sort-collections-save]");
    const status = dialog.querySelector("[data-sort-collections-status]");
    const endpoint = dialog.dataset.endpoint;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    if (!(list instanceof HTMLOListElement) || !(saveButton instanceof HTMLButtonElement) || !endpoint || !csrfHeader || !csrfToken) {
      return;
    }
    const initialOrder = collectionItems(list).map((item) => item.dataset.collectionId);
    let draggedItem = null;
    const setStatus = (message, isError = false) => {
      if (!(status instanceof HTMLElement)) {
        return;
      }
      status.textContent = message;
      status.classList.toggle("text-error", isError);
      status.classList.toggle("text-base-content/60", !isError);
    };
    const setBusy = (busy) => {
      const items = collectionItems(list);
      dialog.setAttribute("aria-busy", String(busy));
      saveButton.disabled = busy || items.length < 2;
      saveButton.textContent = busy ? "Saving\u2026" : "Save order";
      items.forEach((item) => {
        item.draggable = !busy;
        item.toggleAttribute("aria-disabled", busy);
      });
    };
    const restoreInitialOrder = () => {
      const itemsById = new Map(
        collectionItems(list).map((item) => [item.dataset.collectionId, item])
      );
      initialOrder.forEach((id) => {
        const item = itemsById.get(id);
        if (item) {
          list.append(item);
        }
      });
      updatePositions(list);
    };
    const updateCsrf = (result) => {
      if (typeof result.csrf === "string" && result.csrf !== "") {
        csrfToken = result.csrf;
        dialog.dataset.csrfToken = result.csrf;
      }
    };
    const readResponse = async (response) => {
      let result = {};
      try {
        result = await response.json();
      } catch {
      }
      updateCsrf(result);
      return result;
    };
    document.querySelectorAll("[data-sort-collections]").forEach((trigger) => {
      trigger.addEventListener("click", () => {
        restoreInitialOrder();
        setStatus("");
        setBusy(false);
        if (!dialog.open) {
          dialog.showModal();
        }
      });
    });
    list.addEventListener("dragstart", (event) => {
      const item = event.target instanceof Element ? event.target.closest("[data-sort-collection]") : null;
      if (!(item instanceof HTMLLIElement)) {
        return;
      }
      draggedItem = item;
      item.setAttribute("aria-grabbed", "true");
      item.classList.add("opacity-40");
      if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData("text/plain", item.dataset.collectionId ?? "");
      }
    });
    list.addEventListener("dragover", (event) => {
      if (!(draggedItem instanceof HTMLLIElement)) {
        return;
      }
      event.preventDefault();
      if (event.dataTransfer) {
        event.dataTransfer.dropEffect = "move";
      }
      const target = event.target instanceof Element ? event.target.closest("[data-sort-collection]") : null;
      if (!(target instanceof HTMLLIElement) || target === draggedItem) {
        return;
      }
      const bounds = target.getBoundingClientRect();
      const insertAfter = event.clientY > bounds.top + bounds.height / 2;
      list.insertBefore(draggedItem, insertAfter ? target.nextElementSibling : target);
      updatePositions(list);
    });
    list.addEventListener("drop", (event) => {
      event.preventDefault();
      updatePositions(list);
    });
    list.addEventListener("dragend", () => {
      if (draggedItem instanceof HTMLLIElement) {
        draggedItem.removeAttribute("aria-grabbed");
        draggedItem.classList.remove("opacity-40");
      }
      draggedItem = null;
      updatePositions(list);
    });
    list.addEventListener("keydown", (event) => {
      if (!["ArrowUp", "ArrowDown"].includes(event.key)) {
        return;
      }
      const item = event.target instanceof Element ? event.target.closest("[data-sort-collection]") : null;
      if (!(item instanceof HTMLLIElement)) {
        return;
      }
      const sibling = event.key === "ArrowUp" ? item.previousElementSibling : item.nextElementSibling;
      if (!(sibling instanceof HTMLLIElement)) {
        return;
      }
      event.preventDefault();
      if (event.key === "ArrowUp") {
        list.insertBefore(item, sibling);
      } else {
        list.insertBefore(sibling, item);
      }
      updatePositions(list);
      item.focus();
    });
    saveButton.addEventListener("click", async () => {
      const collectionIds = collectionItems(list).map((item) => item.dataset.collectionId);
      setStatus("Saving collection order\u2026");
      setBusy(true);
      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            [csrfHeader]: csrfToken
          },
          body: JSON.stringify({ collection_ids: collectionIds })
        });
        const result = await readResponse(response);
        if (!response.ok) {
          throw new Error(result.error || "Unable to save the collection order.");
        }
        dialog.hidden = true;
        dialog.close();
        window.location.reload();
      } catch (error) {
        setStatus(error.message || "Unable to save the collection order.", true);
        setBusy(false);
      }
    });
    dialog.addEventListener("close", () => {
      restoreInitialOrder();
      setStatus("");
      setBusy(false);
    });
  }

  // theme/js/app.js
  initDateTime();
  initHashing();
  initLinkFilters();
  initLinkSearch();
  initPasswordGenerator();
  initWorkTime();
  initEditCollection();
  initEditLink();
  initEditMode();
  initEditUser();
  initSortCollections();
})();
