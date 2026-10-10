<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$db = new PDO('sqlite:database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS readings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sensorId TEXT,
    waterLevelCm REAL,
    timestamp TEXT
)");

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

if ($method === 'POST' && strpos($request, '/api/readings') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['sensorId']) || !isset($data['waterLevelCm']) || !is_numeric($data['waterLevelCm']) || $data['waterLevelCm'] < 0 || $data['waterLevelCm'] > 500) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid reading data"]);
        exit();
    }
    $stmt = $db->prepare("INSERT INTO readings (sensorId, waterLevelCm, timestamp) VALUES (?, ?, ?)");
    $stmt->execute([$data['sensorId'], $data['waterLevelCm'], $data['timestamp']]);
    http_response_code(201);
    echo json_encode(["message" => "Reading saved"]);

} elseif ($method === 'GET' && strpos($request, '/api/readings') !== false) {
    $stmt = $db->query("SELECT * FROM readings ORDER BY id DESC LIMIT 50");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} elseif ($method === 'GET' && strpos($request, '/api/summary') !== false) {
    // PART E: DATA ANALYTICS
    $stmt = $db->query("SELECT waterLevelCm FROM readings ORDER BY id DESC");
    $all = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($all) === 0) {
        echo json_encode(["error" => "No data"]);
        exit();
    }

    $min = min($all);
    $max = max($all);
    $avg = array_sum($all) / count($all);
    
    $normal = 0; $alert = 0; $danger = 0;
    foreach ($all as $lvl) {
        if ($lvl < 100) $normal++;
        elseif ($lvl < 150) $alert++;
        else $danger++;
    }

    $trend = "steady";
    if (count($all) >= 10) {
        $avgLast5 = array_sum(array_slice($all, 0, 5)) / 5;
        $avgPrev5 = array_sum(array_slice($all, 5, 5)) / 5;
        if ($avgLast5 > $avgPrev5 + 1) $trend = "rising";
        elseif ($avgLast5 < $avgPrev5 - 1) $trend = "falling";
    }

    $summary = [
        "min" => $min, "max" => $max, "average" => round($avg, 2),
        "counts" => ["normal" => $normal, "alert" => $alert, "danger" => $danger],
        "trend" => $trend
    ];

    // PART F: GENERATIVE AI ADVISORY
    if (isset($_GET['ai']) && $_GET['ai'] == 'true') {
        $apiKey = getenv('GEMINI_API_KEY'); 
        $fallback = "Ang average na lebel ng tubig ay " . round($avg, 2) . " cm ($trend). Manatiling alerto. / Average water level is " . round($avg, 2) . " cm ($trend). Stay alert.";

        if (!$apiKey) {
            $summary['advisory'] = $fallback; // Fallback kung walang API key
        } else {
            $prompt = "Write a short advisory (max 3 sentences) in Filipino and English for residents. Data: Average water level is " . round($avg, 2) . "cm, Trend is $trend. Normal is <100cm, Alert is 100-149cm, Danger is 150cm+.";
            
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
            $postData = json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $aiData = json_decode($response, true);
            if (isset($aiData['candidates'][0]['content']['parts'][0]['text'])) {
                $summary['advisory'] = $aiData['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $summary['advisory'] = $fallback;
            }
        }
    }
    echo json_encode($summary);
}
?>
