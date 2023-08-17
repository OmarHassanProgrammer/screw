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
                $players = explode(',', $row['players']);
                array_pop($players);
                array_shift($players);
                $i = array_search($row["turn"], $players);

                if($row['screw'] == $players[($i + 1) % 4]) {
                    require "end.php";
                } else {
                    $row["turn"] = $players[($i + 1) % 4];

                    $query = "UPDATE room SET turn=?, reveal='-1,-1', card='', subCard='', action='before', active=-1 WHERE password = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ss', $row['turn'], $_POST['roomPass']);
                    $stmt->execute();
                    $result = $stmt->get_result();
            
                    if(!isset($a)) {
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                        } else {
                            echo json_encode(array("msg" => "done"));
                        }
                    }
            
                }
            }
        }
    }
?>
