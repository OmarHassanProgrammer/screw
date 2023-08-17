<?php
    require "connect_database.php";

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
        
        $cards = [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 13, 13, 14, 14, 14, 14, 15, 16, 16, 17, 17];
        shuffle($cards);
        
        $pcards = array(array(), array(), array(), array());
        
        array_push($pcards[0], $cards[0]);
        array_push($pcards[0], $cards[1]);
        array_push($pcards[0], $cards[2]);
        array_push($pcards[0], $cards[3]);
        
        array_push($pcards[1], $cards[4]);
        array_push($pcards[1], $cards[5]);
        array_push($pcards[1], $cards[6]);
        array_push($pcards[1], $cards[7]);
        
        array_push($pcards[2], $cards[8]);
        array_push($pcards[2], $cards[9]);
        array_push($pcards[2], $cards[10]);
        array_push($pcards[2], $cards[11]);
        
        array_push($pcards[3], $cards[12]);
        array_push($pcards[3], $cards[13]);
        array_push($pcards[3], $cards[14]);
        array_push($pcards[3], $cards[15]);

        $lastThrown = $cards[16];
        $turn = explode(",", $row['players'])[rand(0, 3)];
        $c = implode(",", $cards);
        
        array_splice($cards, 0, 17);

        $query = "UPDATE room SET lastThrown=?, deck=?, turn=?, started=0, card='', mode='play', screw=-1, active=-1, activeStep=-1, thrown='', action='before', subCard='', drawThrow=0, drawn=-1, reveal='-1,-1' WHERE password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssss', $lastThrown, $c, $turn, $_POST['roomPass']);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result) {
            echo json_encode(array("msg" => 'not'));
        } else {
            $players = explode(",", $row['players']);
            array_pop($players);
            array_shift($players);
            foreach ($players as $key => $player) {
                $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                $stmt = $conn->prepare($query);
                $c = implode(",", $pcards[$key]);
                $stmt->bind_param('sss', $c, $player, $row['password']);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if($result) {
                    echo json_encode(array("msg" => 'not'));
                } else {

                }
            }
            if(!isset($f)) {
                echo json_encode(array("msg" => "done"));
            }
        }

    }

?>
