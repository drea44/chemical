<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use Illuminate\Http\Request;

class QRScannerController extends Controller
{
    public function index()
    {
        return view('qr-scanner.index');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $rawCode = trim($request->input('code'));
        $code = $rawCode;

        $chemical = null;

        // Support URL like http://.../chemicals/12
        if (preg_match('/chemicals\/(\d+)/', $code, $matches)) {
            $chemical = Chemical::with(['category', 'location'])->find($matches[1]);
        }

        if (!$chemical) {
            // Support both raw code and CHEM: prefixed code
            $chemicalCode = str_starts_with($code, 'CHEM:')
                ? substr($code, 5)
                : $code;

            $chemical = Chemical::where('chemical_code', $chemicalCode)
                ->orWhere('batch_number', $chemicalCode)
                ->orWhere('qr_code', 'like', "%{$rawCode}%")
                ->with(['category', 'location'])
                ->first();
        }

        if (!$chemical && is_numeric($code)) {
            $chemical = Chemical::with(['category', 'location'])->find($code);
        }

        if (!$chemical) {
            return response()->json([
                'found'   => false,
                'message' => 'QR code "' . htmlspecialchars($rawCode) . '" not recognized. Chemical not found in registry.',
            ]);
        }

        return response()->json([
            'found'    => true,
            'chemical' => [
                'id'            => $chemical->id,
                'chemical_name' => $chemical->chemical_name,
                'chemical_code' => $chemical->chemical_code,
                'cas_number'    => $chemical->cas_number,
                'current_stock' => number_format($chemical->current_stock, 2),
                'unit'          => $chemical->unit,
                'status'        => $chemical->status,
                'status_color'  => $chemical->status_color,
                'location'      => $chemical->location?->name,
                'expiry_date'   => $chemical->expiry_date?->format('d M Y'),
                'hazard_class'  => $chemical->hazard_class,
                'url'           => route('chemicals.show', $chemical->id),
            ],
        ]);
    }
}
