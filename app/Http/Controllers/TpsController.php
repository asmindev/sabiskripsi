<?php

namespace App\Http\Controllers;

use App\Models\TPS;
use Illuminate\Http\Request;

class TpsController extends Controller
{
    public function index()
    {
        return response()->json(TPS::all());
    }

    public function create()
    {
        return view('admins.tps.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'kapasitas' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $tps = TPS::create($data);

        return redirect()->route('admin.tps')->with('success', 'TPS berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $tps = TPS::with('armada')->findOrFail($id);

        return view('admins.tps.edit', compact('tps'));
    }

    public function update(Request $request, $id)
    {
        $tps = TPS::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'kapasitas' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $tps->update($data);

        return redirect()->route('admin.tps')->with('success', 'TPS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tps = TPS::findOrFail($id);

        // If TPS is assigned to an armada, unassign it first
        if ($tps->armada_id) {
            $tps->update(['armada_id' => null]);
        }

        $tps->delete();

        return redirect()->route('admin.tps')->with('success', 'TPS berhasil dihapus!');
    }
}
