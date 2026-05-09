type LatestPayload = {
    timestamp?: string | null;
    light?: number | null;
    temperature?: number | null;
    humidity?: number | null;
};

type LatestResponse = {
    latest?: LatestPayload | null;
};

type HistoryRow = {
    timestamp?: string | null;
    light?: number | null;
    temperature?: number | null;
    humidity?: number | null;
};

type HistoryResponse = {
    history?: HistoryRow[];
};

function fmt(val: unknown, suffix = ''): string {
    if (val === null || val === undefined || val === '') return '--';
    const num = Number(val);
    if (Number.isFinite(num)) return `${num}${suffix}`;
    return `${val}${suffix}`;
}

const cardEls = {
    light: document.getElementById('card-light'),
    lightTime: document.getElementById('card-light-time'),
    temp: document.getElementById('card-temp'),
    tempTime: document.getElementById('card-temp-time'),
    humidity: document.getElementById('card-humidity'),
    humidityTime: document.getElementById('card-humidity-time'),
} as const;

type CardEl = (typeof cardEls)[keyof typeof cardEls];

function assertEl(el: CardEl | null, id: string): HTMLElement {
    if (!el) throw new Error(`Missing element: #${id}`);
    return el;
}

const historyBody = document.getElementById('history-body');
if (!historyBody) throw new Error('Missing element: #history-body');

const historyBodySafe = historyBody as HTMLTableSectionElement;




const chartCanvas = document.getElementById('sensorsChart') as HTMLCanvasElement | null;
if (!chartCanvas) throw new Error('Missing element: #sensorsChart');

// Chart.js is loaded via CDN in the blade template.
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const ChartCtor = (window as any).Chart as any;
if (!ChartCtor) throw new Error('Chart.js not found on window (expected from CDN)');

const ctx = chartCanvas.getContext('2d');
if (!ctx) throw new Error('2D canvas context not available');

const chart = new ChartCtor(ctx, {
    type: 'line',
    data: {
        labels: [] as Array<string>,
        datasets: [
            {
                label: 'Light (lux)',
                data: [] as Array<number | null>,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                tension: 0.25,
                spanGaps: true,
            },
            {
                label: 'Temperature (°C)',
                data: [] as Array<number | null>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.25,
                spanGaps: true,
            },
            {
                label: 'Humidity (%)',
                data: [] as Array<number | null>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                tension: 0.25,
                spanGaps: true,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
        },
        scales: {
            y: { beginAtZero: false },
        },
    },
});

function updateLatest(latest: LatestPayload | null | undefined) {
    const lightEl = assertEl(cardEls.light, 'card-light');
    const tempEl = assertEl(cardEls.temp, 'card-temp');
    const humidityEl = assertEl(cardEls.humidity, 'card-humidity');
    const lightTimeEl = assertEl(cardEls.lightTime, 'card-light-time');
    const tempTimeEl = assertEl(cardEls.tempTime, 'card-temp-time');
    const humidityTimeEl = assertEl(cardEls.humidityTime, 'card-humidity-time');

    if (!latest) {
        lightEl.textContent = '--';
        tempEl.textContent = '--';
        humidityEl.textContent = '--';
        lightTimeEl.textContent = '';
        tempTimeEl.textContent = '';
        humidityTimeEl.textContent = '';
        return;
    }

    lightEl.textContent = fmt(latest.light, '');
    tempEl.textContent = fmt(latest.temperature, '');
    humidityEl.textContent = fmt(latest.humidity, '');

    const ts = latest.timestamp ?? '';
    lightTimeEl.textContent = ts;
    tempTimeEl.textContent = ts;
    humidityTimeEl.textContent = ts;
}

function updateHistory(history: HistoryRow[] | unknown) {
    const safeHistory = Array.isArray(history) ? history : [];

    if (safeHistory.length === 0) {
        historyBodySafe.innerHTML = '<tr><td colspan="4">No data yet.</td></tr>';
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.data.datasets[1].data = [];
        chart.data.datasets[2].data = [];
        chart.update();
        return;
    }

    const lightData = safeHistory.map((r) => (r as HistoryRow).light ?? null);
    const tempData = safeHistory.map((r) => (r as HistoryRow).temperature ?? null);
    const humidityData = safeHistory.map((r) => (r as HistoryRow).humidity ?? null);
    const labels = safeHistory.map((r) => (r as HistoryRow).timestamp ?? '');

    chart.data.labels = labels;
    chart.data.datasets[0].data = lightData;
    chart.data.datasets[1].data = tempData;
    chart.data.datasets[2].data = humidityData;
    chart.update();

    const rows = safeHistory
        .map((r) => {
            const row = r as HistoryRow;
            return `
                <tr>
                    <td>${row.timestamp ?? ''}</td>
                    <td>${fmt(row.light)}</td>
                    <td>${fmt(row.temperature)} </td>
                    <td>${fmt(row.humidity)} </td>
                </tr>
            `;
        })
        .join('');

    historyBodySafe.innerHTML = rows;
}



async function fetchJSON<T>(url: string): Promise<T> {
    const res = await fetch(url, {
        headers: {
            Accept: 'application/json',
        },
    });

    if (!res.ok) throw new Error(`Request failed: ${res.status}`);
    return (await res.json()) as T;
}

async function refresh() {
    try {
        const [latestRes, historyRes] = await Promise.all([
            fetchJSON<LatestResponse>('/api/sensors'),
            fetchJSON<HistoryResponse>('/api/history'),
        ]);

        updateLatest(latestRes.latest);
        updateHistory(historyRes.history);
    } catch (e) {
        // keep UI as-is
        console.error(e);
    }
}

// Initial load + auto refresh
refresh();
setInterval(refresh, 3000);

