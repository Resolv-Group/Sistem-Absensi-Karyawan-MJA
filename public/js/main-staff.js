// In js/main-staff.js
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

    function fetchPekerja(targetUrl) {
        const fetchUrl = targetUrl || url;

        // Check if any filter is active for the badge
        const hasFilter =
            $("#statusFilter").val() !== "" ||
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
            url: fetchUrl,
            type: "GET",
            data: targetUrl ? {} : {
                search: $("#searchInput").val(),
                status: $("#statusFilter").val(),
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
    $("#searchInput").on("keyup input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchPekerja, 300);
    });

    // Trigger fetch on filter changes
    $("#statusFilter, #startDate, #endDate").on("change", function () {
        fetchPekerja();
    });

    // Reset Button
    $("#resetFilters").on("click", function () {
        $("#statusFilter").val("");
        $("#startDate").val("");
        $("#endDate").val("");
        fetchPekerja();
        $filterDropdown.addClass("hidden"); // Close dropdown on reset
    });

    // Handle Pagination Clicks via Event Delegation
    $(document).on("click", "#search-pagination a, .pagination a", function (e) {
        e.preventDefault();
        const pageUrl = $(this).attr("href");
        if (pageUrl) {
            fetchPekerja(pageUrl);
        }
    });
});
