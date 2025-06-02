<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get the request URI and method
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string if exists
if (strpos($request_uri, '?') !== false) {
    $request_uri = substr($request_uri, 0, strpos($request_uri, '?'));
}

// Split the URI into segments
$uri_segments = explode('/', trim($request_uri, '/'));

// Check if this is an API request
if ($uri_segments[0] === 'oahub' && $uri_segments[1] === 'v1') {
    $endpoint = $uri_segments[2] ?? '';
    $param = $uri_segments[3] ?? null;
    $keyword = $uri_segments[4] ?? null;

    switch ($endpoint) {
        case 'meal':
            if ($param) {
                // Get specific meal
                $query = "SELECT * FROM meal WHERE sure_no = :sure_no";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":sure_no", $param);
            } else {
                // Get all meals
                $query = "SELECT * FROM meal";
                $stmt = $db->prepare($query);
            }
            break;

        case 'kavram':
            if ($param) {
                // Get specific concept
                $query = "SELECT * FROM kavramlar_sifatlar WHERE kavram_no = :kavram_no";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":kavram_no", $param);
            } else {
                // Get all concepts
                $query = "SELECT * FROM kavramlar_sifatlar";
                $stmt = $db->prepare($query);
            }
            break;

        case 'surelist':
            $query = "SELECT sure_no, sure_adi, ayet_sayisi FROM sure_list ORDER BY sure_no ASC";
            $stmt = $db->prepare($query);
            break;

        case 'version':
            $query = "SELECT * FROM version_info ORDER BY id DESC LIMIT 1";
            $stmt = $db->prepare($query);
            break;

        case 'count':
            if ($param === 'meal') {
                $query = "SELECT total_meal FROM count_stats ORDER BY id DESC LIMIT 1";
            } elseif ($param === 'kavram') {
                $query = "SELECT total_kavram FROM count_stats ORDER BY id DESC LIMIT 1";
            }
            $stmt = $db->prepare($query);
            break;

        case 'search':
            if ($param === 'meal') {
                $query = "SELECT * FROM meal WHERE 
                         ayet_arapca LIKE :keyword OR 
                         ayet_ie LIKE :keyword OR 
                         ayet_ahmed_samira LIKE :keyword OR 
                         ayet_latin LIKE :keyword";
                $keyword = "%$keyword%";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":keyword", $keyword);
            } elseif ($param === 'kavram') {
                $query = "SELECT * FROM kavramlar_sifatlar WHERE 
                         kavram_adi LIKE :keyword OR 
                         kavram_text LIKE :keyword OR 
                         kavram_detay LIKE :keyword";
                $keyword = "%$keyword%";
                $stmt = $db->prepare($query);
                $stmt->bindParam(":keyword", $keyword);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found."));
            exit();
    }

    try {
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($result)) {
            http_response_code(404);
            echo json_encode(array("message" => "No data found."));
        } else {
            http_response_code(200);
            echo json_encode($result);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Invalid API request."));
}
?> 