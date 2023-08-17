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
                    if(explode('-', $_POST['card'])[0] == $nrow['id']) {
                        $player = array();
                        $card = "";

                        $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $i = explode("-", $_POST['card'])[0];
                        $stmt->bind_param('ss', $i, $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if($result->num_rows == 0) {
                            echo json_encode(array("msg" => "nouser"));
                            exit;
                        } else {
                            $player = $result->fetch_assoc();
                            $card = explode(',', $player["cards"])[explode("-", $_POST['card'])[1]];
                            
                            if($card == $row['lastThrown']){
                                $newCards = explode(',', $player['cards']);
                                array_splice($newCards, explode("-", $_POST['card'])[1], 1);
                                $newThrown = explode(',', $row['thrown']);
                                array_push($newThrown, $row['lastThrown']);
                                $row['lastThrown'] = $card;
                                $row['thrown'] = implode(',', $newThrown);
                            } else if (($row['lastThrown'] == 16 && $card == 17) ||
                            ($row['lastThrown'] == 17 && $card == 16)) { 
                                $newCards = explode(',', $player['cards']);
                                array_splice($newCards, explode("-", $_POST['card'])[1], 1);
                                $newThrown = explode(',', $row['thrown']);
                                array_push($newThrown, $row['lastThrown']);
                                $row['lastThrown'] = $card;
                                $row['thrown'] = implode(',', $newThrown);
                            } else {
                                $newCards = explode(',', $player['cards']);
                                array_push($newCards, $row['lastThrown']);
                                $newThrown = explode(',', $row['thrown']);
                                $row['lastThrown'] = end($newThrown);
                                array_pop($newThrown);
                                $row['thrown'] = implode(',', $newThrown);
                            }
                            
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
    
                            $query = "UPDATE room SET drawThrow=0, subCard='', thrown=? , lastThrown=?, action='wait' WHERE password = ?";
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


                    } else {
                        echo json_encode(array("msg" => "nouser"));
                    }
                } else {
                    echo json_encode(array("msg" => 'not'));
                }
            }
        }
    }

?>
