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
        $p = "," . $row['id'] . ",";

        $query = "SELECT * FROM room WHERE password = ? AND players LIKE '%$p%'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $_POST['roomPass']);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0) {
            echo json_encode(array("msg" => "noroom"));
            exit;
        } else {
            $nrow = $result->fetch_assoc();

            if(count(explode(",", $nrow["players"])) > 0) {
                $me = $row;

                $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $me['id'], $_POST['roomPass']);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows == 0) {
                    echo json_encode(array("msg" => "noroom"));
                    exit;
                } else {
                    $mrow = $result->fetch_assoc();
                    
                    $show = -1;
                    
                    $cards = explode(",", $mrow["cards"]);
                    if($cards != [""]) {
                        if($nrow['mode'] != "show") {
                            $revealTo = explode(",", $nrow['reveal'])[0];
                            if($revealTo == $me['id'] || $revealTo == '0') {
                                for($i = 0; $i < count($cards); $i++) {
                                    if (explode(",", $nrow['reveal'])[1] == $me['id'] . '-' . $i) {
                                        $show = $i;
                                    }
                                }
                            }
                            if(!$nrow["started"]) {
                                if($show != 2)
                                    if(count($cards) >= 3)
                                        $cards[2] = 0;
                                if($show != 3)
                                    if(count($cards) == 4)
                                        $cards[3] = 0;
                            } else {
                                for($i = 0; $i < count($cards); $i++) {
                                    if($show != $i)
                                        $cards[$i] = 0;
                                }
                            }
                        }
                    
                        $me['cards'] = $cards;
                    } else {
                        $me['cards'] = [];
                    }

                    $scores = explode(",", $mrow['scores']);
                    array_shift($scores);
                    $me['scores'] = $scores != [""]?$scores:[];
                }

                $sortedPlayers = array();
                $players = explode(",", $nrow["players"]);
                array_pop($players);
                array_shift($players);
                $myIndex = array_search($me['id'], $players);
                for($i = 1; $i <= count($players) - 1; $i++) {
                    $query = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $index = $players[($myIndex + $i) % count($players)];
                    $stmt->bind_param('s', $index);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if($result->num_rows == 0) {
                        echo json_encode(array("msg" => "nouser"));
                        exit;
                    } else {
                        $player = $result->fetch_assoc();

                        $query = "SELECT * FROM player_room WHERE player_id = ? AND room_pass = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $player['id'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
    
                        if($result->num_rows == 0) {
                            echo json_encode(array("msg" => "noroom"));
                            exit;
                        } else {
                            $mrow = $result->fetch_assoc();
                            $scores = explode(",", $mrow['scores']);
                            array_shift($scores);
                            $player['scores'] = $scores != [""]?$scores:[];

                            $cards = explode(",", $mrow['cards']);

                            if($cards != [""]) {
                                if($nrow['mode'] != "show") {
                                    $show = -1;
                                    $revealTo = explode(",", $nrow['reveal'])[0];
                                    if($revealTo == $me['id'] || $revealTo == '0') {
                                        for($j = 0; $j < count($cards); $j++) {
                                            if (explode(",", $nrow['reveal'])[1] == $player['id'] . '-' . $j) {
                                                $show = $j;
                                            }
                                        }
                                    }
                                    for($j = 0; $j < count($cards); $j++) {
                                        if($show != $j)
                                            $cards[$j] = 0;
                                    }
                                }

                                $player['cards'] = $cards;
                            } else {
                                $player['cards'] = [];
                            }

                            array_push($sortedPlayers, $player);
                        }
                    }

                    
                }

                echo json_encode(array("msg" => "done", "players" => $sortedPlayers, "me" => $me));
            } else {
                echo json_encode(array("msg" => "not"));
                exit;
            }
        }
    }
    
?>