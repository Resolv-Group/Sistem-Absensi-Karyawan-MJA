// main-pekerja.js — Pure jQuery (no Alpine bridge)
$(document).ready(function () {

    // ============================
    // 1. FILTER PANEL TOGGLE
    // ============================
    const $filterBtn = $("#filterToggleBtn");
    const $filterDropdown = $("#filterDropdown");
    const $badge = $("#activeFilterBadge");

    $filterBtn.on("click", function (e) {
        e.stopPropagation();
        $filterDropdown.toggleClass("hidden");
    });

    $(document).on("click", function (e) {
        // Close filter panel on outside click
        if (
            !$filterDropdown.is(e.target) &&
            $filterDropdown.has(e.target).length === 0 &&
            !$filterBtn.is(e.target)
        ) {
            $filterDropdown.addClass("hidden");
        }

        // Close unit dropdown on outside click
        var $unitMenu = $("#unitDropdownMenu");
        var $unitTrigger = $("#unitDropdownTrigger");
        if (
            !$unitMenu.is(e.target) &&
            $unitMenu.has(e.target).length === 0 &&
            !$unitTrigger.is(e.target) &&
            $unitTrigger.has(e.target).length === 0
        ) {
            closeUnitDropdown();
        }
    });


    // ============================
    // 2. UNIT DROPDOWN (jQuery)
    // ============================
    var $unitTrigger = $("#unitDropdownTrigger");
    var $unitMenu = $("#unitDropdownMenu");
    var $unitLabel = $("#unitDropdownLabel");
    var $unitChevron = $("#unitDropdownChevron");
    var $unitSearch = $("#unitDropdownSearch");
    var $unitFilter = $("#unitFilter");

    // Toggle dropdown open/close
    $unitTrigger.on("click", function (e) {
        e.stopPropagation();
        var isOpening = $unitMenu.hasClass("hidden");
        $unitMenu.toggleClass("hidden");
        $unitChevron.toggleClass("rotate-180");

        if (isOpening) {
            setTimeout(function () {
                $unitSearch.focus();
            }, 50);
        }
    });

    // Prevent search input click from bubbling (closing the dropdown)
    $unitSearch.on("click", function (e) {
        e.stopPropagation();
    });

    // Filter options as user types in search
    $unitSearch.on("input", function () {
        var query = $(this).val().toLowerCase();
        var visibleCount = 0;

        $(".unit-option").each(function () {
            var label = $(this).find(".unit-label").text().toLowerCase();
            if (label.indexOf(query) !== -1 || query === "") {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // Show/hide empty state
        if (visibleCount === 0) {
            $("#unitDropdownEmpty").removeClass("hidden");
        } else {
            $("#unitDropdownEmpty").addClass("hidden");
        }
    });

    // Select a unit option
    $(document).on("click", ".unit-option", function () {
        var val = $(this).attr("data-value");
        var label = $(this).find(".unit-label").text();

        // Update hidden input value (this is what fetchPekerja reads)
        $unitFilter.val(val);

        // Update display label
        $unitLabel.text(label);

        // Reset all options to default styling
        $(".unit-option")
            .removeClass("bg-blue-50 text-blue-700 font-semibold")
            .addClass("text-gray-700");
        $(".unit-option .unit-check").addClass("hidden");
        $(".unit-option .unit-spacer").removeClass("hidden");

        // Highlight the selected option
        $(this)
            .removeClass("text-gray-700")
            .addClass("bg-blue-50 text-blue-700 font-semibold");
        $(this).find(".unit-check").removeClass("hidden");
        $(this).find(".unit-spacer").addClass("hidden");

        // Close dropdown and fetch data
        closeUnitDropdown();
        fetchPekerja();
    });

    function closeUnitDropdown() {
        $unitMenu.addClass("hidden");
        $unitChevron.removeClass("rotate-180");
        $unitSearch.val("");
        $(".unit-option").show();
        $("#unitDropdownEmpty").addClass("hidden");
    }

    function resetUnitDropdown() {
        $unitFilter.val("");
        $unitLabel.text("Semua Unit");

        // Reset all options styling
        $(".unit-option")
            .removeClass("bg-blue-50 text-blue-700 font-semibold")
            .addClass("text-gray-700");
        $(".unit-option .unit-check").addClass("hidden");
        $(".unit-option .unit-spacer").removeClass("hidden");

        // Re-select the first option (Semua Unit)
        $(".unit-option").first()
            .removeClass("text-gray-700")
            .addClass("bg-blue-50 text-blue-700 font-semibold");
        $(".unit-option").first().find(".unit-check").removeClass("hidden");
        $(".unit-option").first().find(".unit-spacer").addClass("hidden");

        closeUnitDropdown();
    }


    // ============================
    // 3. AJAX FETCH (Filters)
    // ============================
    var url = $("#searchInput").data("url");
    var $tableWrapper = $("#table-wrapper");

    function fetchPekerja() {
        // Determine if any filter is active (for badge display)
        var hasFilter =
            $("#statusFilter").val() !== "" ||
            $("#unitFilter").val() !== "" ||
            $("#startDate").val() !== "" ||
            $("#endDate").val() !== "";

        if (hasFilter) {
            $badge.removeClass("hidden");
            $filterBtn.addClass("border-blue-300 bg-blue-50 text-blue-700");
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
                console.error("Error fetching pekerja:", xhr);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
        });
    }


    // ============================
    // 4. SEARCH & FILTER TRIGGERS
    // ============================

    // Live search with debounce
    var debounceTimer;
    $("#searchInput").on("keyup", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchPekerja, 300);
    });

    // Status and date filters trigger fetch directly
    // (Unit filter triggers fetchPekerja() in its own click handler above)
    $("#statusFilter, #startDate, #endDate").on("change", fetchPekerja);

    // Reset all filters
    $("#resetFilters").on("click", function () {
        $("#statusFilter").val("");
        $("#startDate").val("");
        $("#endDate").val("");
        resetUnitDropdown();
        fetchPekerja();
        $filterDropdown.addClass("hidden");
    });


    // ============================
    // 5. PAGINATION (Event Delegation)
    // ============================
    // Using event delegation so it works automatically after AJAX table reloads
    // — no need to re-attach handlers after each fetchPekerja() call.
    $tableWrapper.on("click", "#search-pagination a", function (e) {
        e.preventDefault();
        var pageUrl = $(this).attr("href");
        if (!pageUrl) return;

        $.ajax({
            url: pageUrl,
            type: "GET",
            beforeSend: function () {
                $tableWrapper.addClass("opacity-50 pointer-events-none");
            },
            success: function (response) {
                $tableWrapper.html(response);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
            error: function (xhr) {
                console.error("Pagination error:", xhr);
                $tableWrapper.removeClass("opacity-50 pointer-events-none");
            },
        });
    });

});
