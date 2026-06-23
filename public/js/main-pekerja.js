// In js/main-pekerja.js
$(document).ready(function () {
    // UI: Toggle Dropdown
    const $filterBtn = $("#filterToggleBtn");
    const $filterDropdown = $("#filterDropdown");
    const $badge = $("#activeFilterBadge");

    // Toggle visibility when clicking the button
    $filterBtn.on("click", function (e) {
        e.stopPropagation();
        $filterDropdown.toggleClass("hidden");
    });

    // Close dropdown when clicking outside
    $(document).on("click", function (e) {
        if (
            !$filterDropdown.is(e.target) &&
            $filterDropdown.has(e.target).length === 0 &&
            !$filterBtn.is(e.target)
        ) {
            $filterDropdown.addClass("hidden");
        }
    });

    // AJAX Logic
    const url = $("#searchInput").data("url");
    const $tableWrapper = $("#table-wrapper");

    function fetchPekerja() {
        // Check if any filter is active for the badge
        const hasFilter =
            $("#statusFilter").val() !== "" ||
            $("#unitFilter").val() !== "" ||
            $("#startDate").val() !== "" ||
            $("#endDate").val() !== "";

        if (hasFilter) {
            $badge.removeClass("hidden");
            $filterBtn.addClass("border-blue-300 bg-blue-50 text-blue-700"); // Highlight button
        } else {
            $badge.addClass("hidden");
            $filterBtn.removeClass("border-blue-300 bg-blue-50 text-blue-700");
        }

        $.ajax({
            url: url,
            type: "GET",
            data: {
                search: $("#searchInput").val(),
                status: $("#statusFilter").val(),
                unit: $("#unitFilter").val(),
                start_date: $("#startDate").val(),
                end_date: $("#endDate").val(),
            },
            beforeSend: function () {
                $tableWrapper.addClass("opacity-50 pointer-events-none");
            },
            success: function (response) {
                $tableWrapper.html(response);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
            error: function (xhr) {
                console.error("Error:", xhr);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
        });
    }

    // Live Search with Debounce
    let debounceTimer;
    $("#searchInput").on("keyup", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchPekerja, 300);
    });

    // Trigger fetch on filter changes
    $("#statusFilter, #unitFilter, #startDate, #endDate").on("change", fetchPekerja);

    // Reset Button
    $("#resetFilters").on("click", function () {
        $("#statusFilter").val("");
        $("#unitFilter").val("");
        $("#startDate").val("");
        $("#endDate").val("");

        // Reset Alpine.js dropdown states
        const statusEl = document.querySelector('[x-data]#statusFilter')?.closest('[x-data]');
        if (statusEl && statusEl.__x) {
            statusEl.__x.$data.selected = '';
        } else {
            // Alpine v3 approach
            document.querySelectorAll('[x-data]').forEach(el => {
                if (el._x_dataStack) {
                    const data = el._x_dataStack[0];
                    if (data.hasOwnProperty('selected') && data.hasOwnProperty('list')) {
                        data.selected = '';
                    }
                    if (data.hasOwnProperty('selected') && data.hasOwnProperty('units')) {
                        data.selected = '';
                        data.searchQuery = '';
                    }
                }
            });
        }

        fetchPekerja();
        $filterDropdown.addClass("hidden"); // Close dropdown on reset
    });
});

const input = document.getElementById("searchInput");
const wrapper = document.getElementById("table-wrapper");

// FIX: Get the URL from the HTML attribute, not Blade syntax
const baseUrl = input ? input.getAttribute("data-url") : "";

if (input) {
    input.addEventListener(
        "input",
        debounce(function (e) {
            const q = e.target.value;

            // Use the captured baseUrl
            const url = `${baseUrl}?q=${encodeURIComponent(q)}&page=1`;

            loadPage(url);
        }, 300)
    );
}

function loadPage(url) {
    if (!url) return; // Safety check

    fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "text/html",
        },
    })
        .then((res) => {
            if (!res.ok) throw new Error("Network response was not ok");
            return res.text();
        })
        .then((html) => {
            wrapper.innerHTML = html;
            attachPaginationEvents();
        })
        .catch((error) => console.error("Error loading page:", error));
}

function attachPaginationEvents() {
    // Target the specific ID wrapping the links
    const links = document.querySelectorAll("#search-pagination a");

    links.forEach((a) => {
        a.addEventListener("click", function (e) {
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
