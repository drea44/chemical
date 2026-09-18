<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\ChemicalDocument;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChemicalDocumentController extends Controller
{

    public function store(Request $request, Chemical $chemical)
    {
        $this->authorize('update', $chemical);

        $request->validate([
            'document_type' => 'required|in:COA,MSDS',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes'         => 'nullable|string|max:500',
        ], [
            'document_file.required' => 'Pilih file dokumen terlebih dahulu.',
            'document_file.mimes'    => 'Format file harus PDF, JPG, atau PNG.',
            'document_file.max'      => 'Ukuran file maksimal 10 MB.',
            'document_type.required' => 'Pilih tipe dokumen (COA atau MSDS).',
        ]);

        $file = $request->file('document_file');
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return back()->withErrors(['document_file' => 'Tipe file tidak diizinkan. Hanya PDF, JPG, atau PNG.'])->withInput();
        }
        $type = $request->input('document_type');

        $folder   = 'documents/' . Str::slug($chemical->chemical_code);
        $filename = $type . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs($folder, $filename, 'public');

        $document = ChemicalDocument::create([
            'chemical_id'   => $chemical->id,
            'document_type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'notes'         => $request->input('notes'),
            'uploaded_by'   => Auth::id(),
        ]);

        AuditLogService::logCreated('ChemicalDocument', $document->id, [
            'chemical'      => $chemical->chemical_name,
            'document_type' => $type,
            'file_name'     => $document->original_name,
        ]);

        return back()->with('success', "Dokumen {$type} '{$document->original_name}' berhasil diupload.");
    }

    public function download(Chemical $chemical, ChemicalDocument $document)
    {
        $this->authorize('view', $chemical);

        abort_if($document->chemical_id !== $chemical->id, 403);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    public function destroy(Chemical $chemical, ChemicalDocument $document)
    {
        $this->authorize('update', $chemical);

        abort_if($document->chemical_id !== $chemical->id, 403);

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        AuditLogService::logDeleted('ChemicalDocument', $document->id, [
            'chemical'      => $chemical->chemical_name,
            'document_type' => $document->document_type,
            'file_name'     => $document->original_name,
        ]);

        $name = $document->original_name;
        $document->delete();

        return back()->with('success', "Dokumen '{$name}' berhasil dihapus.");
    }
}

