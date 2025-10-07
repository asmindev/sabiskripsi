<?php

namespace App\Http\Controllers;

use App\Models\Depots;
use Illuminate\Http\Request;

class DepoControllers extends Controller
{
    public function index()
    {
        return response()->json(Depots::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // 'kode' => 'required|unique:depos,kode',
            'nama' => 'required',
            'alamat' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:aktif,maintenance,nonaktif',
        ]);

        $depo = Depots::create($data);
        return response()->json($depo);
    }

    public function update(Request $request, $id)
    {
        $depo = Depots::findOrFail($id);
        $depo->update($request->all());
        return response()->json($depo);
    }

    public function destroy($id)
    {
        Depots::findOrFail($id)->delete();
        return response()->json(['message' => 'Depo deleted']);
    }
}
