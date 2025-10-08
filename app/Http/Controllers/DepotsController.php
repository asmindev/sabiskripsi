<?php

namespace App\Http\Controllers;

use App\Models\Depots;
use Illuminate\Http\Request;

class DepotsController extends Controller
{
    public function index()
    {
        $depoData = Depots::all();

        return view('admins.depot.index', [
            'depoData' => $depoData
        ]);
    }

    public function create()
    {
        return view('admins.depot.create');
    }

    public function edit($id)
    {
        $depot = Depots::findOrFail($id);
        return view('admins.depot.edit', compact('depot'));
    }

    public function show($id)
    {
        $depo = Depots::findOrFail($id);
        return response()->json($depo);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:aktif,maintenance,nonaktif',
            'type' => 'required|in:endpoint,startpoint,regular',
        ]);

        Depots::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Depot created']);
        }

        return redirect()->route('admin.depot.index')->with('success', 'Depot berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $depo = Depots::findOrFail($id);
        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:aktif,maintenance,nonaktif',
            'type' => 'required|in:endpoint,startpoint,regular',
        ]);

        $depo->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Depot updated']);
        }

        return redirect()->route('admin.depot.index')->with('success', 'Depot berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        Depots::findOrFail($id)->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Depot deleted']);
        }

        return redirect()->route('admin.depot.index')->with('success', 'Depot berhasil dihapus');
    }
}
