<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::get();

        return response()->json([
            'success' => true,
            'message' => 'Get all kategori',
            'data' => $kategori,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $kategoriLama = Kategori::orderBy('kode_kategori', 'desc')->first();
            $kategoriId = '';

            if ($kategoriLama) {
                $kategoriId = explode('_', $kategoriLama->kode_kategori)[1];
                $kategoriId = 'KTGRI_' . $kategoriId + 1;
            } else {
                $kategoriId = 'KTGRI_1';
            }

            $kategori = Kategori::create([
                'kode_kategori' => $kategoriId,
                'title' => $request->title,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambah',
                'data' => $kategori,
            ], Response::HTTP_CREATED);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal tambah kategori',
                'error' => $e->errorInfo
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $kategori->title = $request->title;
            $kategori->save();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate',
                'data' => $kategori,
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update kategori',
                'error' => $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus kategori',
                'error' => $e->errorInfo
            ]);
        }
    }
}
