<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'kondisi' => ['nullable', 'in:baik,rusak_ringan,rusak_berat'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);
        $facilities = Facility::with('category')
            ->when($filters['search'] ?? null, fn ($query, string $search) => $query->where(fn ($query) => $query->where('nama', 'like', '%'.$search.'%')->orWhere('kode', 'like', '%'.$search.'%')->orWhere('lokasi', 'like', '%'.$search.'%')))
            ->when($filters['kondisi'] ?? null, fn ($query, string $kondisi) => $query->where('kondisi', $kondisi))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.facilities.index', compact('facilities', 'filters'));
    }

    public function show(Facility $facility): View
    {
        $facility->load(['category']);
        return view('admin.facilities.show', compact('facility'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('admin.facilities.form', ['facility' => new Facility(), 'categories' => $categories, 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori_id' => ['nullable', 'exists:kategoris,id'],
            'nama' => ['required', 'string', 'max:150'],
            'kode' => ['required', 'string', 'max:50', 'unique:saranas,kode'],
            'lokasi' => ['required', 'string', 'max:150'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('facilities', 'public');
        }

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')->with('success', 'Sarana berhasil ditambahkan.');
    }

    public function edit(Facility $facility): View
    {
        $categories = Category::all();

        return view('admin.facilities.form', ['facility' => $facility, 'categories' => $categories, 'mode' => 'edit']);
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'kategori_id' => ['nullable', 'exists:kategoris,id'],
            'nama' => ['required', 'string', 'max:150'],
            'kode' => ['required', 'string', 'max:50', 'unique:saranas,kode,'.$facility->id],
            'lokasi' => ['required', 'string', 'max:150'],
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('facilities', 'public');
        }

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')->with('success', 'Sarana berhasil diperbarui.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        if ($facility->complaints()->exists()) {
            return back()->with('error', 'Sarana tidak dapat dihapus karena memiliki riwayat pengaduan.');
        }

        $facility->delete();

        return redirect()->route('admin.facilities.index')->with('success', 'Sarana berhasil dihapus.');
    }
}
