<?php
session_start();
function initializeTodolist() {
    if (!isset($_SESSION['todolist'])) {
        $_SESSION['todolist'] = [];
    }
}

// fungsi menambahkan data
function addTask($task, $createdAt) {
    $newTask = htmlspecialchars(trim($task));
    $createdAt = $createdAt ?? date('Y-m-d H:i:s');
    if ($newTask !== '') {
        $_SESSION['todolist'][] = [
            'text' => $newTask,
            'done' => false,
            'created_at' => $createdAt,
            'completed_at' => null
        ];
    }
}

// fungsi mengedit data
function editTask($index, $task, $createdAt = null) {
    if (isset($_SESSION['todolist'][$index])) {
        $_SESSION['todolist'][$index]['text'] = htmlspecialchars(trim($task));
        $_SESSION['todolist'][$index]['created_at'] = $createdAt ?? $_SESSION['todolist'][$index]['created_at'];
    }
}

// fungsi menghapus data
function deleteTask($index) {
    if (isset($_SESSION['todolist'][$index])) {
        unset($_SESSION['todolist'][$index]);
        $_SESSION['todolist'] = array_values($_SESSION['todolist']);
    }
}

// fungsi untuk menyelesaikan data
function toggleTask($index, $isDone) {
    if (isset($_SESSION['todolist'][$index])) {
        $_SESSION['todolist'][$index]['done'] = $isDone;
        $_SESSION['todolist'][$index]['completed_at'] = $isDone ? date('Y-m-d H:i:s') : null;
    }
}
?>
