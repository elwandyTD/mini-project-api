<?php

namespace App\Http\Controllers;

use App\Models\Item_Penjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::orderBy('id_nota', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success get all transaction',
            'data' => $penjualan,
        ], Response::HTTP_OK);
    }

    public function user($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $penjualan = Penjualan::where('kode_pelanggan', $id)->get();;

            return response()->json([
                'success' => true,
                'message' => 'Success get user transaction',
                'data' => $penjualan,
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Get akun gagal',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function detail($id)
    {
        try {

            $penjualan = Penjualan::find($id);

            if (!$penjualan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $items = DB::table('barang')->join('item_penjualan', 'barang.kode', '=', 'item_penjualan.kode_barang')->join('kategori', 'barang.kategori', '=', 'kategori.kode_kategori')->select('barang.*', 'kategori.title', 'item_penjualan.qty AS quantity', 'item_penjualan.total')->where('item_penjualan.nota', '=', $id)->get();

            return response()->json([
                'success' => true,
                'message' => 'Success get user transaction',
                'data' => $items,
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Get akun gagal',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_pelanggan' => ['required'],
            'items' => ['required'],
            'subtotal' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $penjualanLama = Penjualan::orderBy('id_nota', 'desc')->first();
            $penjualanId = '';

            if ($penjualanLama) {
                $penjualanId = explode('_', $penjualanLama->id_nota)[1];
                $penjualanId = 'NOTA_' . $penjualanId + 1;
            } else {
                $penjualanId = 'NOTA_1';
            }

            $penjualan = Penjualan::create([
                'id_nota' => $penjualanId,
                'kode_pelanggan' => $request->kode_pelanggan,
                'subtotal' => $request->subtotal,
            ]);

            foreach ($request->items as $item) {
                Item_Penjualan::create([
                    'nota' => $penjualanId,
                    'kode_barang' => $item["kode_barang"],
                    'qty' => $item["qty"],
                    'total' => $item["total"],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Success transaction items',
                'data' => $penjualan,
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal tambah penjualan',
                'error' => $e->errorInfo
            ]);
        }
    }
}
