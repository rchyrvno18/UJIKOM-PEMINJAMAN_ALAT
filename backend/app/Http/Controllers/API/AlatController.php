<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;

class AlatController extends Controller
{
    public function index(): JsonResponse
    {
        $alat = Alat::with('kategori')->latest()->get();

        return response()->json([
            'message' => 'Daftar alat berhasil diambil.',
            'data' => AlatResource::collection($alat),
        ]);
    }

    public function store(StoreAlatRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat = Alat::create($data);
        $alat->load('kategori');

        return response()->json([
            'message' => 'Alat berhasil ditambahkan.',
            'data' => new AlatResource($alat),
        ], 201);
    }

    public function show(Alat $alat): JsonResponse
    {
        $alat->load('kategori');

        return response()->json([
            'data' => new AlatResource($alat),
        ]);
    }

    public function update(UpdateAlatRequest $request, Alat $alat): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);
        $alat->load('kategori');

        return response()->json([
            'message' => 'Alat berhasil diperbarui.',
            'data' => new AlatResource($alat),
        ]);
    }

    public function destroy(Alat $alat): JsonResponse
    {
        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return response()->json([
            'message' => 'Alat berhasil dihapus.',
        ]);
    }
}