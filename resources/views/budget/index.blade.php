<h2>Tambah Budget</h2>
<form method="POST" action="{{ route('budget.store') }}">
    @csrf
    <label>Nama:</label>
    <input type="text" name="name" required>
    <label>Jumlah:</label>
    <input type="number" name="amount" required>
    <label>Kategori:</label>
    <input type="text" name="category" required>
    <button type="submit">Tambah</button>
</form>

<h2>Daftar Budget</h2>
<table border="1">
    <tr>
        <th>Nama</th>
        <th>Jumlah</th>
        <th>Kategori</th>
    </tr>
    @foreach($budgets as $budget)
    <tr>
        <td>{{ $budget->name }}</td>
        <td>{{ $budget->amount }}</td>
        <td>{{ $budget->category }}</td>
    </tr>
    @endforeach
</table>
