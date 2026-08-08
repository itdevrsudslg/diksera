<div class="content-card h-100"> 

    {{-- Header dengan Style Section Title --}}
    <div class="section-title">
        @if (isset($icon))
            <i class="bi {{ $icon }}"></i>
        @endif
        {{ $title }}
    </div>

    <div class="table-responsive">
        {{-- Penambahan text-nowrap agar tabel bisa di scroll horizontal di HP --}}
        <table class="table table-custom align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>

                    {{-- Loop Header --}}
                    @foreach ($cols as $label => $key)
                        <th>
                            {{ is_string($label) ? $label : ucwords(str_replace('_', ' ', $key)) }}
                        </th>
                    @endforeach

                    <th style="width: 100px;">Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>

                        {{-- Loop Value --}}
                        @foreach ($cols as $key)
                            <td>
                                {{ $row->$key ?? '-' }}
                            </td>
                        @endforeach

                        {{-- Kolom Dokumen --}}
                        <td>
                            @if ($row->dokumen_path)
                                <a href="{{ asset('storage/' . $row->dokumen_path) }}" target="_blank"
                                    class="text-decoration-none d-flex align-items-center gap-1 small"
                                    style="color: var(--blue-main);">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                                    <span>Lihat</span>
                                </a>
                            @else
                                <span class="text-muted opacity-50 small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($cols) + 2 }}" class="text-center py-4">
                            <div class="text-muted small fst-italic">
                                Belum ada data {{ strtolower($title) }}.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>