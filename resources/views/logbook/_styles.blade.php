<style>
    .lb-card { background: #fff; border: 1px solid var(--border-soft); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.02); padding: 20px; }
    .lb-toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .lb-stat { background: #fff; border: 1px solid var(--border-soft); border-radius: 14px; padding: 14px 16px; height: 100%; }
    .lb-stat .v { font-size: 22px; font-weight: 700; line-height: 1.1; color: var(--text-main); }
    .lb-stat .l { font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--text-muted); }
    .lb-pill { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .lb-pill.valid, .lb-pill.success { background: #dcfce7; color: #166534; }
    .lb-pill.menunggu, .lb-pill.warning { background: #fef3c7; color: #92400e; }
    .lb-pill.ditolak, .lb-pill.danger { background: #fee2e2; color: #991b1b; }
    .lb-pill.info { background: #e0f2fe; color: #075985; }
    .lb-pill.muted { background: #f1f5f9; color: #475569; }
    .lb-day { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin: 18px 0 8px; }
    .lb-entry { border: 1px solid var(--border-soft); border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; background: #fff; }
    .lb-entry.ditolak { border-left: 4px solid #ef4444; }
    .lb-entry.valid { border-left: 4px solid #22c55e; }
    .lb-entry.menunggu { border-left: 4px solid #f59e0b; }
    .lb-rm { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; color: var(--blue-main); }
    .lb-keg { font-size: 13px; margin-top: 8px; }
    .lb-keg .k { font-weight: 600; color: var(--text-main); }
    .lb-tag { display: inline-block; font-size: 11.5px; background: #f1f5f9; color: #334155; border-radius: 6px; padding: 2px 8px; margin: 3px 4px 0 0; }
    .lb-tag b { color: var(--blue-main); }
    .lb-empty { text-align: center; padding: 40px 16px; color: var(--text-muted); }
    .lb-ttd { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .lb-ttd.dua { grid-template-columns: repeat(2, 1fr); }
    .lb-ttd .box { border: 1px dashed var(--border-soft); border-radius: 12px; padding: 12px; text-align: center; font-size: 12px; }
    .lb-ttd .box.done { border-style: solid; border-color: #86efac; background: #f0fdf4; }
    .lb-ttd .box .role { color: var(--text-muted); font-size: 11px; text-transform: uppercase; }
    .lb-ttd .box .nm { font-weight: 700; margin-top: 4px; }
    .lb-table { font-size: 13px; }
    .lb-table th { font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--text-muted); background: #f8fafc; white-space: nowrap; }
    @media (max-width: 576px) {
        .lb-ttd, .lb-ttd.dua { grid-template-columns: 1fr; }
        .lb-card { padding: 14px; }
    }
</style>
