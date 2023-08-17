<?php
    require "connect_database.php";

    $token = random_int(0, 100000);
    $currentDateTime = date("Y-m-d H:i:s");

    $query = "INSERT INTO users (name, token, last_seen) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $_POST['name'], $token, $currentDateTime);
    
    if($results = $stmt->execute()) {
        echo json_encode(array("msg" => "done", "token" => $token));
        exit;
    } else {
        echo json_encode(array("msg" => "not"));
        exit;
    }