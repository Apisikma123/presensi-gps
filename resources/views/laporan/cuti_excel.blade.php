<table>
    <thead>
        <tr>
            <th colspan="19" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 15px; color: #1E4D3E; text-align: center;">
                {{ strtoupper($generalsetting->nama_perusahaan ?? 'PERUSAHAAN') }}
            </th>
        </tr>
        <tr>
            <th colspan="19" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 12px; color: #0F172A; text-align: center;">
                REKAPITULASI CUTI TAHUNAN KARYAWAN - TAHUN {{ $tahun }}
            </th>
        </tr>
        <tr>
            <th colspan="19" style="font-family: Arial, sans-serif; font-size: 10px; color: #64748B; text-align: center;">
                Alamat: {{ $generalsetting->alamat ?? '-' }} | Telp: {{ $generalsetting->telepon ?? '-' }} | Dicetak: {{ date('d/m/Y H:i') }} WIB
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Cabang / Outlet:</th>
            <th colspan="3" style="border: 1px solid #CBD5E1; text-align: left;">{{ $namacabang }}</th>
            <th colspan="2" style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Departemen:</th>
            <th colspan="4" style="border: 1px solid #CBD5E1; text-align: left;">{{ $namadept }}</th>
            <th colspan="2" style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Jenis Cuti:</th>
            <th colspan="6" style="border: 1px solid #CBD5E1; text-align: left;">{{ $jenis_cuti }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">No</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">NIK</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Nama Karyawan</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Outlet</th>
            <th colspan="12" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Distribusi Bulan (Tahun {{ $tahun }})</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #163B2F; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Total Ambil</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Jatah Cuti</th>
            <th rowspan="2" style="font-family: Arial, sans-serif; font-weight: bold; background-color: #163B2F; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Sisa Cuti</th>
        </tr>
        <tr>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Jan</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Feb</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Mar</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Apr</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Mei</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Jun</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Jul</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Agu</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Sep</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Okt</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Nov</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center;">Des</th>
        </tr>
    </thead>
    <tbody>
        @php
            $monthTotals = array_fill(1, 12, 0);
            $grandTotalAmbil = 0;
        @endphp

        @foreach ($rekap_cuti as $nik => $d)
            @php
                $jatah = $master_cuti->jumlah_hari ?? 12;
                $sisa = max(0, $jatah - $d['total_ambil']);
                $grandTotalAmbil += $d['total_ambil'];
                $bg = ($loop->iteration % 2 == 0) ? '#F8FAF8' : '#FFFFFF';
            @endphp
            <tr style="background-color: {{ $bg }};">
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">'{{ $d['nik_show'] ?? $nik }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle; font-weight: bold;">{{ $d['nama'] }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle;">{{ $d['cabang'] ?? '-' }}</td>
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $val = $d['bulan'][$i] ?? 0;
                        $monthTotals[$i] += $val;
                    @endphp
                    <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; @if($val > 0) background-color: #CCFBF1; font-weight: bold; color: #0F766E; @endif">
                        {{ $val > 0 ? $val : '' }}
                    </td>
                @endfor
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; font-weight: bold; color: #0F766E; background-color: #F0FDFA;">{{ $d['total_ambil'] }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $jatah }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; font-weight: bold; @if($sisa <= 2) color: #D97706; @else color: #059669; @endif">{{ $sisa }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: right; vertical-align: middle;">TOTAL BULANAN:</td>
            @for ($i = 1; $i <= 12; $i++)
                <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle;">
                    {{ $monthTotals[$i] > 0 ? $monthTotals[$i] : '-' }}
                </td>
            @endfor
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #E2E8F0; text-align: center; vertical-align: middle; color: #0F766E;">{{ $grandTotalAmbil }}</td>
            <td colspan="2" style="border: 1px solid #94A3B8; font-size: 10px; background-color: #F1F5F9; text-align: center; vertical-align: middle; color: #64748B;">Hari Kerja</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold;">Dibuat Oleh:</td>
            <td colspan="7" style="text-align: center; font-weight: bold;">Diperiksa Oleh:</td>
            <td colspan="8" style="text-align: center; font-weight: bold;">Disetujui Oleh:</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; height: 50px;"></td>
            <td colspan="7" style="text-align: center; height: 50px;"></td>
            <td colspan="8" style="text-align: center; height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'HR Officer' }}</td>
            <td colspan="7" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</td>
            <td colspan="8" style="text-align: center; font-weight: bold; text-decoration: underline;">Direktur Operasional</td>
        </tr>
    </tfoot>
</table>
