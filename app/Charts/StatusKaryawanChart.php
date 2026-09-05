<?php

namespace App\Charts;

use App\Models\Karyawan;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class StatusKaryawanChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($request = null, $existingRekap = null): \ArielMejiaDev\LarapexCharts\PieChart
    {
        $labels = [];
        $data = [];

        if ($existingRekap !== null) {
            foreach ($existingRekap as $item) {
                $labels[] = $item->nama_status_karyawan;
                $data[] = (int) $item->total;
            }
        } else {
            // Get all statuses from the new table
            $statuses = DB::table('status_karyawan')->get();

            $query = Karyawan::query()
                ->select('status_karyawan', DB::raw('count(*) as total'))
                ->groupBy('status_karyawan');

            // Filter by user access/request
            if (!empty($request->user_cabangs) && is_array($request->user_cabangs)) {
                $query->whereIn('karyawan.kode_cabang', $request->user_cabangs);
            } elseif (!empty($request->kode_cabang)) {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->user_departemens) && is_array($request->user_departemens)) {
                $query->whereIn('karyawan.kode_dept', $request->user_departemens);
            } elseif (!empty($request->kode_dept)) {
                $query->where('karyawan.kode_dept', $request->kode_dept);
            }

            $countsByStatus = $query->pluck('total', 'status_karyawan')->toArray();

            foreach ($statuses as $status) {
                $labels[] = $status->nama_status_karyawan;
                $data[] = (int) ($countsByStatus[$status->kode_status_karyawan] ?? 0);
            }
        }

        return $this->chart->pieChart()
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'])
            ->setDataLabels(true)
            ->setOptions([
                'dataLabels' => [
                    'enabled' => true,
                    'formatter' => function ($val, $opts) {
                        return round($val, 1) . '%';
                    },
                    'dropShadow' => [
                        'enabled' => true
                    ]
                ]
            ]);
    }
}
