<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\T_Produk;
use App\Models\TotalPerbulan;
use App\Models\TransaksiV2;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function clear()
    {
        $transaksi = TransaksiV2::all();
        foreach ($transaksi as $t) {
            TransaksiV2::find($t->id)->delete();
        }

        $produk = T_Produk::all();
        foreach ($produk as $p) {
            T_Produk::find($p->id)->delete();
        }

        $monthly = TotalPerbulan::all();
        foreach ($monthly as $m) {
            TotalPerbulan::find($m->id)->delete();
        }
        // $tproduk = T_Produk::where('id_produk', 'RF1')->whereYear('tanggal', '2019')->get();
        // $tproduk = T_Produk::where('id_produk', 'RF1')->whereHas('transaksi', function ($q) {
        //     $q->where('tanggal', '2019-01-01');
        // })->get();
        // return response()->json($tproduk);
    }

    public function generate_transaction()
    {
        function getDatesFromRange($start, $end, $format = 'Y-m-d')
        {
            $array = array();
            $interval = new DateInterval('P1D');
            $realEnd = new DateTime($end);
            $realEnd->add($interval);
            $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

            foreach ($period as $date) {
                $array[] = $date->format($format);
            }

            return $array;
        }

        $date = getDatesFromRange('2019-01-01', '2021-12-31');
        // return response()->json($date);

        $all_product = Produk::all();

        foreach ($date as $d) {
            $buy_amount = rand(2, 5);
            for ($i = 1; $i <= $buy_amount; $i++) {
                $id_transaksi = "T" . $this->random('num', 6);
                while (true) {
                    $check = TransaksiV2::where('id', $id_transaksi)->first();
                    if ($check) {
                        $id_transaksi = "T" . $this->random('num', 9);
                    } else {
                        break;
                    }
                }

                TransaksiV2::create([
                    "id" => $id_transaksi,
                    "tanggal" => $d,
                    "created_at" => date('Y-m-d H:i', strtotime($d . " " . rand(10, 20) . ":" . rand(0, 10)))
                ]);

                $amount = rand(1, 3);
                $products_to_add = [];
                for ($j = 0; $j < $amount; $j++) {
                    $to_add = $all_product[rand(0, 13)]->id;
                    $check = array_search($to_add, $products_to_add);
                    if ($check === false) {
                        $products_to_add[] = $to_add;
                    }
                }

                foreach ($products_to_add as $p) {
                    $harga = Produk::find($p)->harga;
                    $jml = rand(1, 3);
                    T_Produk::create([
                        "id_transaksi" => $id_transaksi,
                        "id_produk" => $p,
                        "harga" => $harga,
                        "terjual" => $jml,
                        "total" => $harga * $jml
                    ]);
                }

                $tv2 = TransaksiV2::find($id_transaksi);

                $tv2->update([
                    "terjual" => $tv2->produk->sum('terjual'),
                    "total" => $tv2->produk->sum('total')
                ]);
            }
        }

        return response()->json([
            "response" => "success",
            "message" => "Transaction generated successfully !"
        ]);
    }

    public function monthly()
    {
        $transaksi = TransaksiV2::orderBy('tanggal')->get();
        foreach ($transaksi as $t) {
            $monthly = TotalPerbulan::whereYear('periode', date('Y', strtotime($t->tanggal)))->whereMonth('periode', date('m', strtotime($t->tanggal)))->first();
            if ($monthly) {
                $monthly->update([
                    "terjual" => $monthly->terjual + $t->produk->sum('terjual'),
                    "pendapatan" => $monthly->pendapatan + $t->produk->sum('total')
                ]);
            } else {
                $periode = date('Y', strtotime($t->tanggal)) . '-' . date('m', strtotime($t->tanggal)) . '-' . '01';
                $periode = date('Y-m-d', strtotime($periode));

                TotalPerbulan::create([
                    'periode' => $periode,
                    'terjual' => $t->produk->sum('terjual'),
                    'pendapatan' => $t->produk->sum('total')
                ]);
            }
        }
    }
}
