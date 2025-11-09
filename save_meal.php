<?php
header('Content-Type: application/json; charset=utf-8');

// Show errors for debugging (turn off in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB config
$host = '127.0.0.1';
$db   = 'karmiccanteen';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error', 'message' => 'DB connection failed: '.$e->getMessage()]);
    exit;
}

// Read JSON input
$raw = file_get_contents('php://input');
if (!$raw) {
    http_response_code(400);
    echo json_encode(['status'=>'error', 'message'=>'No input received.']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['status'=>'error', 'message'=>'Invalid JSON.']);
    exit;
}

$name = trim($data['employee_name'] ?? '');
$meals = $data['meals'] ?? [];

if ($name === '') {
    http_response_code(400);
    echo json_encode(['status'=>'error', 'message'=>'Employee name is required.']);
    exit;
}
if (!is_array($meals) || count($meals) === 0) {
    http_response_code(400);
    echo json_encode(['status'=>'error', 'message'=>'At least one meal must be selected.']);
    exit;
}

$meals_text = implode(", ", $meals);

try {
    $stmt = $pdo->prepare("INSERT INTO meal_selection (employee_name, meals) VALUES (:name, :meals)");
    $stmt->execute(['name'=>$name, 'meals'=>$meals_text]);
    echo json_encode(['status'=>'success', 'message'=>'Saved', 'insertId' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error', 'message'=>'Insert failed: '.$e->getMessage()]);
}
