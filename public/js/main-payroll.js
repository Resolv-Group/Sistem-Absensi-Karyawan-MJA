$(document).ready(function () {
    // UI: Toggle Dropdown
    const $filterBtn = $("#filterToggleBtn");
    const $filterDropdown = $("#filterDropdown");
    const $badge = $("#activeFilterBadge");

    // Toggle visibility when clicking the filter button
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

    // AJAX Search & Filter Handler
    const baseUrl = $("#searchInput").data("url") || "/payroll";
    const $tableWrapper = $("#table-wrapper");

    function fetchPayroll(targetUrl) {
        const fetchUrl = targetUrl || baseUrl;

        // Check if any filter is active for the badge
        const hasFilter =
            $("#statusFilter").val() !== "" ||
            $("#pengajianFilter").val() !== "";

        if (hasFilter) {
            $badge.removeClass("hidden");
            $filterBtn.addClass("border-blue-300 bg-blue-50 text-blue-700");
        } else {
            $badge.addClass("hidden");
            $filterBtn.removeClass("border-blue-300 bg-blue-50 text-blue-700");
        }

        $.ajax({
            url: fetchUrl,
            type: "GET",
            data: {
                search: $("#searchInput").val(),
                status: $("#statusFilter").val(),
                pengajian: $("#pengajianFilter").val(),
            },
            beforeSend: function () {
                $tableWrapper.addClass("opacity-50 pointer-events-none");
            },
            success: function (response) {
                $tableWrapper.html(response);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
            error: function (xhr) {
                console.error("Error fetching payroll data:", xhr);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
        });
    }

    // Live Search with Debounce
    let debounceTimer;
    $("#searchInput").on("keyup input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            fetchPayroll();
        }, 300);
    });

    // Trigger fetch on filter changes
    $("#statusFilter, #pengajianFilter").on("change", function() {
        fetchPayroll();
    });

    // Reset Button
    $("#resetFilters").on("click", function () {
        $("#statusFilter").val("");
        $("#pengajianFilter").val("");
        fetchPayroll();
        $filterDropdown.addClass("hidden");
    });

    // Pagination Click Delegation
    $(document).on("click", "#search-pagination a", function (e) {
        e.preventDefault();
        const pageUrl = $(this).attr("href");
        if (pageUrl) {
            fetchPayroll(pageUrl);
        }
    });

    // Payslip Modal Open Delegation
    $(document).on("click", ".btn-payslip-open", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const unitId = $btn.data("unit-id");
        const unitName = $btn.data("unit-name");
        const sistemPengajian = $btn.data("sistem-pengajian");

        let workers = $btn.data("workers");
        if (typeof workers === "string") {
            try {
                workers = JSON.parse(workers);
            } catch (err) {
                console.error("Error parsing worker data:", err);
                workers = [];
            }
        }

        if (window.Alpine && window.Alpine.store("payslip")) {
            window.Alpine.store("payslip").open(unitId, unitName, workers || [], sistemPengajian);
        } else {
            console.error("Alpine payslip store not initialized");
        }
    });
});
