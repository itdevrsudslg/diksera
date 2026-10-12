{{-- Pilihan periode rekap: jenis (bulan/triwulan/semester), ke, tahun. $extra = input hidden tambahan --}}
            <form method="GET" class="d-flex flex-wrap gap-2 m-0" id="formPeriode">
                @foreach ($extra ?? [] as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                <select name="jenis" class="form-select form-select-sm" style="width:auto" onchange="this.form.ke.value = 1; this.form.submit()">
                    @foreach (['bulan' => 'Bulanan', 'triwulan' => 'Triwulan', 'semester' => 'Semester'] as $v => $l)
                        <option value="{{ $v }}" {{ $jenis === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <select name="ke" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @if ($jenis === 'bulan')
                        @foreach (range(1, 12) as $b)
                            <option value="{{ $b }}" {{ $ke == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('F') }}</option>
                        @endforeach
                    @elseif ($jenis === 'triwulan')
                        @foreach ([1 => 'I (Jan–Mar)', 2 => 'II (Apr–Jun)', 3 => 'III (Jul–Sep)', 4 => 'IV (Okt–Des)'] as $v => $l)
                            <option value="{{ $v }}" {{ $ke == $v ? 'selected' : '' }}>Triwulan {{ $l }}</option>
                        @endforeach
                    @else
                        @foreach ([1 => 'I (Jan–Jun)', 2 => 'II (Jul–Des)'] as $v => $l)
                            <option value="{{ $v }}" {{ $ke == $v ? 'selected' : '' }}>Semester {{ $l }}</option>
                        @endforeach
                    @endif
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @foreach (range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
