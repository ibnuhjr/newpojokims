<div class="row">
    <div class="col-md-6">
        <table class="table table-borderless table-sm">
            <tr><td><strong>Nomor Surat Jalan:</strong></td><td>{{ $suratJalan->nomor_surat ?? '-' }}</td></tr>
            <tr><td><strong>Tanggal Surat Jalan:</strong></td><td>{{ optional($suratJalan->tanggal)->format('d/m/Y') ?? '-' }}</td></tr>
            <tr><td><strong>Jenis Surat Jalan:</strong></td><td>{{ $suratJalan->jenis_surat_jalan ?? '-' }}</td></tr>
            <tr><td><strong>Kepada:</strong></td><td>{{ $suratJalan->kepada ?? '-' }}</td></tr>
            <tr><td><strong>Berdasarkan:</strong></td><td>{{ $suratJalan->berdasarkan ?? '-' }}</td></tr>
            <tr><td><strong>Status:</strong></td><td>{{ $suratJalan->status ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="col-md-6">
        <table class="table table-borderless table-sm">
            <tr><td><strong>Kendaraan:</strong></td><td>{{ $suratJalan->kendaraan ?? '-' }}</td></tr>
            <tr><td><strong>No Polisi:</strong></td><td>{{ $suratJalan->no_polisi ?? '-' }}</td></tr>
            <tr><td><strong>Pengemudi:</strong></td><td>{{ $suratJalan->pengemudi ?? '-' }}</td></tr>
            <tr><td><strong>Dibuat Oleh:</strong></td><td>{{ $suratJalan->creator->nama ?? '-' }}</td></tr>
            <tr><td><strong>Disetujui Oleh:</strong></td><td>{{ $suratJalan->approver->nama ?? '-' }}</td></tr>
            <tr><td><strong>Keterangan:</strong></td><td>{{ $suratJalan->keterangan ?? '-' }}</td></tr>
        </table>
    </div>
</div>

<hr>

<h6><strong>Detail Material:</strong></h6>
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th>Kode Material</th>
                <th>Deskripsi Material</th>
                <th>Quantity</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suratJalan->details as $detail)
            <tr>
                @if($detail->is_manual)
                    {{-- Mode Manual --}}
                    <td>-</td>
                    <td>{{ $detail->nama_barang_manual ?? '-' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->satuan_manual ?? $detail->satuan ?? '-' }}</td>
                    <td>{{ $detail->keterangan ?? '-' }}</td>
                @else
                    {{-- Mode Normal --}}
                    <td>{{ $detail->material->material_code ?? '-' }}</td>
                    <td>{{ $detail->material->material_description ?? '-' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->satuan ?? '-' }}</td>
                    <td>{{ $detail->keterangan ?? '-' }}</td>
                @endif
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
