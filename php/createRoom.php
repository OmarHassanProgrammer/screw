<?php
    require "connect_database.php";

    $query = "SELECT * FROM users WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $_POST['token']);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0) {
        echo json_encode(array("msg" => "nouser"));
        exit;
    } else {
        $row = $result->fetch_assoc();
        
        $query = "SELECT * FROM room WHERE password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $_POST['roomPass']);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows != 0) {
            echo json_encode(array("msg" => "alreadyExists"));
            exit;
        } else {
            $query = "INSERT INTO room (password, players) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $p = "," . $row['id'] . "," ;
            $stmt->bind_param('ss', $_POST['roomPass'], $p);
            
            if($results = $stmt->execute()) {
                $query = "INSERT INTO player_room (player_id, room_pass) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $p = $row['id'];
                $stmt->bind_param('ss', $row['id'], $_POST['roomPass']);

                if($results = $stmt->execute()) {
                    echo json_encode(array("msg" => "done", "roomPass" => $_POST['roomPass']));
                    exit;
                } else {
                    echo json_encode(array("msg" => "not"));
                    exit;
                }
            } else {
                echo json_encode(array("msg" => "not"));
                exit;
            }
        }
    }

    