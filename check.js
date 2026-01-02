// Inside check.js
window.checkDuplicates = function({ inputId, outputSelector, button }) {
  const txt = document.getElementById(inputId);
  const out = document.querySelector(outputSelector);
  const btn = button;

  if (!txt || !out || !btn) return;

  out.textContent = "";
  btn.textContent = "Checking...";

  function normalizeUrl(url) {
    return url.trim().replace(/\/+$/, '');
  }

  const urls = txt.value.match(/https?:\/\/[^\s]+/gi) || [];
  if (urls.length === 0) {
    btn.textContent = "Check";
    out.textContent = "No URLs found in textarea";
    return;
  }

  fetch("/other/extra/scripts/fakesocialmedia/data_alcea.json?t=" + Date.now())
    .then(res => res.json())
    .then(json => {
      const hits = [];
      const normalizedInputUrls = urls.map(normalizeUrl);

      json.forEach(item => {
        const key = Object.keys(item)[0];
        const val = item[key];
        if (val && val.value) {
          const jsonUrls = val.value.match(/https?:\/\/[^\s]+/gi) || [];
          const normalizedJsonUrls = jsonUrls.map(normalizeUrl);

          normalizedInputUrls.forEach(inputUrl => {
            if (normalizedJsonUrls.includes(inputUrl)) {
              hits.push(item);
            }
          });
        }
      });

      if (hits.length > 0) {
        btn.textContent = "Yes, duplicates found";
        out.innerHTML = "<pre>" + JSON.stringify(hits, null, 2) + "</pre>";
      } else {
        btn.textContent = "Check";
        out.textContent = "No duplicates found";
      }
    })
    .catch(err => {
      btn.textContent = "Check";
      out.textContent = "Error loading JSON";
      console.error(err);
    });
};
