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

        if($result->num_rows == 0) {
            echo json_encode(array("msg" => "noroom"));
            exit;
        } else {
            $nrow = $result->fetch_assoc();

            $found = false;
            foreach (explode(",", $nrow['players']) as $key => $player) {
                if(!$found) {
                    if($player == $row['id']) {
                        $found = true;
                    }
                }
            }

            if($found) {
                echo json_encode(array("msg" => "done"));
                exit;
            } else {
                echo json_encode(array("msg" => "noroom"));
                exit;
            }
        }
    }