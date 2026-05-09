<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>IoT Sensors Dashboard</title>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        /* Let the container define the height (Chart.js will use it via maintainAspectRatio:false). */
        #sensorsChart {
            width: 100% !important;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/flowbite@1.8.1/dist/flowbite.min.css" />
</head>
<body>
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto max-w-6xl px-4 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">IoT Monitoring Dashboard</h1>
            <a
                href="{{ url('/download') }}"
                class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300"
            >
                Download CSV
            </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500">Light (lux)</h3>
                <div id="card-light" class="mt-2 text-3xl font-extrabold text-gray-900">--</div>
                <div id="card-light-time" class="mt-1 min-h-[16px] text-xs text-gray-500"></div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500">Temperature (°C)</h3>
                <div id="card-temp" class="mt-2 text-3xl font-extrabold text-gray-900">--</div>
                <div id="card-temp-time" class="mt-1 min-h-[16px] text-xs text-gray-500"></div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-500">Humidity (%)</h3>
                <div id="card-humidity" class="mt-2 text-3xl font-extrabold text-gray-900">--</div>
                <div id="card-humidity-time" class="mt-1 min-h-[16px] text-xs text-gray-500"></div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="mb-1 text-sm font-bold text-gray-900">Sensors (Last 20 Records)</div>
                        <div class="text-xs text-gray-500">Light, Temperature & Humidity</div>
                    </div>
                </div>

                <div class="mt-3 h-[320px] sm:h-[360px] lg:h-[420px]">
                    <canvas id="sensorsChart" class="h-full w-full"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-3 text-sm font-bold text-gray-900">History (Last 20 Records)</div>
                <div class="max-h-[360px] overflow-auto rounded-lg">
                    <table class="w-full table-auto text-left text-sm text-gray-700">
                        <thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-3 py-2">Time</th>
                                <th class="px-3 py-2">Light</th>
                                <th class="px-3 py-2">Temperature</th>
                                <th class="px-3 py-2">Humidity</th>
                            </tr>
                        </thead>
                        <tbody id="history-body">
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-gray-500">No data yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



@vite(['resources/ts/dashboard.ts'])
</body>
</html>


