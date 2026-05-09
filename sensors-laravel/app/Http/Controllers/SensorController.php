<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SensorController extends Controller
{
    private function csvPath(): string
    {
        return storage_path('app/sensors.csv');
    }

    private function ensureCsvFileExists(): void
    {
        $path = $this->csvPath();

        if (file_exists($path)) {
            return;
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        // Create with header only
        file_put_contents($path, "timestamp,light,temperature,humidity\n");
    }

    /**
     * @return array<int, array{timestamp:string, light:float|null, temperature:float|null, humidity:float|null}>
     */
    private function readRows(): array
    {
        $this->ensureCsvFileExists();

        $path = $this->csvPath();
        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $lines = preg_split('/\r\n|\n|\r/', trim($content));
        if (!$lines || count($lines) === 0) {
            return [];
        }

        // Remove header
        if (str_starts_with($lines[0], 'timestamp,')) {
            array_shift($lines);
        } else {
            // If header is missing, still try to parse all lines.
        }

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = explode(',', $line);
            if (count($parts) < 4) {
                continue;
            }

            [$timestamp, $light, $temperature, $humidity] = $parts;

            $rows[] = [
                'timestamp' => $timestamp,
                'light' => is_numeric($light) ? (float) $light : null,
                'temperature' => is_numeric($temperature) ? (float) $temperature : null,
                'humidity' => is_numeric($humidity) ? (float) $humidity : null,
            ];
        }

        return $rows;
    }

    public function dashboard(Request $request)
    {
        return view('dashboard');
    }

    public function latest(Request $request)
    {
        $rows = $this->readRows();
        $latest = $rows ? $rows[count($rows) - 1] : null;

        return response()->json([
            'latest' => $latest,
        ]);
    }

    public function history(Request $request)
    {
        $rows = $this->readRows();
        $last20 = array_slice($rows, -20);

        return response()->json([
            'history' => array_values($last20),
        ]);
    }

    public function download(Request $request)
    {
        $path = $this->csvPath();
        $this->ensureCsvFileExists();

        return response()->download($path, 'sensors.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

