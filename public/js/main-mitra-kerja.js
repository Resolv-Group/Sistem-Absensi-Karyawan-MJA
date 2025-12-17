// In js/main-kerja.js

const input = document.getElementById("searchInput");
const wrapper = document.getElementById("table-wrapper");

// FIX: Get the URL from the HTML attribute, not Blade syntax
const baseUrl = input ? input.getAttribute('data-url') : '';

if (input) {
    input.addEventListener("input", debounce(function (e) {
        const q = e.target.value;

        // Use the captured baseUrl
        const url = `${baseUrl}?q=${encodeURIComponent(q)}&page=1`;

        loadPage(url);
    }, 300));
}

function loadPage(url) {
    if (!url) return; // Safety check

    fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "text/html"
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.text();
    })
    .then(html => {
        wrapper.innerHTML = html;
        attachPaginationEvents();
    })
    .catch(error => console.error('Error loading page:', error));
}

function attachPaginationEvents() {
    // Target the specific ID wrapping the links
    const links = document.querySelectorAll("#search-pagination a");

    links.forEach(a => {
        a.addEventListener("click", function(e) {
            e.preventDefault();
            loadPage(this.href);
        });
    });
}

function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

// Initial attachment
attachPaginationEvents();
