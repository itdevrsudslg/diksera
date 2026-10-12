    <style>
        /* Lembar rekap format surat resmi */
        .surat { background: #fff; color: #000; font-family: 'Times New Roman', Times, serif; font-size: 11.5pt; line-height: 1.35; padding: 28px 32px; border: 1px solid var(--border-soft, #d0d7ee); }
        .surat table { border-collapse: collapse; }
        .surat .kop { width: 100%; border-bottom: 3px double #000; margin-bottom: 14px; }
        .surat .kop td { vertical-align: middle; padding-bottom: 8px; }
        .surat .kop-logo { width: 72px; }
        .surat .kop-teks { text-align: center; line-height: 1.15; }
        .surat .kop .k1 { font-size: 13pt; font-weight: bold; }
        .surat .kop .k2 { font-size: 14.5pt; font-weight: bold; }
        .surat .kop .k3 { font-size: 9.5pt; }
        .surat .kop .k4 { font-size: 11pt; font-weight: bold; letter-spacing: 3px; }
        .surat .judul { text-align: center; margin: 6px 0 14px; }
        .surat .judul .j1 { font-size: 13pt; font-weight: bold; text-decoration: underline; }
        .surat .ident td { padding: 1px 4px 1px 0; vertical-align: top; }
        .surat .sub { font-weight: bold; margin: 14px 0 5px; }
        .surat .tbl { width: 100%; }
        .surat .tbl th, .surat .tbl td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; font-size: 10.5pt; }
        .surat .tbl th { background: #eee; text-align: center; font-weight: bold; }
        .surat .tbl tfoot th { background: #f5f5f5; }
        .surat .c, .surat .c td { text-align: center; }
        .surat .kosong { font-style: italic; color: #444; padding: 10px; }
        .surat .tempat { text-align: right; margin: 22px 0 6px; }
        .surat .ttd { width: 100%; text-align: center; page-break-inside: avoid; }
        .surat .ttd td { vertical-align: top; padding: 0 6px; }
        .surat .ttd .role { min-height: 2.8em; }
        .surat .ttd .sig { height: 100px; display: flex; align-items: center; justify-content: center; }
        .surat .ttd .qr { width: 90px; height: 90px; }
        .surat .ttd .belum { font-size: 9.5pt; font-style: italic; color: #666; }
        .surat .ttd .nm { font-weight: bold; text-decoration: underline; }
        .surat .catatan { margin-top: 18px; font-size: 8.5pt; font-style: italic; color: #333; border-top: 1px solid #999; padding-top: 4px; }
        @media (max-width: 576px) {
            .surat { padding: 14px; font-size: 10.5pt; overflow-x: auto; }
            .surat .kop-logo { width: 48px; }
        }
        @media print {
            .surat { border: none; padding: 0; }
            .surat .tbl thead { display: table-header-group; }
            .surat .tbl tr { page-break-inside: avoid; }
        }
    </style>
