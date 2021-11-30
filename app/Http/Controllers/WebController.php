<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    public function login_attempt(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect('/dashboard');
        } else {
            Session::flash('failed');
            return redirect()->back()->withInput($request->all());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function dashboard()
    {
        $dataPenjualan = Penjualan::orderBy('periode')->get();
        $periode = [];
        foreach ($dataPenjualan as $dp) {
            $periodeDp = date('Y', strtotime($dp->periode));
            $cek = array_search($periodeDp, $periode);
            if ($cek === false) {
                $periode[] = $periodeDp;
            }
        }

        $pendapatan = Penjualan::whereYear('periode', end($periode))->sum('pendapatan');
        $terjual = Penjualan::whereYear('periode', end($periode))->sum('terjual');

        return view('dashboard', compact('periode', 'pendapatan', 'terjual'));
    }

    public function data_pendapatan(Request $request)
    {
        if ($request->tahun == null) {
            $periode = Penjualan::orderBy('periode', 'DESC')->first();
            $periode = date('Y', strtotime($periode->periode));
            $penjualan = Penjualan::whereYear('periode', $periode)->get();
            $pendapatan = [];
            foreach ($penjualan as $p) {
                $pendapatan[] = $p->pendapatan;
            }

            $totalPendapatan = Penjualan::whereYear('periode', $periode)->sum('pendapatan');
            $terjual = Penjualan::whereYear('periode', $periode)->sum('terjual');

            $response = [
                "pendapatan" => $pendapatan,
                "totalPendapatan" => number_format($totalPendapatan),
                "terjual" => number_format($terjual)
            ];

            return response()->json($response);
        } else {
            $periode = $request->tahun;
            $penjualan = Penjualan::whereYear('periode', $periode)->get();
            $pendapatan = [];
            foreach ($penjualan as $p) {
                $pendapatan[] = $p->pendapatan;
            }

            $totalPendapatan = Penjualan::whereYear('periode', $periode)->sum('pendapatan');
            $terjual = Penjualan::whereYear('periode', $periode)->sum('terjual');

            $response = [
                "pendapatan" => $pendapatan,
                "totalPendapatan" => number_format($totalPendapatan),
                "terjual" => number_format($terjual)
            ];

            return response()->json($response);
        }
    }

    public function kelola_data_penjualan()
    {
        $lastPeriod = Penjualan::orderBy('periode', 'DESC')->first();
        if ($lastPeriod) {
            $lastPeriod = date('F Y', strtotime('+1 months', strtotime($lastPeriod->periode)));
        }
        return view('input', compact('lastPeriod'));
    }

    public function data_penjualan()
    {
        $dataPenjualan = Penjualan::all();
        $data = [];
        foreach ($dataPenjualan as $dp) {
            $data[] = [
                "no" => $dp->id,
                "periode" => date('F Y', strtotime($dp->periode)),
                "stok_awal" => $dp->stok_awal,
                "stok_akhir" => $dp->stok_akhir,
                "terjual" => $dp->terjual,
                "pendapatan" => $dp->pendapatan
            ];
        }
        $response = [
            "data" => $data
        ];
        return response()->json($response);
    }

    public function input(Request $request)
    {
        $parsedMonthYear = date('Y-m-d', strtotime('01 ' . $request->monthYear));
        $cek = Penjualan::where('periode', $parsedMonthYear)->first();
        if (!$cek) {
            $count = Penjualan::all();
            $id = count($count) + 1;
            Penjualan::create([
                'id' => $id,
                'periode' => $parsedMonthYear,
                'stok_awal' => $request->stokAwal,
                'stok_akhir' => $request->stokAkhir,
                'terjual' => $request->terjual,
                'pendapatan' => $request->pendapatan
            ]);

            $lastPeriod = Penjualan::orderBy('periode', 'DESC')->first();
            $lastPeriod = date('F Y', strtotime('+1 months', strtotime($lastPeriod->periode)));

            $response = [
                'response' => 'success',
                'monthYear' => $request->monthYear,
                'lastPeriod' => $lastPeriod
            ];
            return response()->json($response);
        } else {
            $response = [
                'response' => 'failed',
                'monthYear' => $request->monthYear
            ];
            return response()->json($response);
        }
    }

    public function edit(Request $request)
    {
        $update = Penjualan::where('id', $request->id)->update([
            'stok_awal' => $request->stokAwal,
            'stok_akhir' => $request->stokAkhir,
            'terjual' => $request->terjual,
            'pendapatan' => $request->pendapatan
        ]);
        if ($update) {
            return response()->json("success");
        }
    }

    public function prediksi()
    {
        $dataPenjualanTerakhir = Penjualan::orderBy('periode', 'DESC')->first();
        $prediksiPeriode = [];
        for ($i = 1; $i <= 5; $i++) {
            $prediksiPeriode[] = date('F Y', strtotime('+ ' . $i . ' months', strtotime($dataPenjualanTerakhir->periode)));
        }

        return view('prediksi', compact('prediksiPeriode'));
    }

    public function knn(Request $request)
    {
        $dataPenjualan = Penjualan::all();
        $avg = ($dataPenjualan->sum('pendapatan') + $dataPenjualan->sum('terjual')) / count($dataPenjualan);
        $avg = number_format((float)$avg, 0, '.', '');

        $ktotal = [];
        foreach ($dataPenjualan as $p) {
            $penjualan = $p->terjual + $p->pendapatan;
            if ($penjualan > $avg) {
                $klasifikasi = "naik";
            } elseif ($penjualan < $avg) {
                $klasifikasi = "turun";
            }

            $terjual = $p->terjual - $request->terjual;
            $terjual = pow($terjual, 2);

            $pendapatan = $p->pendapatan - $request->pendapatan;
            $pendapatan = pow($pendapatan, 2);

            $euclidean = $terjual + $pendapatan;
            $euclidean = number_format((float)sqrt($euclidean), 2, '.', '');

            $ktotal[] = [
                "periode" => date('F Y', strtotime($p->periode)),
                "terjual" => $p->terjual,
                "pendapatan" => $p->pendapatan,
                "klasifikasi" => $klasifikasi,
                "euclidean" => $euclidean
            ];
        }

        $response["dataPenjualan"] = $ktotal;
        foreach ($response["dataPenjualan"] as $key => $value) {
            $response["dataPenjualan"][$key]["pendapatan"] = number_format($response["dataPenjualan"][$key]["pendapatan"]);
        }

        usort($ktotal, function ($a, $b) {
            $a = $a["euclidean"];
            $b = $b["euclidean"];

            return ($a < $b) ? -1 : 1;
        });

        $knearest = [];
        for ($i = 0; $i < $request->k; $i++) {
            $knearest[] = [
                "periode" => $ktotal[$i]["periode"],
                "terjual" => $ktotal[$i]["terjual"],
                "pendapatan" => $ktotal[$i]["pendapatan"],
                "klasifikasi" => $ktotal[$i]["klasifikasi"],
                "euclidean" => $ktotal[$i]["euclidean"]
            ];
        }

        $count = array_count_values(array_column($knearest, 'klasifikasi'));

        if (array_key_exists('naik', $count)) {
            $naik = $count["naik"];
        } else {
            $naik = 0;
        }

        if (array_key_exists('turun', $count)) {
            $turun = $count["turun"];
        } else {
            $turun = 0;
        }

        if ($naik > $turun) {
            $result = "naik";
        } else {
            $result = "turun";
        }

        $response["knearest"] = $knearest;
        $response["prediksi"] = [
            "periode" => $request->periode,
            "terjual" => $request->terjual,
            "pendapatan" => number_format($request->pendapatan),
            "result" => $result
        ];
        $response["k"] = $request->k;

        foreach ($response["knearest"] as $key => $value) {
            $response["knearest"][$key]["pendapatan"] = number_format($response["knearest"][$key]["pendapatan"]);
        }

        return response()->json($response);
    }
}
