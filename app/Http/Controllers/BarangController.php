<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BarangController extends Controller
{
    public function index()
    {
        // $barang = Barang::orderBy('kode', 'desc')->get();
        $barang = DB::table('barang')->join('kategori', 'barang.kategori', '=', 'kategori.kode_kategori')->orderBy('barang.created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Get all item',
            'data' => $barang,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required'],
            'kategori' => ['required'],
            'harga' => ['required', 'numeric'],
            'qty' => ['required', 'numeric'],
            'maxGambar' => ['numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileNames = [];

        if ($request->maxGambar && !$request->images) {
            for ($i = 1; $i <= $request->maxGambar; $i++ ) {
                $gambar = $request->file('image' . $i);
                $namaGambar = time() . '-' . rand(1, 999) . '.' . $gambar->getClientOriginalExtension();

                $gambar->move(public_path('images/barang'), $namaGambar);

                $fileNames[] = $namaGambar;

            }
        }

        try {
            $barangLama = Barang::orderBy('kode', 'desc')->first();
            $barangId = '';

            if ($barangLama) {
                $barangId = explode('_', $barangLama->kode)[1];
                $barangId = 'BRG_' . $barangId + 1;
            } else {
                $barangId = 'BRG_1';
            }

            $barang = Barang::create([
                'kode' => $barangId,
                'nama' => $request->nama,
                'kategori' => $request->kategori,
                'harga' => $request->harga,
                'qty' =>  $request->qty,
                'gambar' => json_encode($fileNames),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambah',
                'data' => $barang,
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal tambah barang',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required'],
            'kategori' => ['required'],
            'harga' => ['required', 'numeric'],
            'qty' => ['required', 'numeric'],
            'maxGambar' => ['numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $barang = Barang::find($id);

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $barang->nama = $request->nama;
            $barang->kategori = $request->kategori;
            $barang->harga = $request->harga;
            $barang->qty = $request->qty;

            $fileNames = [];

            if ($request->maxGambar && !$request->images) {
                for ($i = 1; $i <= $request->maxGambar; $i++ ) {
                    $gambar = $request->file('image' . $i);
                    $namaGambar = time() . '-' . rand(1, 999) . '.' . $gambar->getClientOriginalExtension();

                    $gambar->move(public_path('images/barang'), $namaGambar);

                    $fileNames[] = $namaGambar;

                }

                $images = json_decode($barang->gambar);

                foreach ($images as $image) {
                    $path = public_path('images/barang/' . $image);
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }

                $barang->gambar = json_encode($fileNames);
            }

            $barang->save();

            return response()->json([
                'success' => true,
                'message' => 'Success update item',
                'data' => $barang,
            ], Response::HTTP_OK);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update barang',
                'error' => $e->errorInfo
            ]);
        }

    }

    public function destroy($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $images = json_decode($barang->gambar);

            $barang->delete();

            foreach ($images as $image) {
                $path = public_path('images/barang/' . $image);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Success delete item !',
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus barang',
                'error' => $e->errorInfo
            ]);
        }

        return response()->json(json_decode($barang->gambar), 200);
    }
}
