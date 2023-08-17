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
                if($row['action'] == "after") {
                    if($row["drawThrow"]) {
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
                        }
                        
                        $cards = explode(",", $player["cards"]);
                        $cards[explode("-", $_POST['card'])[1]] = $row['lastThrown'];
                        $player["cards"] = implode(",", $cards);
                        
                        $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $player['cards'], $player['player_id'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                            exit;
                        }

                        $query = "UPDATE room SET lastThrown=?, card=?, action='wait', drawThrow=0 WHERE password = ?";
                        $stmt = $conn->prepare($query);
                        $row['thrown'] .= ',' . $row['lastThrown'];
                        $stmt->bind_param('sss', $card, $_POST['card'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                        } else {
                            echo json_encode(array("msg" => "done"));
                        }
                    
                    } else {
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
                        }
                        
                        $cards = explode(",", $player["cards"]);
                        $cards[explode("-", $_POST['card'])[1]] = $row["drawn"];
                        $player["cards"] = implode(",", $cards);

                        $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $player['cards'], $player['player_id'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                            exit;
                        }

                        
                        $query = "UPDATE room SET drawn=-1, thrown=?, card=?, action='wait', lastThrown=? WHERE password = ?";
                        $stmt = $conn->prepare($query);
                        $row['thrown'] .= ',' . $row['lastThrown'];
                        $stmt->bind_param('ssss', $row['thrown'], $_POST['card'], $card, $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                        } else {
                            echo json_encode(array("msg" => "done"));
                        }

                    }
                } else if ($row['action'] == "post") {
                    if($row["active"] == 11) {
                        $player1 = array();
                        $card1 = "";
                        $player2 = array();
                        $card2 = "";

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
                            $player1 = $result->fetch_assoc();
                            $card1 = explode(',', $player1["cards"])[explode("-", $_POST['card'])[1]];
                        }

                        $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $i = explode("-", $row["subCard"])[0];
                        $stmt->bind_param('ss', $i, $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if($result->num_rows == 0) {
                            echo json_encode(array("msg" => "nouser"));
                            exit;
                        } else {
                            $player2 = $result->fetch_assoc();
                            $card2 = explode(',', $player2["cards"])[explode("-", $row["subCard"])[1]];
                        }
                        $cards = explode(",", $player2["cards"]);
                        $cards[explode("-", $row["subCard"])[1]] = $card1;
                        $player2["cards"] = implode(",", $cards);
                        
                        $cards = explode(",", $player1["cards"]);
                        $cards[explode("-", $_POST['card'])[1]] = $card2;
                        $player1["cards"] = implode(",", $cards);

                        //echo $player1['id'] . ' ' . explode("-", $_POST['card'])[1] . ' ' . $player2['id'] . ' ' . explode("-", $row["subCard"])[1];

                        $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $player1['cards'], $player1['player_id'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                            exit;
                        }

                        $query = "UPDATE player_room SET cards = ? WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $player2['cards'], $player2['player_id'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                            exit;
                        }

                        $query = "UPDATE room SET active=-1, card=?, action='wait' WHERE password = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $_POST['card'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                        } else {
                            echo json_encode(array("msg" => "done"));
                        }
                    }
                } else {
                    echo json_encode(array("msg" => 'not'));
                    exit;
                }
            }
        }
    }


?>
