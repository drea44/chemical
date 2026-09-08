<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemical Label - <?php echo e($chemical->chemical_code); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        body {
            background: #f1f5f9;
            padding: 24px;
            color: #0f172a;
        }
        .no-print-bar {
            max-width: 600px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #ffffff;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-secondary:hover {
            background: #f8fafc;
        }

        /* Label Container: Standard Lab Bottle / Container sticker */
        .label-container {
            max-width: 540px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .chemical-title {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
            color: #0f172a;
        }
        .chemical-code {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
            margin-top: 2px;
        }
        .hazard-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 4px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #f87171;
            white-space: nowrap;
        }

        .label-body {
            display: grid;
            grid-template-columns: 1fr 130px;
            gap: 16px;
            align-items: center;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 12px;
            font-size: 12px;
        }
        .info-item {
            line-height: 1.3;
        }
        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-val {
            font-weight: 600;
            color: #1e293b;
        }

        .qr-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .qr-box img {
            width: 110px;
            height: 110px;
            display: block;
        }
        .qr-text {
            font-family: monospace;
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            margin-top: 4px;
        }

        .label-footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none;
            }
            .label-container {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <a href="<?php echo e(route('chemicals.show', $chemical)); ?>" class="btn btn-secondary">
        &larr; Back to Details
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        Print Label
    </button>
</div>

<div class="label-container">
    <div class="label-header">
        <div>
            <h1 class="chemical-title"><?php echo e($chemical->chemical_name); ?></h1>
            <div class="chemical-code"><?php echo e($chemical->chemical_code); ?></div>
        </div>
        <?php if($chemical->hazard_class): ?>
        <div class="hazard-badge">
            <?php echo e($chemical->hazard_class); ?>

        </div>
        <?php endif; ?>
    </div>

    <div class="label-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">CAS Number</div>
                <div class="info-val" style="font-family: monospace;"><?php echo e($chemical->cas_number ?: 'N/A'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Category</div>
                <div class="info-val"><?php echo e($chemical->category?->name ?: 'Standard'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Batch / Lot No.</div>
                <div class="info-val"><?php echo e($chemical->batch_number ?: $chemical->lot_number ?: 'N/A'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Concentration</div>
                <div class="info-val"><?php echo e($chemical->concentration ?: 'Pure / 100%'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-val"><?php echo e($chemical->location?->full_address ?: 'Main Storage'); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Expiry Date</div>
                <div class="info-val" style="<?php echo e($chemical->isExpired() ? 'color: #dc2626;' : ''); ?>">
                    <?php echo e($chemical->expiry_date?->format('d M Y') ?: 'Permanent'); ?>

                </div>
            </div>
        </div>

        <div class="qr-box">
            <?php if($chemical->qr_code && file_exists(public_path(ltrim($chemical->qr_code, '/')))): ?>
                <img src="<?php echo e($chemical->qr_code); ?>" alt="QR Code">
            <?php else: ?>
                <div style="width:110px;height:110px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#94a3b8;border:1px dashed #cbd5e1;">
                    QR Code
                </div>
            <?php endif; ?>
            <div class="qr-text"><?php echo e($chemical->chemical_code); ?></div>
        </div>
    </div>

    <div class="label-footer">
        <span>Chemical Stock OS Enterprise</span>
        <span>Storage: <?php echo e($chemical->storage_condition ?: 'Room Temp'); ?></span>
        <span>Printed: <?php echo e(now()->format('d M Y H:i')); ?></span>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/chemicals/label.blade.php ENDPATH**/ ?>