<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChemicalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemical_id',
        'document_type',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'notes',
        'uploaded_by',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function chemical()
    {
        return $this->belongsTo(Chemical::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Returns human-readable file size (e.g., "1.2 MB")
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
        }
    }

    /**
     * Full public URL to the file
     */
    public function getPublicUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Badge color for document type
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->document_type) {
            'COA'  => 'blue',
            'MSDS' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Check if file is a PDF (for inline view)
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
