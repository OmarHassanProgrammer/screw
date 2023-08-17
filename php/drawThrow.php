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
        $nrow = $result->fetch_assoc();

        $query = "SELECT * FROM room WHERE password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $_POST['roomPass']);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0) {
            echo json_encode(array("msg" => "noroom"));
            exit;
        } else {
            $row = $result->fetch_assoc();
            
            if($row['turn'] != $nrow['id']) {
                echo json_encode(array("msg" => 'notturn'));
            } else {
                if($row['action'] == "before") {
                    $query = "UPDATE room SET drawThrow=1, action='after' WHERE password = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('s', $_POST['roomPass']);
                    $stmt->execute();
                    $result = $stmt->get_result();
            
                    if($result) {
                        echo json_encode(array("msg" => 'not'));
                    } else {
                        echo json_encode(array("msg" => "done"));
                    }
                }
            }
        }
    }


?>
