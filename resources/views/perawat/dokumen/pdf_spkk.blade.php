<!DOCTYPE html>
<html>
<head>
    <title>SPKK Kolektif</title>
  <style>
    body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; margin: 0 1cm; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .anti-potong { page-break-inside: avoid; }
    /* Perbaikan Garis Kop Surat */
    .kop-surat { width: 100%; border-collapse: collapse; border-bottom: 3px solid black; margin-bottom: 2px; }
    .kop-surat td { padding-bottom: 10px; vertical-align: middle; }
    .kop-surat-2 { border-bottom: 1px solid black; margin-bottom: 15px; }
    
    .page-break { page-break-after: always; }
    .ttd-box { width: 300px; float: right; text-align: center; margin-top: 20px; }
</style>
</head>
<body>
<!-- KOP SURAT -->
<table class="kop-surat">
    <tr>
        <td width="15%" class="text-center">
            <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Pemkab.png" width="90">
        </td>
        <td width="85%" class="text-center" style="line-height: 1.1;">
            <div style="font-size: 14pt; margin-bottom: 2px;">PEMERINTAH KABUPATEN KEDIRI</div>
            <div class="bold" style="font-size: 16pt; margin-bottom: 2px;">DINAS KESEHATAN</div>
            <div class="bold" style="font-size: 18pt; margin-bottom: 5px;">RSUD SIMPANG LIMA GUMUL</div>
            <div style="font-size: 10pt; line-height: 1.2;">
                Jalan Galuh Candra Kirana Nomor 8, Kecamatan Ngasem, Kabupaten Kediri 64182<br>
                Telepon (0354) 2891400 Fax. (0354) 2891414<br>
                Pos-el : rsudslg@kedirikab.go.id Laman : rsudslg.kedirikab.go.id
            </div>
        </td>
    </tr>
</table>
<div class="kop-surat-2"></div>

    <!-- JUDUL SURAT -->
    <div class="text-center bold">
        <p>KEPUTUSAN<br>DIREKTUR UOBK RSUD SIMPANG LIMA GUMUL KEDIRI<br>
        <span style="text-decoration: underline;">NOMOR : {{ $no_spkk }}</span></p>

        <p>TENTANG<br>
        PENUGASAN KLINIS (CLINICAL APPOINTMENT) STAF KEPERAWATAN / KEBIDANAN {{ $nama_kualifikasi }}<br>
        DI UOBK RSUD SIMPANG LIMA GUMUL KEDIRI</p>

        <p>DIREKTUR UOBK RSUD SIMPANG LIMA GUMUL KEDIRI,</p>
    </div>

    <!-- MENIMBANG & MENGINGAT -->
    <table style="margin-top: 10px;">
        <tr>
            <td width="15%" valign="top">Menimbang</td>
            <td width="3%" valign="top">:</td>
            <td width="4%" valign="top">a.</td>
            <td width="78%" align="justify">bahwa dalam rangka mendukung terwujudnya pelayanan keperawatan di UOBK RSUD Simpang Lima Gumul Kediri yang lebih optimal dan penataan manajemen pelayanan di ruangan yang lebih baik, maka dipandang perlu ditetapkan Penugasan Klinis Staf Keperawatan / Kebidanan di UOBK RSUD SLG Kediri;</td>
        </tr>
        <tr>
            <td></td><td></td>
            <td valign="top">b.</td>
            <td align="justify">bahwa untuk merealisasi sebagaimana dimaksud huruf a diatas perlu ditetapkan dengan Keputusan Direktur UOBK RSUD SLG Kediri;</td>
        </tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
            <td valign="top">Mengingat</td>
            <td valign="top">:</td>
            <td valign="top">a.</td>
            <td align="justify">Undang-undang Nomor 25 Tahun 2009 tentang Pelayanan Publik;</td>
        </tr>
        <tr><td></td><td></td><td valign="top">b.</td><td align="justify">Undang-undang Nomor 36 Tahun 2009 tentang Kesehatan;</td></tr>
        <tr><td></td><td></td><td valign="top">c.</td><td align="justify">Undang-Undang Nomor 44 Tahun 2009 Tentang Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">d.</td><td align="justify">Undang-Undang Nomor 36 Tahun 2014 Tentang Tenaga Kesehatan;</td></tr>
        <tr><td></td><td></td><td valign="top">e.</td><td align="justify">Undang-Undang Nomor 38 Tahun 2014 Tentang Keperawatan;</td></tr>
        <tr><td></td><td></td><td valign="top">f.</td><td align="justify">Peraturan Presiden Nomor 77 Tahun 2015 Tentang Pedoman Organisasi Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">g.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor 46 Tahun 2013 Tentang Registrasi Tenaga Kesehatan;</td></tr>
        <tr><td></td><td></td><td valign="top">h.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor : 49 Tahun 2013 tentang Komite Keperawatan Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">i.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor 56 Tahun 2014 tentang Klasifikasi dan Perijinan Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">j.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor 34 Tahun 2017 tentang Akreditasi Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">k.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor 40 Tahun 2017 tentang Pengembangan Jenjang Karir Profesional Perawat Klinis;</td></tr>
    </table>

    <div class="page-break"></div>

    <table>
        <tr><td width="15%"></td><td width="3%"></td><td width="4%" valign="top">l.</td><td width="78%" align="justify">Peraturan Menteri Kesehatan RI Nomor 4 Tahun 2019 tentang Standar Teknis Pemenuhan Mutu Pelayanan Dasar Pada Standar Pelayanan Minimal Bidang Kesehatan;</td></tr>
        <tr><td></td><td></td><td valign="top">m.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor HK.02.02/MENKES/148/I/2010 Tentang Izin Dan Penyelenggaraan Praktik Perawat;</td></tr>
        <tr><td></td><td></td><td valign="top">n.</td><td align="justify">Peraturan Menteri Kesehatan RI Nomor HK.02.02/MENKES/149/I/2010 Tentang Izin Dan Penyelenggaraan Praktik Bidan;</td></tr>
        <tr><td></td><td></td><td valign="top">o.</td><td align="justify">Keputusan Menteri Kesehatan RI No. 1195/MENKES/SK/VII/2010 Tentang Lembaga/Badan Kreditasi Rumah Sakit Bertaraf Internasional;</td></tr>
        <tr><td></td><td></td><td valign="top">p.</td><td align="justify">Keputusan Menteri Kesehatan RI Nomor : 722/Menkes/SK/VI/2002 tentang Pedoman Peraturan Internal Rumah Sakit (Hospital Bylaws);</td></tr>
        <tr><td></td><td></td><td valign="top">q.</td><td align="justify">Keputusan Menteri Kesehatan RI No. 129 Tahun 2008 Tentang Standar Pelayanan Minimal Rumah Sakit;</td></tr>
        <tr><td></td><td></td><td valign="top">r.</td><td align="justify">Standar Asuhan Keperawatan, Departemen Kesehatan Republik Indonesia tahun 1997;</td></tr>
        <tr><td></td><td></td><td valign="top">s.</td><td align="justify">Peraturan Bupati Kediri Nomor 45 Tahun 2016 tentang Kedudukan, Susunan Organisasi, Uraian Tugas dan Fungsi serta Tata Kerja Dinas Kesehatan Kabupaten Kediri;</td></tr>
        <tr><td></td><td></td><td valign="top">t.</td><td align="justify">Peraturan Bupati Kediri Nomor 36 Tahun 2017 tentang Pembentukan Unit Pelaksana Teknis Daerah Rumah Sakit Umum Daerah Simpang Lima Gumul pada Dinas Kesehatan Kabupaten Kediri;</td></tr>
        <tr><td></td><td></td><td valign="top">u.</td><td align="justify">Keputusan Direktur UPTD RSUD Simpang Lima Gumul Kediri No. 188/3000/418.25.5/2019 Tentang Pengesahan Dan Pemberlakuan Nursing Staff By Law;</td></tr>
    </table>

    <div class="text-center bold" style="margin: 20px 0;">MEMUTUSKAN</div>

    <table style="width: 100%;">
        <tr><td width="15%" valign="top">Menetapkan</td><td width="3%" valign="top">:</td><td align="justify"></td></tr>
        <tr>
            <td valign="top">KESATU</td>
            <td valign="top">:</td>
            <td align="justify" class="bold">KEPUTUSAN DIREKTUR UOBK RSUD SIMPANG LIMA GUMUL TENTANG PENUGASAN KLINIS (CLINICAL APPOINTMENT) STAF KEPERAWATAN / KEBIDANAN {{ $nama_kualifikasi }} DI UOBK RSUD SIMPANG LIMA GUMUL KEDIRI.</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td valign="top">KEDUA</td><td valign="top">:</td>
            <td align="justify">Penugasan Staf Klinis Keperawatan / Kebidanan dimaksud dalam diktum pertama sebagaimana tercantum dalam Surat Penugasan Kerja Klinis, dan sesuai dengan kewenangan klinis yang dimilikinya.</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td valign="top">KETIGA</td><td valign="top">:</td>
            <td align="justify">Rincian Kewenangan Klinis dari Staf Keperawatan / Kebidanan tersebut sesuai dengan Surat Penugasan Staf Klinis Keperawatan / Kebidanan.</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td valign="top">KEEMPAT</td><td valign="top">:</td>
            <td align="justify">Penugasan Klinis Staf Keperawatan / Kebidanan berlaku untuk jangka waktu 3 tahun, dan tidak akan melebihi masa berlaku STR yang bersangkutan.</td>
        </tr>
    </table> <!-- TUTUP TABEL DISINI -->

    <!-- BUKA TABEL BARU DAN BUNGKUS DENGAN ANTI-POTONG BERSAMA TTD -->
    <div class="anti-potong">
        <table style="width: 100%;">
            <tr><td colspan="3">&nbsp;</td></tr>
            <tr>
                <td width="15%" valign="top">KELIMA</td><td width="3%" valign="top">:</td>
                <td align="justify">Keputusan ini berlaku sejak tanggal ditetapkan, bila kemudan hari diketemukan kekeliran akan diadakan perbaikan sebagaimana mestinya.</td>
            </tr>
        </table>

        <div class="ttd-box">
            <p>Ditetapkan di : Kediri<br>
            <span style="text-decoration: underline;">Pada Tanggal : {{ $tgl_terbit }}</span></p>
            
            <p>DIREKTUR<br>UOBK RSUD SIMPANG LIMA GUMUL<br>KEDIRI</p>
            <img src="{{ $qr_url }}" alt="QR Code" width="90" style="margin: 10px 0;">
            <p class="bold" style="text-decoration: underline; margin-bottom: 0;">{{ $direktur_nama }}</p>
            <p style="margin-top: 0;">NIP. {{ $direktur_nip }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>