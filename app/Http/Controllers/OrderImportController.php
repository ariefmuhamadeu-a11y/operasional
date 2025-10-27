<?php

namespace App\Http\Controllers;

use App\Models\OrderShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class OrderImportController extends Controller
{
    // ====== INDEX: list + cari + filter tanggal ======
    public function index(Request $req)
    {
        $q        = trim((string) $req->query('q', ''));
        $dateFrom = $req->query('date_from');
        $dateTo   = $req->query('date_to');

        // default filter pakai kolom waktu_pesanan_dibuat
        $dateCol  = 'waktu_pesanan_dibuat';

        $shipments = OrderShipment::query()
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($qq) use ($q) {
                    $qq->where('no_pesanan', 'like', "%$q%")
                       ->orWhere('no_resi', 'like', "%$q%");
                });
            })
            ->when($dateFrom || $dateTo, function ($qr) use ($dateFrom, $dateTo, $dateCol) {
                if ($dateFrom && $dateTo) {
                    $start = Carbon::parse($dateFrom . ' 00:00:00', 'Asia/Jakarta')->timezone('UTC');
                    $end   = Carbon::parse($dateTo   . ' 23:59:59', 'Asia/Jakarta')->timezone('UTC');
                    $qr->whereBetween($dateCol, [$start, $end]);
                } elseif ($dateFrom) {
                    $start = Carbon::parse($dateFrom . ' 00:00:00', 'Asia/Jakarta')->timezone('UTC');
                    $qr->where($dateCol, '>=', $start);
                } elseif ($dateTo) {
                    $end   = Carbon::parse($dateTo   . ' 23:59:59', 'Asia/Jakarta')->timezone('UTC');
                    $qr->where($dateCol, '<=', $end);
                }
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('imports.orders.index', compact('shipments','q','dateFrom','dateTo','dateCol'));
    }

    // ====== PREVIEW: baca file & tampilkan editable table ======
    public function preview(Request $req)
    {
        $req->validate([
            'file' => ['required','file','mimes:xlsx,xls'],
        ]);

        $rows = $this->readFromUpload($req->file('file')); // <= UploadedFile, bukan getRealPath
        $normalized = $this->normalize($rows); // jangan format tanggal di sini

        // tandai duplikat di FILE (gabungan 4 kolom kunci)
        $inFileDupes = [];
        $seen = [];
        foreach ($normalized as $i => $r) {
            $key = $this->key4($r);
            if (isset($seen[$key])) $inFileDupes[$i] = true;
            else $seen[$key] = true;
        }

        // tandai yang SUDAH ADA di DB
        $exists = OrderShipment::query()
            ->where(function ($q) use ($normalized) {
                $normalized->each(function ($r) use ($q) {
                    $q->orWhere(function ($qq) use ($r) {
                        $qq->where('no_pesanan',            $r['no_pesanan'] ?? '')
                           ->where('no_resi',               $r['no_resi'] ?? '')
                           ->where('sku_induk',             $r['sku_induk'] ?? '')
                           ->where('nomor_referensi_sku',   $r['nomor_referensi_sku'] ?? '');
                    });
                });
            })
            ->get()
            ->map(fn($s) => $s->no_pesanan.'|'.$s->no_resi.'|'.$s->sku_induk.'|'.$s->nomor_referensi_sku)
            ->flip();

        return view('imports.orders.preview', [
            'rows'        => $normalized,
            'inFileDupes' => $inFileDupes,
            'existingKeys'=> $exists,
        ]);
    }

    // ====== IMPORT: simpan baris terpilih (pasca edit) ======
    public function import(Request $req)
    {
        $input = collect($req->input('rows', []))
            ->filter(fn($r) => !empty($r['no_pesanan'])) // buang baris kosong
            ->map(fn($r) => $this->fillDefaults($r))     // kunci kosong jadi ''
            ->values();

        // deteksi duplikat di FORM setelah diedit
        $seen = [];
        foreach ($input as $i => $r) {
            $key = $this->key4($r);
            if (isset($seen[$key])) {
                return back()->with('error','Ada duplikat pada data yang diedit (No. Pesanan + No. Resi + SKU Induk + Ref SKU).');
            }
            $seen[$key] = true;
        }

        $inserted = 0; $skipped = 0;
        DB::beginTransaction();
        try {
            foreach ($input as $r) {
                $exists = OrderShipment::where('no_pesanan', $r['no_pesanan'])
                    ->where('no_resi', $r['no_resi'])
                    ->where('sku_induk', $r['sku_induk'])
                    ->where('nomor_referensi_sku', $r['nomor_referensi_sku'])
                    ->exists();

                if ($exists) { $skipped++; continue; }
                OrderShipment::create([
                    'no_pesanan'                 => $r['no_pesanan'],
                    'no_resi'                    => $r['no_resi'],
                    'sku_induk'                  => $r['sku_induk'],
                    'nomor_referensi_sku'        => $r['nomor_referensi_sku'],

                    'status_pesanan'             => $r['status_pesanan'] ?? null,
                    'shipped_by_advance_fulfilment' => $r['shipped_by_advance_fulfilment'] ?? null,
                    'status_pembatalan_pengembalian'  => $r['status_pembatalan_pengembalian'] ?? null,
                    'metode_pembayaran'          => $r['metode_pembayaran'] ?? null,

                    // TANGGAL — konversi di sini (bukan saat preview)
                    'waktu_pesanan_dibuat'       => $this->toDateTime($r['waktu_pesanan_dibuat'] ?? null),
                    'waktu_pengiriman_diatur'    => $this->toDateTime($r['waktu_pengiriman_diatur'] ?? null),
                    'waktu_pesanan_selesai'      => $this->toDateTime($r['waktu_pesanan_selesai'] ?? null),

                    // Angka
                    'jumlah'                     => $this->toInt($r['jumlah'] ?? null),
                    'harga_setelah_diskon' => (int) $this->toDecimal($r['harga_setelah_diskon'] ?? null),

                    // Lain-lain
                    'nama_penerima'              => $r['nama_penerima'] ?? null,
                    'no_telepon'                 => $r['no_telepon'] ?? null,
                    'kota_kabupaten'             => $r['kota_kabupaten'] ?? null,
                    'provinsi'                   => $r['provinsi'] ?? null,
                    'metode_pembayaran'    => $r['metode_pembayaran'] ?? null,
                ]);
                $inserted++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Gagal import: '.$e->getMessage());
        }

        return redirect()->route('imports.orders.index')
            ->with('status',"Selesai. Insert: $inserted, Skip (duplikat): $skipped");
    }

    // ====== EDIT / UPDATE BARIS ======
    public function edit(OrderShipment $shipment)
    {
        return view('imports.orders.edit', compact('shipment'));
    }

    public function update(Request $req, OrderShipment $shipment)
    {
        $data = $req->validate([
            'no_pesanan' => 'required|string',
            'no_resi'    => 'nullable|string',
            'sku_induk'  => 'nullable|string',
            'nomor_referensi_sku' => 'nullable|string',

            'status_pesanan' => 'nullable|string',
            'shipped_by_advance_fulfilment' => 'nullable|string',
            'status_pembatalan_pengembalian' => 'nullable|string',
            'metode_pembayaran' => 'nullable|string',

            'waktu_pesanan_dibuat'    => 'nullable|string',
            'waktu_pengiriman_diatur' => 'nullable|string',
            'waktu_pesanan_selesai'   => 'nullable|string',

            'jumlah' => 'nullable|numeric',
            'harga_setelah_diskon' => 'nullable|numeric',

            'nama_penerima' => 'nullable|string',
            'no_telepon'    => 'nullable|string',
            'kota_kabupaten'=> 'nullable|string',
            'provinsi'      => 'nullable|string',
            // validate
            'metode_pembayaran' => 'nullable|string',


            // sebelum $shipment->update($data);

        ]);

        // kunci kosong jadi ''
        $data = $this->fillDefaults($data);

        // cegah duplikat terhadap record lain
        $exists = OrderShipment::where('no_pesanan', $data['no_pesanan'])
            ->where('no_resi', $data['no_resi'])
            ->where('sku_induk', $data['sku_induk'])
            ->where('nomor_referensi_sku', $data['nomor_referensi_sku'])
            ->where('id', '!=', $shipment->id)
            ->exists();
        if ($exists) {
            return back()->with('error','Perubahan menyebabkan duplikat (No. Pesanan + No. Resi + SKU Induk + Ref SKU).');
        }

        // tanggal -> Carbon
        $data['waktu_pesanan_dibuat']    = $this->toDateTime($data['waktu_pesanan_dibuat'] ?? null);
        $data['waktu_pengiriman_diatur'] = $this->toDateTime($data['waktu_pengiriman_diatur'] ?? null);
        $data['waktu_pesanan_selesai']   = $this->toDateTime($data['waktu_pesanan_selesai'] ?? null);

        $shipment->update($data);

        return redirect()->route('imports.orders.index')->with('status','Data diperbarui.');
    }

    // ====== Helpers ======
    private function readFromUpload(UploadedFile $file): Collection
    {
        $array = Excel::toArray([], $file)[0] ?? [];
        return collect($array);
    }

    private function normalize(Collection $rows): Collection
    {
        if ($rows->isEmpty()) return collect();

        $rawHeaders = collect($rows->shift())->values();
        $headers = $rawHeaders->map(fn($h,$i) => $this->cleanHeader($h,$i))->toArray();

        // Aliases header -> field internal
        $aliases = [
            'no pesanan' => 'no_pesanan', 'no order' => 'no_pesanan', 'order id'=>'no_pesanan','order number'=>'no_pesanan','nomor pesanan'=>'no_pesanan',
            'no resi' => 'no_resi', 'nomor resi'=>'no_resi', 'awb'=>'no_resi',
            'sku induk' => 'sku_induk', 'parent sku'=>'sku_induk', 'sku utama'=>'sku_induk',
            'nomor referensi sku' => 'nomor_referensi_sku', 'ref sku'=>'nomor_referensi_sku', 'sku'=>'nomor_referensi_sku',
            'status pesanan' => 'status_pesanan',
            'status pembatalan pengembalian' => 'status_pembatalan_pengembalian', 'status pembatalan / pengembalian'=>'status_pembatalan_pengembalian', 'status pembatalan/ pengembalian'=>'status_pembatalan_pengembalian',
            'waktu pengiriman diatur'=>'waktu_pengiriman_diatur',
            'waktu pesanan dibuat'=>'waktu_pesanan_dibuat',
            'waktu pesanan selesai'=>'waktu_pesanan_selesai',
            'metode pembayaran'=>'metode_pembayaran',
            'jumlah'=>'jumlah','qty'=>'jumlah',
            'harga setelah diskon'=>'harga_setelah_diskon','harga diskon'=>'harga_setelah_diskon',
            'nama penerima'=>'nama_penerima',
            'no telepon'=>'no_telepon',
            'kota/kabupaten'=>'kota_kabupaten', 'kota / kabupaten'=>'kota_kabupaten', 'kota kabupaten'=>'kota_kabupaten','kabupaten/kota'=>'kota_kabupaten','kabupaten / kota'=>'kota_kabupaten','kota'=>'kota_kabupaten',
            'provinsi'=>'provinsi',
            'metode pembayaran' => 'metode_pembayaran',
            // di dalam $aliases
            'shipped by advance fulfilment'  => 'shipped_by_advance_fulfilment', // UK (fulfilment)
            'shipped by advance fulfillment' => 'shipped_by_advance_fulfilment', // US (fulfillment)



        ];

        // Peta posisi kolom -> field
        $indexToField = [];
        foreach ($headers as $i => $h) {
            $indexToField[$i] = $aliases[$h] ?? null;
        }

        return $rows->map(function ($row) use ($indexToField) {
            $assoc = [];
            foreach ($indexToField as $i => $field) {
                if (!$field) continue;
                $assoc[$field] = $row[$i] ?? null; // JANGAN format tanggal di sini
            }
            // cast angka ringan
            $assoc['jumlah']               = $this->toInt($assoc['jumlah'] ?? null);
            $assoc['harga_setelah_diskon'] = $this->toDecimal($assoc['harga_setelah_diskon'] ?? null);

            // kunci default '' agar konsisten dengan unik gabungan
            return $this->fillDefaults($assoc);
        })->filter(fn($x) => array_filter($x))->values();
    }

    private function cleanHeader($h, $index = null): string
    {
        $s = (string)$h;
        $s = str_replace(["\xEF\xBB\xBF", "\xC2\xA0"], '', $s); // BOM & NBSP
        $s = trim(mb_strtolower($s));
        $s = str_replace(['.', ','], '', $s);
        $s = str_replace(['/', '\\'], ' / ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        if ($s === '') $s = 'col_'.$index;
        return $s;
    }

    private function key4(array $r): string
    {
        return ($r['no_pesanan'] ?? '')
            .'|'.(($r['no_resi'] ?? '') ?: '')
            .'|'.(($r['sku_induk'] ?? '') ?: '')
            .'|'.(($r['nomor_referensi_sku'] ?? '') ?: '');
    }

    private function fillDefaults(array $r): array
    {
        $r['no_resi']             = trim((string)($r['no_resi'] ?? ''));
        $r['sku_induk']           = trim((string)($r['sku_induk'] ?? ''));
        $r['nomor_referensi_sku'] = trim((string)($r['nomor_referensi_sku'] ?? ''));
        return $r;
    }

    private function toInt($v): ?int
    {
        if ($v === '' || $v === null) return null;
        if (is_numeric($v)) return (int)$v;
        return (int) preg_replace('/[^\d\-]/', '', (string)$v);
    }

   private function toDecimal($v): ?float
    {
        if ($v === '' || $v === null) return null;

        // Ubah ke string dan bersihkan spasi/Rp
        $v = trim((string)$v);
        $v = str_ireplace(['Rp', ' ', 'IDR'], '', $v);

        // Kalau format Indonesia (55.600 atau 55.600,50)
        // maka hilangkan titik ribuan, ubah koma jadi titik
        if (preg_match('/^\d{1,3}(\.\d{3})*(,\d+)?$/', $v)) {
            $v = str_replace('.', '', $v);
            $v = str_replace(',', '.', $v);
        }

        // Kalau format English (55,600.00)
        elseif (preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $v)) {
            $v = str_replace(',', '', $v);
        }

        // Setelah dibersihkan, pastikan numeric
        return is_numeric($v) ? (float)$v : null;
    }


    private function toDateTime($v): ?Carbon
    {
        if ($v === null || $v === '') return null;

        if ($v instanceof Carbon || $v instanceof \DateTimeInterface) {
            return Carbon::parse((string)$v, 'Asia/Jakarta')->timezone('Asia/Jakarta');
        }

        if (is_numeric($v)) { // Excel serial
            $ts = (int)(($v - 25569) * 86400);
            return Carbon::createFromTimestamp($ts, 'Asia/Jakarta');
        }

        $s = trim((string)$v);
        $formats = [
            'Y-m-d H:i:s','Y-m-d H:i',
            'd/m/Y H:i:s','d/m/Y H:i',
            'd-m-Y H:i:s','d-m-Y H:i',
            'm/d/Y H:i:s','m/d/Y H:i',
            'Y-m-d','d/m/Y','d-m-Y','m/d/Y',
        ];
        foreach ($formats as $fmt) {
            try { return Carbon::createFromFormat($fmt, $s, 'Asia/Jakarta'); } catch (\Throwable) {}
        }
        try { return Carbon::parse($s, 'Asia/Jakarta'); } catch (\Throwable) { return null; }
    }



}
