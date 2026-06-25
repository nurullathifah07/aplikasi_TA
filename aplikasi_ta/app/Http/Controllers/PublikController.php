<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use App\Models\PermintaanDarah;
use App\Models\StokDarah;
use Yajra\DataTables\Facades\DataTables;

class PublikController extends Controller
{
    public function prediksi()
    {
        $prediksi = Prediksi::with('komponenDarah')
            ->orderBy('tanggal_prediksi', 'desc')
            ->orderBy('tanggal_target', 'asc')
            ->get();

        return view('publik.prediksi', compact('prediksi'));
    }

    public function stokDarah()
    {
        $stokDarah = StokDarah::with('komponenDarah')->get();

        return view('publik.stok_darah', compact('stokDarah'));
    }

    public function historiTren()
    {
        return view('publik.histori_tren');
    }

    // Server-side DataTables untuk histori tren
    public function historiTrenData()
    {
        $query = PermintaanDarah::with('komponenDarah')
            ->selectRaw('tanggal, golongan_darah, komponen_darah_id, SUM(jumlah) as total')
            ->groupBy('tanggal', 'golongan_darah', 'komponen_darah_id')
            ->orderBy('tanggal', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->addColumn('komponen_kode', function ($row) {
                return $row->komponenDarah->kode;
            })
            ->make(true);
    }

    // Data chart per bulan yang dipilih
    public function historiTrenChart()
    {
        $bulan = request('bulan', date('Y-m'));

        $startDate = \Carbon\Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $data = PermintaanDarah::selectRaw('tanggal, golongan_darah, SUM(jumlah) as total')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('tanggal', 'golongan_darah')
            ->orderBy('tanggal', 'asc')
            ->get();

        $dates = [];
        $allDates = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dates[] = $date->format('d/m');
            $allDates[] = $date->format('Y-m-d');
        }

        $golongans = ['A', 'B', 'AB', 'O'];
        $series = [];

        foreach ($golongans as $gol) {

            $series[$gol] = [];

            foreach ($allDates as $tgl) {

                $found = $data->first(function ($d) use ($tgl, $gol) {
                    return $d->tanggal->format('Y-m-d') === $tgl
                        && $d->golongan_darah === $gol;
                });

                $series[$gol][] = $found ? (int) $found->total : 0;
            }

            if (array_sum($series[$gol]) == 0) {
                unset($series[$gol]);
            }
        }

        return response()->json([
            'dates'  => $dates,
            'series' => $series
        ]);
    }
}
