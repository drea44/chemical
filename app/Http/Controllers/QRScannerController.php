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
        $code    = $rawCode;

        $chemical = null;

        // Support CHEM:-prefixed QR codes (primary format)
        $chemicalCode = str_starts_with($code, 'CHEM:')
            ? substr($code, 5)
            : $code;

        $chemical = Chemical::where('chemical_code', $chemicalCode)
            ->orWhere('batch_number', $chemicalCode)
            ->with(['category', 'location'])
            ->first();

        // NOTE: Intentionally NOT supporting raw numeric ID lookup to prevent IDOR enumeration.
        // Also removed URL-pattern matching — QR codes should only contain CHEM: or chemical_code values.

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
