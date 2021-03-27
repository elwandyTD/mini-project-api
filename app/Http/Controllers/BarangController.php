<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BarangController extends Controller
{
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

        if ($request->hasFile('images') && is_array($request->images)) {
            foreach ($request->file('images') as $image) {
                var_dump($image, 21);
            }
            var_dump($request->input('images'), 25);
        }

        $fileNames = [];

        if ($request->maxGambar && !$request->images) {
            for ($i = 1; $i <= $request->maxGambar; $i++ ) {
                $gambar = $request->file('image' . $i);
                $namaGambar = time() . '-' . rand(1, 99) . '.' . $gambar->getClientOriginalExtension();

                $gambar->move(public_path('images/barang'), $namaGambar);

                $fileNames[] = $namaGambar;

            }

            return response()->json($fileNames, 200);

        }


        // foreach($request->file('images') as $image)
        // {
        //     $image->move(public_path('/images/barang'), 'htahah.jpg');
        //     // $image->move(public_path('/images/barang'), 'htahah.jpg');
        //     // $fileNames[] = $imageName;
        // }

        // $images = json_encode($fileNames);

        return response()->json($fileNames, 200);
    }
}
