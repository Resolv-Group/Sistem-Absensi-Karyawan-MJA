const el = document.getElementById("employeeGrowthChart");
const employeeChartData = JSON.parse(el.dataset.chart);

document.addEventListener("DOMContentLoaded", function () {
    var options = {
        series: [
            {
                name: "Jumlah Pegawai Baru",
                data: employeeChartData, // Dummy data for months
            },
        ],
        chart: {
            type: "area",
            height: 320,
            toolbar: {
                show: false,
            },
            fontFamily: "inherit",
        },
        stroke: {
            curve: "smooth",
            width: 2,
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100],
            },
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#3b82f6"], // Blue-500
        xaxis: {
            categories: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ],
            labels: {
                style: {
                    colors: "#64748b",
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: "#64748b",
                },
            },
        },
        grid: {
            borderColor: "#f1f5f9",
            strokeDashArray: 4,
        },
        tooltip: {
            // This ensures the tooltip background is transparent so our Tailwind classes control the look
            cssClass: "apexcharts-tooltip-custom",
            custom: function ({ series, seriesIndex, dataPointIndex }) {
                const months = [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember",
                ];

                const value = series[seriesIndex][dataPointIndex];
                const month = months[dataPointIndex];

                return `
                            <div class="px-4 py-2 bg-white border border-gray-100 shadow-lg rounded-lg">
                                <div class="text-xs text-gray-500 mb-0.5">${month}</div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    <span class="text-sm font-bold text-gray-900">${value} Pegawai</span>
                                </div>
                            </div>
                        `;
            },
        },
    };

    var chart = new ApexCharts(
        document.querySelector("#employeeGrowthChart"),
        options
    );
    chart.render();
});
