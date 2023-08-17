<?php
    require "connect_database.php";

    $query = "SELECT * FROM users WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $_POST['token']);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        echo json_encode(array("msg" => "not"));
        exit;
    } else {
        $row = $result->fetch_assoc();
        echo json_encode(array("msg" => "done", "player" => $row));
        exit;
    }
?>