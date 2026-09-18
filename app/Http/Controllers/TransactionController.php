<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\ChemicalCategory;
use App\Models\ChemicalDailyUsage;
use App\Models\ChemicalLocation;
use App\Models\ChemicalLogDate;
use App\Models\ChemicalMonthlyBalance;
use App\Services\AuditLogService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TransactionController extends Controller
{

    public function masterReport(Request $request)
    {
        $search = trim($request->get('search', ''));
        $year   = $request->get('year', '2026');

        $reportMonths = [
            $year . '-03' => 'March',
            $year . '-04' => 'April',
            $year . '-05' => 'May',
            $year . '-06' => 'June',
        ];

        $query = Chemical::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('chemical_name', 'like', "%{$search}%")
                  ->orWhere('chemical_code', 'like', "%{$search}%")
                  ->orWhere('cas_number', 'like', "%{$search}%");
            });
        }

        $perPageParam = $request->get('per_page', 'all');
        if ($perPageParam === 'all' || (int)$perPageParam <= 0) {
            $perPage = 500;
        } else {
            $perPage = (int)$perPageParam;
        }

        $chemicals = $query->orderByRaw('COALESCE(sort_order, 9999) ASC')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $chemicalIds = $chemicals->pluck('id');
        $balances = ChemicalMonthlyBalance::whereIn('chemical_id', $chemicalIds)
            ->whereIn('period_month', array_keys($reportMonths))
            ->get()
            ->groupBy('chemical_id');

        $matrix = [];
        foreach ($chemicals as $chem) {
            $chemBalances = $balances->get($chem->id, collect())->keyBy('period_month');
            $matrix[$chem->id] = [];

            foreach ($reportMonths as $mKey => $mLabel) {
                $mb = $chemBalances->get($mKey);
                if ($mb && $mb->saldo_awal !== null) {
                    $matrix[$chem->id][$mKey] = [
                        'amount' => (float) $mb->saldo_awal,
                        'unit'   => $mb->unit ?? null,
                    ];
                } else {
                    $matrix[$chem->id][$mKey] = null;
                }
            }
        }

        $totalChemicals = Chemical::count();

        return view('transactions.master-report', compact(
            'chemicals',
            'reportMonths',
            'matrix',
            'search',
            'year',
            'totalChemicals'
        ));
    }

    public function warningStock(Request $request)
    {
        $search       = trim($request->get('search', ''));
        $statusFilter = $request->get('status', 'all');

        $query = Chemical::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('chemical_name', 'like', "%{$search}%")
                  ->orWhere('chemical_code', 'like', "%{$search}%")
                  ->orWhere('cas_number', 'like', "%{$search}%");
            });
        }

        if ($statusFilter === 'ok') {
            $query->where(function ($q) {
                $q->where(function ($s) {
                    $s->where('minimum_stock', '>', 0)
                      ->whereColumn('current_stock', '>', 'minimum_stock');
                })->orWhere(function ($s) {
                    $s->where(function ($z) {
                        $z->whereNull('minimum_stock')->orWhere('minimum_stock', '<=', 0);
                    })->where('current_stock', '>=', 0);
                });
            });
        } elseif ($statusFilter === 'refill') {
            $query->where(function ($q) {
                $q->where(function ($s) {
                    $s->where('minimum_stock', '>', 0)
                      ->whereColumn('current_stock', '<=', 'minimum_stock');
                })->orWhere(function ($s) {
                    $s->where(function ($z) {
                        $z->whereNull('minimum_stock')->orWhere('minimum_stock', '<=', 0);
                    })->where('current_stock', '<', 0);
                });
            });
        }

        $perPage   = (int) $request->get('per_page', 50);
        $chemicals = $query->orderByRaw('COALESCE(sort_order, 9999) ASC')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $chemicalIds = $chemicals->pluck('id');
        $baselineBalances = ChemicalMonthlyBalance::whereIn('chemical_id', $chemicalIds)
            ->orderBy('period_month', 'asc')
            ->get()
            ->groupBy('chemical_id')
            ->map(fn($group) => $group->first())
            ->keyBy('chemical_id');

        $rows = [];
        foreach ($chemicals as $c) {
            $mb = $baselineBalances->get($c->id);
            $stokAwal = $mb ? (float)$mb->saldo_awal : (float)$c->current_stock;
            $minStock = (float)($c->minimum_stock ?? 0);
            $remaining = (float)($c->current_stock ?? 0);

            if ($minStock > 0) {
                $status = $remaining > $minStock ? 'OK' : 'Time to Refill';
            } else {
                $status = $remaining < 0 ? 'Time to Refill' : 'OK';
            }

            $rows[$c->id] = [
                'stok_awal'       => $stokAwal,
                'min_stock'       => $minStock,
                'remaining_stock' => $remaining,
                'status'          => $status,
            ];
        }

        $totalCount = Chemical::count();
        $refillCount = Chemical::where(function ($q) {
            $q->where(function ($s) {
                $s->where('minimum_stock', '>', 0)
                  ->whereColumn('current_stock', '<=', 'minimum_stock');
            })->orWhere(function ($s) {
                $s->where(function ($z) {
                    $z->whereNull('minimum_stock')->orWhere('minimum_stock', '<=', 0);
                })->where('current_stock', '<', 0);
            });
        })->count();
        $okCount = $totalCount - $refillCount;

        return view('transactions.warning-stock', compact(
            'chemicals',
            'rows',
            'search',
            'statusFilter',
            'totalCount',
            'okCount',
            'refillCount'
        ));
    }

    public function index(Request $request)
    {

        $periodMonth = $request->get('month', now()->format('Y-m'));
        $parsedMonth = Carbon::createFromFormat('Y-m', $periodMonth);
        $monthTitle  = $parsedMonth->format('F Y');

        $prevMonth = $parsedMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $parsedMonth->copy()->addMonth()->format('Y-m');

        $logDates = ChemicalLogDate::where('period_month', $periodMonth)
            ->orderBy('log_date')
            ->orderBy('id')
            ->get();

        $monthHasData = ChemicalMonthlyBalance::where('period_month', $periodMonth)->exists()
                     || $logDates->isNotEmpty();

        $daysInMonth = $parsedMonth->daysInMonth;
        $existingDays = $logDates->map(fn($d) => (int)$d->log_date->format('d'))->unique()->toArray();
        if ($monthHasData && count($existingDays) < $daysInMonth) {
            $admin = auth()->user() ?? \App\Models\User::first();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                if (!in_array($day, $existingDays)) {
                    ChemicalLogDate::create([
                        'log_date'     => sprintf('%s-%02d', $periodMonth, $day),
                        'period_month' => $periodMonth,
                        'analyst_name' => '',
                        'created_by'   => $admin?->id,
                    ]);
                }
            }
            $logDates = ChemicalLogDate::where('period_month', $periodMonth)
                ->orderBy('log_date')
                ->orderBy('id')
                ->get();
        }

        $query = Chemical::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('chemical_name', 'like', "%{$search}%")
                  ->orWhere('chemical_code', 'like', "%{$search}%")
                  ->orWhere('cas_number', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        if ($catId = $request->get('category')) {
            $query->where('category_id', $catId);
        }

        $perPage   = (int)$request->get('per_page', 100);
        $chemicals = $query->orderByRaw('COALESCE(sort_order, 9999) ASC')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $chemicalIds = $chemicals->pluck('id');
        $dateIds     = $logDates->pluck('id');

        $monthlyBalances = ChemicalMonthlyBalance::whereIn('chemical_id', $chemicalIds)
            ->where('period_month', $periodMonth)
            ->get()
            ->keyBy('chemical_id');

        $prevMonthBalances = ChemicalMonthlyBalance::whereIn('chemical_id', $chemicalIds)
            ->where('period_month', $prevMonth)
            ->get()
            ->keyBy('chemical_id');

        $prevDateIds = ChemicalLogDate::where('period_month', $prevMonth)->pluck('id');
        $prevDailyUsages = ChemicalDailyUsage::whereIn('chemical_id', $chemicalIds)
            ->whereIn('log_date_id', $prevDateIds)
            ->get()
            ->groupBy('chemical_id');

        $dailyUsages = ChemicalDailyUsage::whereIn('chemical_id', $chemicalIds)
            ->whereIn('log_date_id', $dateIds)
            ->get()
            ->groupBy('chemical_id');

        $rows = [];
        foreach ($chemicals as $chem) {
            $mBalance    = $monthlyBalances->get($chem->id);
            $prevBalance = $prevMonthBalances->get($chem->id);

            if ($mBalance) {
                $saldoAwal = (float)$mBalance->saldo_awal;
            } elseif ($prevBalance) {

                $prevSaldoAwal  = (float)$prevBalance->saldo_awal;
                $prevPenerimaan = (float)($prevBalance->penerimaan ?? 0);
                $prevPengeluaran = 0;
                foreach ($prevDailyUsages->get($chem->id, collect()) as $pu) {
                    $prevPengeluaran += (float)($pu->take_1 ?? 0)
                                     + (float)($pu->take_2 ?? 0)
                                     + (float)($pu->take_3 ?? 0);
                }
                $saldoAwal = $prevSaldoAwal + $prevPenerimaan - $prevPengeluaran;
            } else {
                $saldoAwal = (float)$chem->current_stock;
            }

            $penerimaan = (float)($mBalance?->penerimaan ?? 0);

            $cUsages    = $dailyUsages->get($chem->id, collect());
            $usageMap   = [];
            $pengeluaran = 0;

            foreach ($cUsages as $u) {
                $t1 = $u->take_1 !== null ? (float)$u->take_1 : null;
                $t2 = $u->take_2 !== null ? (float)$u->take_2 : null;
                $t3 = $u->take_3 !== null ? (float)$u->take_3 : null;

                $usageMap[$u->log_date_id] = [
                    'take_1' => $t1,
                    'take_2' => $t2,
                    'take_3' => $t3,
                ];

                $pengeluaran += (float)($t1 ?? 0) + (float)($t2 ?? 0) + (float)($t3 ?? 0);
            }

            $saldoAkhir = $saldoAwal + $penerimaan - $pengeluaran;

            $rows[$chem->id] = [
                'saldo_awal'  => $saldoAwal,
                'penerimaan'  => $penerimaan,
                'pengeluaran' => $pengeluaran,
                'saldo_akhir' => $saldoAkhir,
                'usages'      => $usageMap,
            ];
        }

        $categories = ChemicalCategory::orderBy('name')->get(['id', 'name']);

        $analysts = ChemicalLogDate::whereNotNull('analyst_name')
            ->pluck('analyst_name')
            ->merge(['Fitria', 'Rizky'])
            ->unique()
            ->filter()
            ->values();

        $availableMonths = ChemicalMonthlyBalance::select('period_month')
            ->distinct()
            ->orderBy('period_month', 'asc')
            ->pluck('period_month')
            ->toArray();

        return view('transactions.index', compact(
            'chemicals',
            'rows',
            'logDates',
            'periodMonth',
            'monthTitle',
            'prevMonth',
            'nextMonth',
            'categories',
            'analysts',
            'availableMonths'
        ));
    }

    public function storeDate(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403, 'Tidak memiliki akses untuk menambah tanggal log.');

        $request->validate([
            'log_date'     => 'required|date',
            'analyst_name' => 'nullable|string|max:100',
        ]);

        $periodMonth = Carbon::parse($request->log_date)->format('Y-m');
        $analystName = $request->analyst_name ? trim($request->analyst_name) : 'Analyst';

        $logDate = ChemicalLogDate::whereDate('log_date', $request->log_date)
            ->where('analyst_name', $analystName)
            ->first();

        if (!$logDate) {
            $logDate = ChemicalLogDate::create([
                'log_date'     => $request->log_date,
                'analyst_name' => $analystName,
                'period_month' => $periodMonth,
                'created_by'   => auth()->id(),
            ]);
        }

        return redirect()->route('transactions.index', ['month' => $periodMonth])
            ->with('success', "Date {$logDate->log_date->format('d/m/Y')} added.");
    }

    public function deleteDate(ChemicalLogDate $date)
    {

        abort_unless(auth()->user()?->canManageStock(), 403, 'Tidak memiliki akses untuk menghapus tanggal log.');
        $period = $date->period_month;
        $date->delete();

        return redirect()->route('transactions.index', ['month' => $period])
            ->with('success', "Date column deleted successfully.");
    }

    public function updateCell(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);

        $cleanVal = $request->value;
        if (is_string($cleanVal)) {
            $cleanVal = str_replace(',', '.', trim($cleanVal));
        }
        $request->merge(['value' => $cleanVal]);

        $request->validate([
            'chemical_id' => 'required|exists:chemicals,id',
            'log_date_id' => 'required|exists:chemical_log_dates,id',
            'field'       => 'required|in:take_1,take_2,take_3',
            'value'       => 'nullable|numeric|min:0',
        ]);

        $val = ($request->value !== null && $request->value !== '') ? (float)$request->value : null;

        $result = DB::transaction(function () use ($request, $val) {
            $usage = ChemicalDailyUsage::firstOrCreate([
                'chemical_id' => $request->chemical_id,
                'log_date_id' => $request->log_date_id,
            ]);

            $usage->{$request->field} = $val;
            $usage->updated_by = auth()->id();
            $usage->save();

            $logDate = ChemicalLogDate::findOrFail($request->log_date_id);
            $dateIds = ChemicalLogDate::where('period_month', $logDate->period_month)->pluck('id');

            $usages = ChemicalDailyUsage::where('chemical_id', $request->chemical_id)
                ->whereIn('log_date_id', $dateIds)
                ->get();

            $totalPengeluaran = $usages->sum(function ($u) {
                return (float)($u->take_1 ?? 0) + (float)($u->take_2 ?? 0) + (float)($u->take_3 ?? 0);
            });

            $monthlyBalance = ChemicalMonthlyBalance::where('chemical_id', $request->chemical_id)
                ->where('period_month', $logDate->period_month)
                ->first();

            $chemical   = Chemical::findOrFail($request->chemical_id);
            $saldoAwal  = (float)($monthlyBalance?->saldo_awal ?? $chemical->current_stock);
            $penerimaan = (float)($monthlyBalance?->penerimaan ?? 0);
            $saldoAkhir = $saldoAwal + $penerimaan - $totalPengeluaran;

            return compact('totalPengeluaran', 'saldoAkhir');
        });

        $totalPengeluaran = $result['totalPengeluaran'];
        $saldoAkhir       = $result['saldoAkhir'];

        $formattedPengeluaran = floor($totalPengeluaran) == $totalPengeluaran
            ? number_format($totalPengeluaran, 0, ',', '')
            : rtrim(rtrim(number_format($totalPengeluaran, 4, ',', ''), '0'), ',');

        $formattedSaldoAkhir = floor($saldoAkhir) == $saldoAkhir
            ? number_format($saldoAkhir, 0, ',', '')
            : rtrim(rtrim(number_format($saldoAkhir, 4, ',', ''), '0'), ',');

        return response()->json([
            'success'         => true,
            'value'           => $val,
            'pengeluaran'     => $formattedPengeluaran,
            'saldo_akhir'     => $formattedSaldoAkhir,
            'raw_pengeluaran' => $totalPengeluaran,
            'raw_saldo_akhir' => $saldoAkhir,
        ]);
    }

    public function updateBalance(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);
        $request->validate([
            'chemical_id'  => 'required|exists:chemicals,id',
            'period_month' => 'required|string|size:7',
            'field'        => 'required|in:saldo_awal,penerimaan,unit',
            'value'        => 'nullable',
        ]);

        $monthlyBalance = ChemicalMonthlyBalance::firstOrCreate(
            [
                'chemical_id'  => $request->chemical_id,
                'period_month' => $request->period_month,
            ],
            [
                'saldo_awal' => null,
                'penerimaan' => 0,
            ]
        );

        $field  = $request->field;
        $oldVal = $monthlyBalance->{$field};

        if ($field === 'unit') {
            $val = ($request->value !== null && $request->value !== '') ? trim($request->value) : null;
            $monthlyBalance->unit = $val;
            $monthlyBalance->save();

            return response()->json(['success' => true, 'value' => $val]);
        }

        $rawVal = $request->value;
        if (is_string($rawVal)) {
            $rawVal = str_replace(',', '.', trim($rawVal));
        }
        $val = ($rawVal !== null && $rawVal !== '') ? (float)$rawVal : null;
        $monthlyBalance->{$field} = $val;
        $monthlyBalance->save();

        AuditLogService::logUpdated('ChemicalMonthlyBalance', $monthlyBalance->id, [
            $field         => $oldVal,
            'period_month' => $request->period_month,
        ], [
            $field         => $val,
            'period_month' => $request->period_month,
        ]);

        $dateIds = ChemicalLogDate::where('period_month', $request->period_month)->pluck('id');
        $usages  = ChemicalDailyUsage::where('chemical_id', $request->chemical_id)
            ->whereIn('log_date_id', $dateIds)
            ->get();

        $totalPengeluaran = $usages->sum(function ($u) {
            return (float)($u->take_1 ?? 0) + (float)($u->take_2 ?? 0) + (float)($u->take_3 ?? 0);
        });

        $saldoAwal  = (float)$monthlyBalance->saldo_awal;
        $penerimaan = (float)$monthlyBalance->penerimaan;
        $saldoAkhir = $saldoAwal + $penerimaan - $totalPengeluaran;

        $formattedPengeluaran = floor($totalPengeluaran) == $totalPengeluaran
            ? number_format($totalPengeluaran, 0, ',', '')
            : rtrim(rtrim(number_format($totalPengeluaran, 4, ',', ''), '0'), ',');

        $formattedSaldoAkhir = floor($saldoAkhir) == $saldoAkhir
            ? number_format($saldoAkhir, 0, ',', '')
            : rtrim(rtrim(number_format($saldoAkhir, 4, ',', ''), '0'), ',');

        return response()->json([
            'success'     => true,
            'value'       => $val,
            'pengeluaran' => $formattedPengeluaran,
            'saldo_akhir' => $formattedSaldoAkhir,
        ]);
    }

    public function updateChemical(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);

        $request->validate([
            'chemical_id' => 'required|exists:chemicals,id',
            'field'       => 'required|in:chemical_name,unit',
            'value'       => 'required|string|max:255',
        ]);

        $chemical = Chemical::findOrFail($request->chemical_id);
        $old = [$request->field => $chemical->{$request->field}];
        $chemical->{$request->field} = trim($request->value);
        $chemical->updated_by = auth()->id();
        $chemical->save();

        AuditLogService::logUpdated('Chemical', $chemical->id, $old, [$request->field => $chemical->{$request->field}]);

        return response()->json([
            'success' => true,
            'value'   => $chemical->{$request->field},
        ]);
    }

    public function updateAnalyst(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);
        $request->validate([
            'log_date_id'  => 'required|exists:chemical_log_dates,id',
            'field'        => 'nullable|in:analyst_name,analyst_take_1,analyst_take_2,analyst_take_3',
            'analyst_name' => 'nullable|string|max:100',
        ]);

        $logDate = ChemicalLogDate::findOrFail($request->log_date_id);
        $field   = $request->field ?? 'analyst_name';

        $rawVal  = $request->analyst_name;
        $val     = ($rawVal !== null && trim($rawVal) !== '') ? trim($rawVal) : null;

        $logDate->{$field} = $val;
        $logDate->save();

        return response()->json([
            'success'      => true,
            'field'        => $field,
            'analyst_name' => $val,
        ]);
    }

    public function quickAddChemical(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);

        $input = $request->all();
        foreach (['saldo_awal', 'penerimaan', 'minimum_stock', 'current_stock'] as $f) {
            if (isset($input[$f]) && is_string($input[$f])) {
                $input[$f] = str_replace(',', '.', trim($input[$f]));
            }
        }
        if (isset($input['months']) && is_array($input['months'])) {
            foreach ($input['months'] as $mk => $mv) {
                if (is_string($mv)) {
                    $input['months'][$mk] = str_replace(',', '.', trim($mv));
                }
            }
        }
        $request->merge($input);

        $request->validate([
            'chemical_name' => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'period_month'  => 'nullable|string|size:7',
            'saldo_awal'    => 'nullable|numeric|min:0',
            'penerimaan'    => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'redirect_to'   => 'nullable|string|in:master-report,warning-stock,index',
            'months'        => 'nullable|array',
        ]);

        $code         = 'CHM-LOG-' . strtoupper(Str::random(5));
        $category     = ChemicalCategory::first();
        $location     = ChemicalLocation::first();

        $saldoAwal    = (float)($request->saldo_awal ?? $request->current_stock ?? 0);
        $penerimaan   = (float)($request->penerimaan ?? 0);
        $minimumStock = (float)($request->minimum_stock ?? 0);
        $periodMonth  = $request->period_month ?? now()->format('Y-m');

        $totalStock = $saldoAwal + $penerimaan;
        $status = 'SAFE';
        if ($minimumStock > 0 && $totalStock <= $minimumStock) {
            $status = 'LOW';
        }

        $chemical = DB::transaction(function () use (
            $request, $code, $category, $location,
            $saldoAwal, $penerimaan, $minimumStock, $periodMonth,
            $totalStock, $status
        ) {
            $maxOrder = (int)(Chemical::max('sort_order') ?? 0);

            $chemical = Chemical::create([
                'sort_order'    => $maxOrder + 1,
                'chemical_code' => $code,
                'chemical_name' => trim($request->chemical_name),
                'unit'          => trim($request->unit),
                'category_id'   => $category?->id ?? 1,
                'location_id'   => $location?->id ?? 1,
                'current_stock' => $totalStock,
                'minimum_stock' => $minimumStock,
                'status'        => $status,
                'created_by'    => auth()->id(),
                'updated_by'    => auth()->id(),
            ]);

            if ($totalStock > 0) {
                $stockService = app(StockService::class);
                $stockService->createInitialStockTransaction($chemical);
            }

            if ($request->has('months') && is_array($request->months)) {
                foreach ($request->months as $mKey => $val) {
                    if ($val !== null && $val !== '') {
                        $cleanVal = is_string($val) ? (float)str_replace(',', '.', trim($val)) : (float)$val;
                        ChemicalMonthlyBalance::create([
                            'chemical_id'  => $chemical->id,
                            'period_month' => $mKey,
                            'saldo_awal'   => $cleanVal,
                            'penerimaan'   => 0,
                        ]);
                    }
                }
            } elseif ($periodMonth && ($saldoAwal > 0 || $penerimaan > 0)) {
                ChemicalMonthlyBalance::create([
                    'chemical_id'  => $chemical->id,
                    'period_month' => $periodMonth,
                    'saldo_awal'   => $saldoAwal,
                    'penerimaan'   => $penerimaan,
                ]);
            }

            AuditLogService::logCreated('Chemical', $chemical->id, [
                'chemical_name' => $chemical->chemical_name,
                'chemical_code' => $chemical->chemical_code,
                'unit'          => $chemical->unit,
                'initial_stock' => $chemical->current_stock,
                'minimum_stock' => $chemical->minimum_stock,
            ]);

            return $chemical;
        });

        try {
            $qrService = app(\App\Services\QRCodeService::class);
            $qrContent = $qrService->buildContent($chemical->chemical_code);
            $chemical->qr_code = $qrService->generate($chemical->chemical_code, $qrContent);
            $chemical->save();
        } catch (\Throwable $e) {

        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'chemical' => $chemical,
                'message'  => "Chemical '{$chemical->chemical_name}' berhasil ditambahkan.",
            ]);
        }

        $redirectTo = $request->redirect_to;
        if ($redirectTo === 'master-report') {
            return redirect()->route('transactions.master-report', ['highlight' => $chemical->id])
                ->with('success', "Chemical '{$chemical->chemical_name}' berhasil ditambahkan ke Master Report.");
        } elseif ($redirectTo === 'warning-stock') {
            return redirect()->route('transactions.warning-stock', ['highlight' => $chemical->id])
                ->with('success', "Chemical '{$chemical->chemical_name}' berhasil ditambahkan ke Warning Stock.");
        }

        return redirect()->route('transactions.index', ['month' => $periodMonth, 'highlight' => $chemical->id])
            ->with('success', "Chemical '{$chemical->chemical_name}' berhasil ditambahkan ke log.");
    }

    public function updateMinimumStock(Request $request)
    {

        abort_unless(auth()->user()?->canManageStock(), 403);

        $minStock = $request->minimum_stock;
        if (is_string($minStock)) {
            $minStock = str_replace(',', '.', trim($minStock));
        }
        $request->merge(['minimum_stock' => $minStock]);

        $request->validate([
            'chemical_id'   => 'required|exists:chemicals,id',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $chemical = Chemical::findOrFail($request->chemical_id);
        $old = ['minimum_stock' => $chemical->minimum_stock];
        $chemical->minimum_stock = (float) $request->minimum_stock;
        $chemical->updated_by    = auth()->id();
        $chemical->save();

        AuditLogService::logUpdated('Chemical', $chemical->id, $old, ['minimum_stock' => $chemical->minimum_stock]);

        return response()->json([
            'success'       => true,
            'minimum_stock' => $chemical->minimum_stock,
        ]);
    }

    public function destroyChemical(Chemical $chemical)
    {

        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Administrator yang dapat menghapus chemical.');

        $name = $chemical->chemical_name;
        $id   = $chemical->id;

        DB::transaction(function () use ($chemical) {

            $chemical->dailyUsages()->delete();

            $chemical->monthlyBalances()->delete();

            DB::table('stock_transactions')->where('chemical_id', $chemical->id)->delete();

            DB::table('stock_adjustments')->where('chemical_id', $chemical->id)->delete();

            DB::table('chemical_documents')->where('chemical_id', $chemical->id)->delete();

            $chemical->delete();
        });

        AuditLogService::logDeleted('Chemical', $id, ['chemical_name' => $name, 'source' => 'log_sheet']);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Chemical '{$name}' berhasil dihapus."]);
        }

        return redirect()->back()->with('success', "Chemical '{$name}' berhasil dihapus dari log.");
    }

    public static function getReferenceChemicalNames(): array
    {
        return [

            '1,10 - phenanthroline chloride monohydrate',
            '1,10 - phenanthroline monohydrate',
            '1,8-Dihydroxy-2-(4-Sulfophenylazo)-naphthalene-3,6-disulfonic acid trisodium salt',
            '1,5-Diphenylcarbazide',
            '1-Amino-2-hydroxide-4-naphtalene sulfonic acid',
            '1-Butanol',
            '1-Naphtholbenzine',
            '4-amino-2,3-dimethyl-1phenyl-3-pyrazolin-5-one',
            'Acetic acid (glacial) 100%',
            'Acetone p.a',
            'Acetone teknis',
            'Acetone',
            'Alizarin -3- methylamine-N,N diacetic acid dihydrate',
            'Alizarin Red indicator',
            'Alkylbenzyl dimethylammonium chloride',
            'Aluminium foil',
            'Aluminium hydroxide',
            'Aluminium kalium sulfat dodecahydrat',
            'Amidosulfuris acid',
            'Ammonia solution 25 %',
            'Ammonium acetate',
            'Ammonium fluoride',

            'Ammonium heptamolybdate tetrahydrate',
            'Ammonium iron (III) sulfate dodecahydrate',
            'Amonium chloride',
            'Amonium monovanadate',
            'Aquabides',
            'Asam salisilat teknis',
            'Asam sulfamat',
            'Barbituric acid',
            'Barium acetat',
            'Barium chloride dihydrate',
            'Biuret wasserfrei',
            'Boric acid',
            'Brilliant Gree Bile Broth',
            'Bromocresol green indicator',
            'Brucine',
            'Cadmium sulfat hydrate',
            'Calcium carbonate',
            'Calcium hypochlorite',
            'Cerium (III) nitratehex-hydrate',
            'Chloramin T trihydrate',
            'Chloroform',

            'Chromatropic Acid disodium salt',
            'Chromium (VI) oxide',
            'Citric acid monohydrate',
            'Cobalt (II) chloride hexahydrate',
            'Compact dry bc',
            'Compact dry ec',
            'Compact dry etb',
            'Compact dry etc',
            'Compact dry pa',
            'Compact dry SA',
            'Compact dry sl',
            'Compact dry tc',
            'Compact dry tc (2)',
            'Compact dry vp',
            'Compact dry ym',
            'Copper (II) sulfate pentahydrate',
            'Curcumine',
            'Cyclohexane',
            'D (+) Glucose anhydrous for biochemistry',
            'Devarda alloy',
            'Di-ammonium oxalate monohydrate',

            'Di-amonium hydrogen phosphate',
            'Dikalium hydrogen phosphat',
            'Dimedone',
            'Disodium hydrogen phosphate dodecahydrate',
            'Disodium hydrogen phosphate heptahydrate',
            'Di-sodium oxalate',
            'Dodecyl sulfate sodium salt',
            'EC Broth',
            'Eisen (II) sulfate-heptahydrat',
            'EMB Agar',
            'EO STERILE PETRI DISH 90MM CITOTEST',
            'Eosin Y (Yellowish) CI 45380',
            'Erichrome cyanine R CI 4380',
            'Ethanol',
            'Ethanol absolut',
            'Fecal swab 2 ml copan',
            'Ferroin indicator solution',
            'Formaldehyde solution 37%',
            'Glass fiber filter 1 mikron 47 mm 10 pk PALL',
            'Glass fiber filter 0,45 mikron 47 mm 10 pk PALL',
            'Gelatine',

            'Hexamethylene-tetramine',
            'Hydrazinium sulfate',
            'Hydrochloric acid 37%',
            'Hydrochloric acid fuming 37%',
            'Hydrogen peroxide 30%',
            'Hydroxlamine hydrochloride',
            'Hydroxylammonium chloride',
            'Hydroxylammonium sulfate',
            'Indigocarmin',
            'Iodine',
            'Iron (III) Chloride',
            'Iron (II) Sulfate Heptahydrate',
            'Iron test',
            'Isoamyl alkohol',
            'Isobutyl methyl ketone',
            'Kalium bromat',
            'Kalium chlorat',
            'Kalium iodidat',
            'Kaliumantimon (III)-oxidtartrat-hemihydrat',
            'Kertas saring 41 Diameter 125 mm Whatman',
            'Kertas saring 42 Diameter 125 mm Whatman',

            'Kertas saring 43 Diameter 125 mm Whatman',
            'Kertas saring 0,45 mikron Diameter 47 mm Whatman',
            'Kertas saring 0,45 mikron Diameter 47 mm PALL',
            'Kwikstik a. brasiliensis',
            'Kwikstik c. albicans',
            'Kwikstik e. coli',
            'Kwikstik k. aerogenes',
            'Kwikstik s. aureus',
            'Kwikstik s. typhimurium',
            'L (+) Asorbic Acid',
            'Lactose Broth agar',
            'Lanthanum (III) chloride heptahydrate 98%',
            'Lanthanum nitrate hexahydrate',
            'Lauryl Tryptose Broth',
            'Lead (II) acetate trihydrate',
            'Lead (II) oxide',
            'L-Histidine monohydrate chloride monohydrate',
            'M Endo agar',
            'M Fecal coliform agar',
            'MacConkey Agar',
            'Magnesium (II) sulfate monohydrate',

            'Magnesium chloride',
            'Magnesium chloride hexahydrate',
            'Magnesium oxide',
            'Manganese (II) Sulfate Monohydrate',
            'Manganese test',
            'Masker orlee hijab',
            'Masker orlee non hijab',
            'Mercury (II) chloride',
            'Mercury (II) iodide',
            'Mercury (II) oxide',
            'Mercury (II) sulfate',
            'Mercury (II) thiocyanate',
            'Methanol',
            'Methyl Purple Indicator Solution 1 g/L',
            'Methyl red (CI 13020)',
            'Methyl red sodium salt (CI 13020)',
            'Methylenblue',
            'Micropipette 1 ml',
            'Micropipette 5 ml',
            'Micropipette plus vol 100 mikron',
            'Micropipette plus vol 0,5 ml',
            'MUG EC Broth',

            'N-(1-Naphthyl) ethylenediamine dihydro-chloride',
            'N,N-diethyl-1,4 phenyle diammonium sulfat',
            'N,N-dimethyl-1,4 phenylene diamonium dichloride',
            'N,N-Dimethyl-1,4 phenyl-enediamine oxalate',
            'n-Amylalkohol',
            'Naphtholbenzein',
            'Natrium fluoride',
            'Natrium molibdat dihidrat',
            'n-heptane',
            'n-Hexadecane',
            'Nickel (II) nitrate hexahydrate',
            'Nitric acid 65%',
            'Nutrient Agar',
            'Nutrient Broth',
            'Ortho-Phosphoric acid 85%',
            'Oxalic acid dihydrate',
            'Pararosanline (chloride) CI 42500',
            'Perochloric acid 70-72%',
            'Petri Dish Labware Charuzu 60 x 15 mm',
            'Pewarnaan gram (crystal violet)',
            'Pewarnaan gram (lugol)',

            'Pewarnaan gram (safranin)',
            'Phenol',
            'Phenol Red (Phenol sulfonphthalein)',
            'Phenol red indicator',
            'Phenolphathalein indicator',
            'Pipet pasteur plastik 3 ml',
            'Pipet pasteur plastik 5 ml',
            'Pipette Tips 1-10ml',
            'Plate count agar',
            'Plate count agar (2)',
            'Potassium chloride',
            'Potassium chromate',
            'Potassium cyanide',
            'Potassium dichromate',
            'Potassium dihydrogen phosphate',
            'Potassium hexacynoferrate (II) trihydrate',
            'Potassium hexxchloroplatirale (IV)',
            'Potassium hydrogen phthalate',
            'Potassium hydroxide',
            'Potassium Iodide',
            'Potassium nitrate',

            'Potassium permanganat',
            'Potassium peroxodisulfate',
            'Potassium sodium tartrate tetrahydrate',
            'Potassium Sulfate',
            'Potato Dextrose Agar',
            'Potato Dextrose Agar (2)',
            'Pyrroliodine-1-dithiocarboxylic acid ammonium salt',
            'Sabun cuci sunlight',
            'Salicylic Acid',
            'Sarung tangan ukuran S',
            'Sarung tangan ukuran M',
            'Sarung tangan ukuran L',
            'Selenium reagent mixture',
            'Silver nitrate',
            'Silver sulfate',
            'Sodium acetat trihydrate',
            'Sodium azide',
            'Sodium borohyride',
            'Sodium Carbonate',
            'Sodium chloride',
            'Sodium dihydrogen phospate dihydrate',

            'Sodium fluoride',
            'Sodium hydrogen Carbonate',
            'Sodium hydrogen sulfite',
            'Sodium hydroxide',
            'Sodium nitrite',
            'Sodium nitroprusside dihydrate',
            'Sodium sulfate',
            'Sodium sulfite',
            'Sodium tetraborate',
            'Sodium tetraphenyl borate',
            'Sodium thiosulfate',
            'Spirtus',
            'Starch',
            'Stearic acid',
            'Sterile Petri Dish Labware Charuzu 90 x 15 mm',
            'Strontium nitrate',
            'Sulfanilamide',
            'Sulfanilic acid',
            'Sulphuric acid',
            'Susu bear brand',
            'Thiocetamide',

            'Tin (II) chloride',
            'Tisu paseo',
            'Titriplex',
            'Titriplex I',
            'Titriplex II',
            'Titriplex III',
            'Tri-natriumphosphat-dodecahhydrate',
            'Trisodium citrate dihydrate',
            'Tri-Sodium Phosphate dodecahydrat',
            'Tube 16 x 100 srk medium',
            'Wolframatophosphosaure hydrate',
            'Xylene',
            'Zirkon (IV) Oxidchlorid-Octahydrat',
            'Zinc acetate dihydrate',
            'Zinc sulfate',
            'Zinc sulfate heptahydrate',
        ];
    }
}

