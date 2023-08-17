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
                if($row['action'] == "post") {

                    if($row['active'] == 13) {
                        $cardI = explode('-', $_POST['card'])[0];
                        
                        if($nrow['id'] == $cardI) {
                        
                            $player = array();
                            $card = "";

                            $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param('ss', $cardI, $_POST['roomPass']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if($result->num_rows == 0) {
                                echo json_encode(array("msg" => "nouser"));
                                exit;
                            } else {
                                $player = $result->fetch_assoc();
                                $card = explode(',', $player["cards"])[explode("-", $_POST['card'])[1]];

                                $newCards = explode(',', $player['cards']);
                                array_splice($newCards, explode("-", $_POST['card'])[1], 1);
                                $newThrown = explode(',', $row['thrown']);
                                array_push($newThrown, $row['lastThrown']);
                                $row['lastThrown'] = $card;
                                $row['thrown'] = implode(',', $newThrown);
        
                                $player['cards'] = implode(',', $newCards);
                                $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('sss', $player['cards'], $player['player_id'], $_POST['roomPass']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if($result) {
                                    echo json_encode(array("msg" => 'not'));
                                    exit;
                                }
        
                                $query = "UPDATE room SET active=-1, thrown=? , lastThrown=?, card='', action='wait' WHERE password = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('sss', $row['thrown'], $row['lastThrown'], $_POST['roomPass']);
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
            }
        }
    }

?>
