<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tambah Budget</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('budget.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Tambah</button>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Daftar Budget</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($budgets as $budget)
                            <tr>
                                <td>{{ $budget->name }}</td>
                                <td>Rp {{ number_format($budget->amount, 0, ',', '.') }}</td>
                                <td>{{ $budget->category }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data budget.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
