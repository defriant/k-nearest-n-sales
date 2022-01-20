<?php

namespace App\Http\Controllers;

use App\Models\TotalPerbulan;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WebController extends Controller
{
    function random($type, $length)
    {
        $result = "";
        if ($type == 'char') {
            $char = 'ABCDEFGHJKLMNPRTUVWXYZ';
            $max        = strlen($char) - 1;
            for ($i = 0; $i < $length; $i++) {
                $rand = mt_rand(0, $max);
                $result .= $char[$rand];
            }
            return $result;
        } elseif ($type == 'num') {
            $char = '123456789';
            $max        = strlen($char) - 1;
            for ($i = 0; $i < $length; $i++) {
                $rand = mt_rand(0, $max);
                $result .= $char[$rand];
            }
            return $result;
        } elseif ($type == 'mix') {
            $char = 'A1B2C3D4E5F6G7H8J9KLMNPRTUVWXYZ';
            $max = strlen($char) - 1;
            for ($i = 0; $i < $length; $i++) {
                $rand = mt_rand(0, $max);
                $result .= $char[$rand];
            }
            return $result;
        }
    }

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
        $dataPenjualan = TotalPerbulan::orderBy('periode')->get();
        $periode = [];
        foreach ($dataPenjualan as $dp) {
            $periodeDp = date('Y', strtotime($dp->periode));
            $cek = array_search($periodeDp, $periode);
            if ($cek === false) {
                $periode[] = $periodeDp;
            }
        }

        $pendapatan = TotalPerbulan::whereYear('periode', end($periode))->sum('pendapatan');
        $terjual = TotalPerbulan::whereYear('periode', end($periode))->sum('terjual');

        return view('dashboard', compact('periode', 'pendapatan', 'terjual'));
    }

    public function data_pendapatan(Request $request)
    {
        if ($request->tahun == null) {
            $periode = TotalPerbulan::orderBy('periode', 'DESC')->first();
            $periode = date('Y', strtotime($periode->periode));
            $penjualan = TotalPerbulan::whereYear('periode', $periode)->get();
            $pendapatan = [];
            foreach ($penjualan as $p) {
                $pendapatan[] = $p->pendapatan;
            }

            $totalPendapatan = TotalPerbulan::whereYear('periode', $periode)->sum('pendapatan');
            $terjual = TotalPerbulan::whereYear('periode', $periode)->sum('terjual');

            $response = [
                "pendapatan" => $pendapatan,
                "totalPendapatan" => number_format($totalPendapatan),
                "terjual" => number_format($terjual)
            ];

            return response()->json($response);
        } else {
            $periode = $request->tahun;
            $penjualan = TotalPerbulan::whereYear('periode', $periode)->get();
            $pendapatan = [];
            foreach ($penjualan as $p) {
                $pendapatan[] = $p->pendapatan;
            }

            $totalPendapatan = TotalPerbulan::whereYear('periode', $periode)->sum('pendapatan');
            $terjual = TotalPerbulan::whereYear('periode', $periode)->sum('terjual');

            $response = [
                "pendapatan" => $pendapatan,
                "totalPendapatan" => number_format($totalPendapatan),
                "terjual" => number_format($terjual)
            ];

            return response()->json($response);
        }
    }

    public function get_produk()
    {
        $data = Produk::all();

        foreach ($data as $d) {
            $d["harga"] = "Rp. " . number_format($d->harga);
        }

        $response = [
            "data" => $data
        ];

        return response()->json($response);
    }

    public function input_produk(Request $request)
    {
        $produk = Produk::all();
        $produk_count = count($produk) + 1;

        while (true) {
            $id = "RF" . $produk_count;
            $cek = Produk::where('id', $id)->first();
            if ($cek) {
                $produk_count = $produk_count + 1;
            } else {
                break;
            }
        }

        Produk::create([
            'id' => $id,
            'nama' => $request->nama,
            'harga' => $request->harga
        ]);

        $response = [
            "response" => "success",
            "message" => "Berhasil menambahkan data produk"
        ];
        return response()->json($response);
    }

    public function update_produk(Request $request)
    {
        Produk::where('id', $request->id)->update([
            "nama" => $request->nama,
            "harga" => $request->harga
        ]);

        $response = [
            "response" => "success",
            "message" => "Produk " . $request->id . " berhasil di update"
        ];
        return response()->json($response);
    }

    public function delete_produk(Request $request)
    {
        $data = Produk::where('id', $request->id)->first();
        Produk::where('id', $request->id)->delete();

        $response = [
            "response" => "success",
            "message" => $data->nama . " berhasil di hapus"
        ];

        return response()->json($response);
    }

    public function transaksi()
    {
        $produk = Produk::all();
        return view('transaksi', compact('produk'));
    }

    public function get_transaksi(Request $request)
    {
        $transaksi = Transaksi::where('tanggal', $request->tanggal)->get();
        $response = [
            "response" => "success",
            "data" => $transaksi
        ];

        return response()->json($response);
    }

    public function input_transaksi(Request $request)
    {
        $produk = Produk::find($request->id_produk);
        $cek = Transaksi::where('tanggal', $request->tanggal)->where('id_produk', $request->id_produk)->first();
        if ($cek) {
            Transaksi::where('id', $cek->id)->update([
                "harga" => $produk->harga,
                "terjual" => $cek->terjual + $request->terjual,
                "total_harga" => $cek->total_harga + ($produk->harga * $request->terjual)
            ]);
        } else {
            Transaksi::create([
                "tanggal" => $request->tanggal,
                "id_produk" => $request->id_produk,
                "produk" => $produk->nama,
                "harga" => $produk->harga,
                "terjual" => $request->terjual,
                "total_harga" => $produk->harga * $request->terjual
            ]);
        }

        $month = date('m', strtotime($request->tanggal));
        $year = date('Y', strtotime($request->tanggal));
        $cek = TotalPerbulan::whereMonth('periode', $month)->whereYear('periode', $year)->first();
        if ($cek) {
            TotalPerbulan::where('id', $cek->id)->update([
                "terjual" => $cek->terjual + $request->terjual,
                "pendapatan" => $cek->pendapatan + ($produk->harga * $request->terjual)
            ]);
        } else {
            TotalPerbulan::create([
                "periode" => $year . "-" . $month . "-01",
                "terjual" => $request->terjual,
                "pendapatan" => $produk->harga * $request->terjual
            ]);
        }

        $response = [
            "response" => "success",
            "message" => "Berhasil input transaksi"
        ];

        return response()->json($response);
    }

    public function get_transaksi_perbulan(Request $request)
    {
        $year = date('Y', strtotime($request->periode));
        $month = date('m', strtotime($request->periode));

        $produk = Produk::all();
        $data = [];

        foreach ($produk as $prod) {
            $periode = Transaksi::where('id_produk', $prod->id)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->get();
            $terjual = 0;
            $pendapatan = 0;
            foreach ($periode as $period) {
                $terjual = $terjual + $period->terjual;
                $pendapatan = $pendapatan + $period->total_harga;
            }

            $data[] = [
                "periode" => $request->periode,
                "produk" => $prod->nama,
                "terjual" => $terjual,
                "pendapatan" => $pendapatan
            ];
        }

        $totalPerbulan = TotalPerbulan::whereMonth('periode', $month)->whereYear('periode', $year)->first();
        $total_terjual = 0;
        $total_pendapatan = 0;
        if ($totalPerbulan) {
            $total_terjual = $totalPerbulan->terjual;
            $total_pendapatan = $totalPerbulan->pendapatan;
        }

        $response = [
            "response" => "success",
            "data" => $data,
            "total_terjual" => $total_terjual,
            "total_pendapatan" => $total_pendapatan
        ];
        return response()->json($response);
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
        $dataPenjualanTerakhir = TotalPerbulan::orderBy('periode', 'DESC')->first();
        $prediksiPeriode = [];
        for ($i = 1; $i <= 5; $i++) {
            $prediksiPeriode[] = date('F Y', strtotime('+ ' . $i . ' months', strtotime($dataPenjualanTerakhir->periode)));
        }

        return view('prediksi', compact('prediksiPeriode'));
    }

    public function knn(Request $request)
    {
        $dataPenjualan = TotalPerbulan::all();
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

    // public function generate_total_perbulan()
    // {
    //     $transaksi = Transaksi::all();
    //     $periode = [];
    //     $data = [];

    //     foreach ($transaksi as $t) {
    //         $t_periode = date('Y-m', strtotime($t->tanggal));
    //         $t_periode = $t_periode . "-01";
    //         $cek = array_search($t_periode, $periode);
    //         if ($cek === false) {
    //             $periode[] = $t_periode;
    //         }
    //     }

    //     foreach ($periode as $period) {
    //         $month = date('m', strtotime($period));
    //         $year = date('Y', strtotime($period));

    //         $data[] = [
    //             "periode" => $period,
    //             "terjual" => Transaksi::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('terjual'),
    //             "pendapatan" => Transaksi::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('total_harga')
    //         ];
    //     }

    //     foreach ($data as $d) {
    //         TotalPerbulan::create([
    //             "periode" => $d["periode"],
    //             "terjual" => $d["terjual"],
    //             "pendapatan" => $d["pendapatan"]
    //         ]);
    //     }
    // }

    // public function cek()
    // {
    //     // $transaksi = Transaksi::where('terjual', 0)->where('total_harga', 0)->get();
    //     // $transaksi = count($transaksi);

    //     // $transaksi = Transaksi::where('terjual', '>', 0)->where('total_harga', 0)->get();
    //     // $transaksi = count($transaksi);

    //     // $transaksi = Transaksi::where('terjual', 0)->where('total_harga', '>', 0)->get();
    //     // $transaksi = count($transaksi);

    //     // $transaksi = Transaksi::all();
    //     // foreach ($transaksi as $t) {
    //     //     Transaksi::where('id', $t->id)->update([
    //     //         "created_at" => date('Y-m-d H:i:s'),
    //     //         "updated_at" => date('Y-m-d H:i:s')
    //     //     ]);
    //     // }

    //     // dd($transaksi);
    // }
}
