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
            
            $cardsScores = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 10, 20, -1, 25, 0];
            $newScores = [0,0,0,0];
            $oldScores = [];

            $players = explode(',', $row['players']);
            array_pop($players);
            array_shift($players);
            foreach ($players as $key => $player) {
                $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $player, $_POST['roomPass']);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows == 0) {
                    echo json_encode(array("msg" => "nouser"));
                    exit;
                } else {
                    $p = $result->fetch_assoc();

                    array_push($oldScores, explode(',', $p['scores']));

                    foreach (explode(',', $p['cards']) as $k => $card) {
                        $newScores[$key] += $cardsScores[(int)$card - 1];
                    }

                }
            }

            $minValue = min($newScores);
            $minIndexes = [];
            foreach ($newScores as $index => $value) {
                if ($value === $minValue) {
                    array_push($oldScores[$index], 0);
                } else {
                    array_push($oldScores[$index], $value);
                }
            }

            foreach ($players as $key => $player) {
                $query = "UPDATE player_room SET scores = ? WHERE player_id = ? AND room_pass = ?";
                $stmt = $conn->prepare($query);
                $newScore = implode(',', $oldScores[$key]);
                $stmt->bind_param('sss', $newScore, $player, $_POST['roomPass']);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result) {
                    echo json_encode(array("msg" => 'not'));
                    exit;
                }
            }

            $query = "UPDATE room SET mode='show', action='wait' WHERE password = ?";
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


?>
