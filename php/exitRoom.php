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

            if($row['turn'] == $nrow['id']) {
                $a = "a";
                require_once "nextTurn.php";
            }

            $newPlayers = explode(",", $row['players']);
            array_splice($newPlayers, array_search($nrow['id'], $newPlayers), 1);

            $query = "UPDATE room SET players=? WHERE password = ?";
            $stmt = $conn->prepare($query);
            $p = implode(",", $newPlayers);
            $stmt->bind_param('ss', $p, $_POST['roomPass']);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if($result) {
                echo json_encode(array("msg" => 'not'));
            } else {
                $query = "DELETE FROM player_room WHERE player_id = ? AND room_pass = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $nrow['id'], $_POST['roomPass']);
                if ($stmt->execute()) {
                    echo json_encode(array("msg" => "done"));
                } else {
                    echo json_encode(array("msg" => "not"));
                }
            }
        }
    }
?>