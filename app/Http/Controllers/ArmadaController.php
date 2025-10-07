<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\TPS;
use Illuminate\Http\Request;

class ArmadaController extends Controller
{
    public function index()
    {
        return response()->json(Armada::with('tps')->get());
    }

    public function create()
    {
        // Get only TPS that are not assigned to any armada
        $availableTPS = TPS::whereNull('armada_id')->get();

        return view('admins.armada.create', compact('availableTPS'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'namaTruk' => 'required',
            'nomorPlat' => 'required',
            'kapasitas' => 'required|numeric',
            'status' => 'required',
            'driver' => 'nullable',
            'lastMaintenance' => 'nullable|date',
            'tps_ids' => 'nullable|array',
            'tps_ids.*' => 'exists:t_p_s,id'
        ]);

        $armada = Armada::create($data);

        // Assign TPS to armada
        if (isset($data['tps_ids'])) {
            TPS::whereIn('id', $data['tps_ids'])->update(['armada_id' => $armada->id]);
        }

        return redirect()->route('admin.armada')->with('success', 'Armada berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $armada = Armada::with('tps')->findOrFail($id);

        // Get TPS that are either unassigned OR assigned to this specific armada
        $availableTPS = TPS::where(function ($query) use ($armada) {
            $query->whereNull('armada_id')
                ->orWhere('armada_id', $armada->id);
        })->get();

        return view('admins.armada.edit', compact('armada', 'availableTPS'));
    }

    public function update(Request $request, $id)
    {
        $armada = Armada::findOrFail($id);

        $data = $request->validate([
            'namaTruk' => 'required',
            'nomorPlat' => 'required',
            'kapasitas' => 'required|numeric',
            'status' => 'required',
            'driver' => 'nullable',
            'lastMaintenance' => 'nullable|date',
            'tps_ids' => 'nullable|array',
            'tps_ids.*' => 'exists:t_p_s,id'
        ]);

        $armada->update($data);

        // Update TPS assignments
        // First, remove this armada from all previously assigned TPS
        TPS::where('armada_id', $armada->id)->update(['armada_id' => null]);

        // Then assign new TPS
        if (isset($data['tps_ids'])) {
            TPS::whereIn('id', $data['tps_ids'])->update(['armada_id' => $armada->id]);
        }

        return redirect()->route('admin.armada')->with('success', 'Armada berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $armada = Armada::findOrFail($id);

        // Remove armada assignment from TPS before deleting
        TPS::where('armada_id', $armada->id)->update(['armada_id' => null]);

        $armada->delete();

        return redirect()->route('admin.armada')->with('success', 'Armada berhasil dihapus!');
    }
}
