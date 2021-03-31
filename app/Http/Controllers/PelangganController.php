<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PelangganController extends Controller
{
    public function profile($email)
    {
        $pelanggan = Pelanggan::select('id_pelanggan', 'nama', 'email', 'photo', 'domisili', 'jenis_kelamin')->where('email', $email)->first();

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success get user',
            'data' => [
                'nama' => $pelanggan->nama,
                'email' => $pelanggan->email,
                'photo' => '/images/photo/' . $pelanggan->photo,
                'domisili' => $pelanggan->domisili,
                'jenis_kelamin' => $pelanggan->jenis_kelamin,
                'created_at' => $pelanggan->created_at,
            ],
        ], Response::HTTP_OK);

    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required'],
            'email' => ['required', 'email'],
            'password1' => ['required'],
            'password2' => ['required', 'same:password1'],
            'domisili' => ['required'],
            'jenis_kelamin' => ['required', 'in:Pria,Wanita'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pelanggan = Pelanggan::where('email', $request->email)->first();

            if ($pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Register gagal !',
                    'error' => 'Email sudah dipakai !'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $pelangganTerakhir = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
            $pelangganId = '';

            if ($pelangganTerakhir) {
                $pelangganId = explode('_', $pelangganTerakhir->id_pelanggan)[1];
                $pelangganId = 'PELANGGAN_' . $pelangganId + 1;
            } else {
                $pelangganId = 'PELANGGAN_1';
            }


            $pelanggan = Pelanggan::create([
                'id_pelanggan' => $pelangganId,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password1),
                'domisili' => $request->domisili,
                'jenis_kelamin' => $request->jenis_kelamin,
                'photo' => 'user.png'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dibuat',
                'data' => [
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'domisili' => $pelanggan->domisili,
                    'jenis_kelamin' => $pelanggan->jenis_kelamin,
                ],
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tambah akun gagal',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pelanggan = Pelanggan::where('email', $request->email)->first();

            if ($pelanggan) {
                if (Hash::check($request->password, $pelanggan->password)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Success sign in !',
                        'data' => [
                            'nama' => $pelanggan->nama,
                            'email' => $pelanggan->email,
                            'photo' => $pelanggan->photo,
                            'domisili' => $pelanggan->domisili,
                            'jenis_kelamin' => $pelanggan->jenis_kelamin,
                            'created_at' => $pelanggan->created_at,
                        ]
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed sign in !',
                        'error' => 'Wrong email or password !'
                    ], Response::HTTP_UNAUTHORIZED);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed sign in !',
                    'error' => 'Wrong email or password !'
                ], Response::HTTP_UNAUTHORIZED);
            }

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal tambah akun',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function signout(Request $request)
    {
        try {
            $pelanggan = Pelanggan::find($request->id_pelanggan);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed sign out account !'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'success' => true,
                'message' => 'Success sign out account !'
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed sign out',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function update_username(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $pelanggan->nama = $request->nama;

            $pelanggan->save();

            return response()->json([
                'success' => true,
                'message' => 'Success update username !',
                'data' => [
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'photo' => $pelanggan->photo,
                    'domisili' => $pelanggan->domisili,
                    'jenis_kelamin' => $pelanggan->jenis_kelamin,
                    'created_at' => $pelanggan->created_at,
                ],
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed update username',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function update_email(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:pelanggan,email']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $pelanggan->email = $request->email;

            $pelanggan->save();

            return response()->json([
                'success' => true,
                'message' => 'Success update username !',
                'data' => [
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'photo' => $pelanggan->photo,
                    'domisili' => $pelanggan->domisili,
                    'jenis_kelamin' => $pelanggan->jenis_kelamin,
                    'created_at' => $pelanggan->created_at,
                ],
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed update username',
                'error' => $e->errorInfo
            ]);
        }
    }

    public function photo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'mimes:png,jpg,jpeg']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $gambar = $request->file('photo');
            $namaGambar = time() . '-' . rand(1, 999) . '.' . $gambar->getClientOriginalExtension();

            $gambar->move(public_path('images/profile'), $namaGambar);

            if ($pelanggan->photo !== 'user.png') {
                $path = public_path('images/profile/' . $pelanggan->photo);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $pelanggan->photo = $namaGambar;

            $pelanggan->save();

            return response()->json([
                'success' => true,
                'message' => 'Success update photo !',
                'data' => [
                    'nama' => $pelanggan->nama,
                    'email' => $pelanggan->email,
                    'photo' => $pelanggan->photo,
                    'domisili' => $pelanggan->domisili,
                    'jenis_kelamin' => $pelanggan->jenis_kelamin,
                    'created_at' => $pelanggan->created_at,
                ],
            ], Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed update username',
                'error' => $e->errorInfo
            ]);
        }
    }
}
