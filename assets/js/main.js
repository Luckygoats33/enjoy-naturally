(function () {
  var navToggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("site-nav");
  var searchOpen = document.querySelector(".search-open");
  var dialog = document.getElementById("search-dialog");
  var searchInput = document.getElementById("search-input");
  var searchList = document.getElementById("search-results");
  var searchStatus = document.getElementById("search-status");
  var indexNode = document.getElementById("search-index");
  var index = [];

  if (indexNode) {
    try {
      index = JSON.parse(indexNode.textContent);
    } catch (error) {
      index = [];
    }
  }

  function closeNav() {
    if (!nav || !navToggle) return;
    nav.classList.remove("is-open");
    navToggle.setAttribute("aria-expanded", "false");
  }

  if (navToggle && nav) {
    navToggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") closeNav();
  });

  document.addEventListener("click", function (event) {
    if (!nav || !nav.classList.contains("is-open")) return;
    if (nav.contains(event.target) || navToggle.contains(event.target)) return;
    closeNav();
  });

  function renderResults(query) {
    var q = query.trim().toLowerCase();
    var matches = index.filter(function (item) {
      if (!q) return item.kind === "article" || item.kind === "recipe";
      return (item.title + " " + item.text + " " + item.kind).toLowerCase().indexOf(q) !== -1;
    }).slice(0, 8);

    searchList.innerHTML = "";
    if (!matches.length) {
      searchStatus.textContent = "Nothing matches that yet. The journal is just getting started.";
      return;
    }
    searchStatus.textContent = matches.length + (matches.length === 1 ? " match" : " matches");
    matches.forEach(function (item) {
      var li = document.createElement("li");
      var link = document.createElement("a");
      link.href = item.href;
      var title = document.createElement("strong");
      title.textContent = item.title;
      var meta = document.createElement("span");
      meta.textContent = item.kindLabel;
      link.appendChild(title);
      link.appendChild(meta);
      li.appendChild(link);
      searchList.appendChild(li);
    });
  }

  if (searchOpen && dialog && searchInput) {
    searchOpen.addEventListener("click", function () {
      closeNav();
      dialog.showModal();
      searchInput.value = "";
      renderResults("");
      searchInput.focus();
    });
    searchInput.addEventListener("input", function () {
      renderResults(searchInput.value);
    });
    dialog.addEventListener("click", function (event) {
      if (event.target === dialog) dialog.close();
    });
    var closer = dialog.querySelector(".search-close");
    if (closer) closer.addEventListener("click", function () { dialog.close(); });
  }

  function wireForm(form) {
    if (!form) return;
    var error = form.querySelector(".form-error");
    var success = form.parentElement.querySelector(".success");
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      var invalid = false;
      form.querySelectorAll("input, textarea, select").forEach(function (field) {
        if (field.type === "checkbox") {
          var bad = field.required && !field.checked;
          field.setAttribute("aria-invalid", bad ? "true" : "false");
          if (bad) invalid = true;
          return;
        }
        var badField = !field.checkValidity();
        field.setAttribute("aria-invalid", badField ? "true" : "false");
        if (badField) invalid = true;
      });
      if (invalid) {
        if (error) {
          error.hidden = false;
          error.textContent = "Check the highlighted fields. An email address and the consent box are required.";
        }
        var first = form.querySelector("[aria-invalid='true']");
        if (first) first.focus();
        return;
      }
      if (error) error.hidden = true;
      var data = { at: new Date().toISOString() };
      new FormData(form).forEach(function (value, key) {
        data[key] = String(value);
      });
      var key = form.dataset.store;
      var existing = [];
      try {
        existing = JSON.parse(localStorage.getItem(key) || "[]");
      } catch (err) {
        existing = [];
      }
      if (!Array.isArray(existing)) existing = [];
      var duplicate = form.dataset.store === "en-newsletter" && existing.some(function (row) {
        return data.email && row.email && row.email.toLowerCase() === data.email.toLowerCase();
      });
      if (!duplicate) existing.push(data);
      try {
        localStorage.setItem(key, JSON.stringify(existing));
      } catch (err) {
        if (error) {
          error.hidden = false;
          error.textContent = "This browser blocked local storage, so the note was not saved.";
        }
        return;
      }
      form.hidden = true;
      if (success) {
        success.hidden = false;
        var heading = success.querySelector("h3");
        if (duplicate && heading && form.dataset.store === "en-newsletter") {
          heading.textContent = "That address is already saved in this browser.";
        }
      }
    });
  }

  wireForm(document.querySelector("[data-store='en-newsletter']"));
  wireForm(document.querySelector("[data-store='en-contact']"));
})();
