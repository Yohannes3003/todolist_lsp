<?php
require_once 'functions.php';
initializeTodolist();

if (!isset($_SESSION['todolist'])) {
    $_SESSION['todolist'] = [];
}

// Tambah tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && !isset($_POST['edit_index'])) {
    addTask($_POST['task'], $_POST['created_at'] ?? null);
    header("Location: ?");
    exit;
}

// Edit tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_index'])) {
    editTask((int) $_POST['edit_index'], $_POST['task'], $_POST['created_at'] ?? null);
    header("Location: ?");
    exit;
}

// Hapus tugas
if (isset($_GET['hapus'])) {
    deleteTask((int) $_GET['hapus']);
    header("Location: ?");
    exit;
}

// Toggle selesai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_index'])) {
    toggleTask((int) $_POST['toggle_index'], isset($_POST['is_done']) && $_POST['is_done'] === 'on');
    header("Location: ?");
    exit;
}

// Data edit
$taskToEdit = null;
$editIndex = null;

if (isset($_GET['edit'])) {
    $editIndex = (int) $_GET['edit'];
    if (isset($_SESSION['todolist'][$editIndex])) {
        $taskToEdit = $_SESSION['todolist'][$editIndex];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Todo List LSP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 min-h-screen px-4 py-6">

<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">📝 Todo List</h1>

    <button onclick="document.getElementById('modal').classList.remove('hidden')"
            class="mb-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded font-semibold">
        + Tambah Kegiatan
    </button>

    <ul class="space-y-3">
    <?php if (!empty($_SESSION['todolist'])): ?>
        <?php foreach ($_SESSION['todolist'] as $index => $task): ?>
            <li class="flex justify-between items-center p-3 bg-gray-50 rounded border hover:bg-gray-100">
                <div>
                    <div class="<?= $task['done'] ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                        <?= htmlspecialchars($task['text']) ?>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Dibuat: <?= date('Y-m-d H:i', strtotime($task['created_at'])) ?>

                        <?php if ($task['done'] && !empty($task['completed_at'])): ?>
                            

                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="?edit=<?= $index ?>" title="Edit" class="text-yellow-500 hover:text-yellow-600">
                        <i data-feather="edit"></i>
                    </a>
                    <button type="button" onclick="hapusTugas(<?= $index ?>)" title="Hapus" class="text-red-500 hover:text-red-600">
                        <i data-feather="trash-2"></i>
                    </button>
                    <form method="post">
                        <input type="hidden" name="toggle_index" value="<?= $index ?>">
                       <input type="checkbox" name="is_done" onchange="this.form.submit()" <?= $task['done'] ? 'checked' : '' ?> class="w-5 h-5 mt-2 accent-green-600">

                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="text-gray-400 italic">Tidak ada Kegiatan.</li>
    <?php endif; ?>
</ul>


<!-- Modal Tambah -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg relative">
        <h2 class="text-lg font-bold mb-4">Tambah Kegiatan</h2>
        <form method="post">
            <label class="block mb-2 text-sm font-semibold">Nama Kegiatan:</label>
            <input type="text" name="task" class="w-full border px-4 py-2 rounded mb-4" required>

            <label class="block mb-2 text-sm font-semibold">Tanggal Buat:</label>
            <input type="datetime-local" name="created_at" class="w-full border px-4 py-2 rounded mb-4" required>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">Batal</button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<?php if ($taskToEdit !== null): ?>
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg relative">
        <h2 class="text-lg font-bold mb-4">Edit Kegiatan</h2>
        <form method="post">
            <input type="hidden" name="edit_index" value="<?= $editIndex ?>">

            <label class="block mb-2 text-sm font-semibold">Nama Kegiatan:</label>
            <input type="text" name="task" value="<?= htmlspecialchars($taskToEdit['text']) ?>" class="w-full border px-4 py-2 rounded mb-4" required>

            <label class="block mb-2 text-sm font-semibold">Tanggal Buat:</label>
            <input type="datetime-local" name="created_at" value="<?= date('Y-m-d\TH:i', strtotime($taskToEdit['created_at'])) ?>" class="w-full border px-4 py-2 rounded mb-4" required>

            <div class="flex justify-end gap-2">
                <a href="?" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">Batal</a>
                <button type="submit"
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    feather.replace();

    function hapusTugas(index) {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Tugas akan dihapus secara permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?hapus=' + index;
            }
        });
    }
</script>

</body>
</html>
