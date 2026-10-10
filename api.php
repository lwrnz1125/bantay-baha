<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Connect o gumawa ng SQLite database
$db = new PDO('sqlite:database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Gumawa ng table kung wala pa
$db->exec("CREATE TABLE IF NOT EXISTS readings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sensorId TEXT,
    waterLevelCm REAL,
    timestamp TEXT
)");

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// POST /api/readings
if ($method === 'POST' && strpos($request, '/api/readings') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (!isset($data['sensorId']) || !isset($data['waterLevelCm']) || 
        !is_numeric($data['waterLevelCm']) || $data['waterLevelCm'] < 0 || $data['waterLevelCm'] > 500) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid reading data"]);
        exit();
    }

    $stmt = $db->prepare("INSERT INTO readings (sensorId, waterLevelCm, timestamp) VALUES (?, ?, ?)");
    $stmt->execute([$data['sensorId'], $data['waterLevelCm'], $data['timestamp']]);
    
    http_response_code(201);
    echo json_encode(["message" => "Reading saved successfully"]);

// GET /api/readings
} elseif ($method === 'GET' && strpos($request, '/api/readings') !== false) {
    // Kukunin ang 50 pinakabagong readings
    $stmt = $db->query("SELECT * FROM readings ORDER BY id DESC LIMIT 50");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

// GET /api/summary
} elseif ($method === 'GET' && strpos($request, '/api/summary') !== false) {
    // Placeholder para sa Part E (Analytics) na gagawin natin mamaya
    echo json_encode(["message" => "Summary endpoint is ready for analytics"]);

} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}
?>
