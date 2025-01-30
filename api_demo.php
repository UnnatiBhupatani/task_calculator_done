<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

switch ($method) {
    case 'GET': // Fetch Tasks
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? LIMIT ? OFFSET ?");
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $totalTasks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalTasks / $limit);

        echo json_encode(["tasks" => $tasks, "totalPages" => $totalPages,"totalTasks" => $totalTasks ]);
        break;

    case 'POST': // Add Task
        if (!isset($data['task_name']) || !isset($data['category'])) {
            echo json_encode(["error" => "Task name and category are required"]);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, category) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $data['task_name'], $data['category']]);
        echo json_encode(["message" => "Task added successfully"]);
        break;

    case 'PUT': // Update Task
        if (!isset($data['task_id']) || !isset($data['task_name']) || !isset($data['category'])) {
            echo json_encode(["error" => "Task ID, name, and category are required"]);
            exit;
        }
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, category = ? WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$data['task_name'], $data['category'], $data['task_id'], $user_id]);
        echo json_encode(["message" => "Task updated successfully"]);
        break;

    case 'DELETE': // Delete Task
        if (!isset($data['task_id'])) {
            echo json_encode(["error" => "Task ID is required"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$data['task_id'], $user_id]);
        echo json_encode(["message" => "Task deleted successfully"]);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}
?>
