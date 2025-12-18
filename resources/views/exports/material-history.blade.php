<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>No SLIP</th>
            <th>Masuk</th>
            <th>Keluar</th>
            <th>Sisa Persediaan</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($histories as $h)
            <tr>
                <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('Y-m-d') }}</td>
                <td>{{ $h->tipe }}</td>
                <td>{{ $h->no_slip ?? '-' }}</td>
                <td style="text-align: right;">{{ $h->masuk ?? 0 }}</td>
                <td style="text-align: right;">{{ $h->keluar ?? 0 }}</td>
                <td style="text-align: right;">{{ $h->sisa_persediaan ?? 0 }}</td>
                <td>{{ $h->catatan ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
