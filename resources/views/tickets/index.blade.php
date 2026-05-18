@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-headset me-2"></i>IT Help Desk Monitor</h5>
            <button class="btn btn-light btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="fas fa-plus"></i> Buat Tiket
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="ticketTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Kategori</th>
                            <th>Kendala</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Buat Tiket Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCreate">
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama Karyawan</label>
                        <input type="text" name="nama_karyawan" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Divisi</label>
                        <input type="text" name="divisi" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Network">Network</option>
                            <option value="Other">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Keperluan</label>
                        <input type="text" name="keperluan" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Detail Kendala</label>
                        <textarea name="detail_kendala" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Update Tiket #<span id="editTicketIdDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUpdate">
                <input type="hidden" id="editId">
                <div class="modal-body">
                    <div class="alert alert-secondary py-2 small">
                        <strong>Pelapor:</strong> <span id="viewNama"></span> (<span id="viewDivisi"></span>)<br>
                        <strong>Masalah:</strong> <span id="viewKendala"></span>
                    </div>

                    <div class="mb-2">
                        <label>PIC (Tech Support)</label>
                        <input type="text" id="editPic" name="pic" class="form-control" placeholder="Nama Teknisi">
                    </div>
                    <div class="mb-2">
                        <label>Status</label>
                        <select id="editStatus" name="status" class="form-select">
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Penanganan / Solusi</label>
                        <textarea id="editPenanganan" name="penanganan" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-2">
                        <label>Tingkat Kesulitan</label>
                        <select id="editKesulitan" name="tingkat_kesulitan" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // 1. Fungsi Load Data Tabel
    function loadTickets() {
        $.ajax({
            url: "{{ route('tickets.data') }}",
            type: "GET",
            success: function(response) {
                let rows = '';
                $.each(response, function(key, item) {
                    let badgeColor = 'secondary';
                    if(item.status == 'Open') badgeColor = 'danger';
                    if(item.status == 'In Progress') badgeColor = 'warning text-dark';
                    if(item.status == 'Resolved') badgeColor = 'success';
                    if(item.status == 'Closed') badgeColor = 'dark';

                    // Format Tanggal JS
                    let date = new Date(item.created_at).toLocaleDateString('id-ID');

                    rows += `
                        <tr>
                            <td>#${item.id}</td>
                            <td>${date}</td>
                            <td>
                                <strong>${item.nama_karyawan}</strong><br>
                                <small class="text-muted">${item.divisi}</small>
                            </td>
                            <td>${item.kategori}</td>
                            <td>${item.keperluan}</td>
                            <td><span class="badge bg-${badgeColor}">${item.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning edit-btn" data-id="${item.id}">
                                    <i class="fas fa-edit"></i> Proses
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#ticketTable tbody').html(rows);
            }
        });
    }

    // Load pertama kali
    loadTickets();

    // 2. Handle Submit Create Ticket
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('tickets.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                $('#modalCreate').modal('hide'); // Tutup modal
                $('#formCreate')[0].reset(); // Reset form
                loadTickets(); // Reload tabel tanpa refresh page
                Swal.fire('Sukses!', 'Tiket berhasil dibuat.', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal membuat tiket.', 'error');
            }
        });
    });

    // 3. Handle Klik Tombol Edit (Tampilkan Modal Update)
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('tickets.show', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "GET",
            success: function(response) {
                // Isi data ke modal update
                $('#editId').val(response.id);
                $('#editTicketIdDisplay').text(response.id);
                
                // Info Read Only
                $('#viewNama').text(response.nama_karyawan);
                $('#viewDivisi').text(response.divisi);
                $('#viewKendala').text(response.detail_kendala);

                // Form Input
                $('#editPic').val(response.pic);
                $('#editStatus').val(response.status);
                $('#editPenanganan').val(response.penanganan);
                $('#editKesulitan').val(response.tingkat_kesulitan);

                $('#modalUpdate').modal('show');
            }
        });
    });

    // 4. Handle Submit Update Ticket
    $('#formUpdate').on('submit', function(e) {
        e.preventDefault();
        let id = $('#editId').val();
        let formData = $(this).serialize();
        let url = "{{ route('tickets.update', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: "PUT",
            data: formData,
            success: function(response) {
                $('#modalUpdate').modal('hide');
                loadTickets();
                Swal.fire('Terupdate!', 'Status tiket berhasil diperbarui.', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal update tiket.', 'error');
            }
        });
    });

    // Auto refresh tabel setiap 30 detik (Opsional)
    setInterval(loadTickets, 30000);
});
</script>

@endsection