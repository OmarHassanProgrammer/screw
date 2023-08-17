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
            
            if(count(explode(",", $nrow['players'])) >= 6) {
                echo json_encode(array("msg" => "full"));
                exit;
            } else if ($nrow['mode'] == "ended") {
                echo json_encode(array("msg" => "ended"));
                exit;
            } else {
                $newPlayers = $nrow['players'] . $row['id'] . ",";
    
                $query = "UPDATE room SET players = ? WHERE password = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $newPlayers, $_POST['roomPass']);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if($result) {
                    echo json_encode(array("msg" => 'not'));
                } else {
                    $query = "INSERT INTO player_room (player_id, room_pass) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $p = $row['id'];
                    $stmt->bind_param('ss', $row['id'], $_POST['roomPass']);

                    if($results = $stmt->execute()) {
                        echo json_encode(array("msg" => "done", "roomPass" => $_POST['roomPass']));
                        if(count(explode(",", $nrow['players'])) == 5) {
                            $f = true;
                            require "newRound.php";
                        }
                        exit;
                    } else {
                        echo json_encode(array("msg" => "not"));
                        exit;
                    }                    
                }
            }
        }
    }

    