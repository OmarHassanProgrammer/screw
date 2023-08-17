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
                if($row['action'] != "after") {
                    echo json_encode(array("msg" => 'not'));
                    exit;
                } else {
                    if($_POST['card'] != "drawn") {
                        echo json_encode(array("msg" => "not"));
                    } else {
                        $thrown = $row['thrown'] . ',' . $row['lastThrown'];

                        $card = $row["lastThrown"] = $row["drawn"];
                        
                        if($card <= 5 || $card >= 14) {
                            $players = explode(',', $row['players']);
                            array_pop($players);
                            array_shift($players);
                            $i = array_search($row["turn"], $players);

                            if($row['screw'] == $players[($i + 1) % 4]) {
                                require "end.php";
                            } else {
                                $row["turn"] = $players[($i + 1) % 4];
                                $row["action"] = "before";
                            }
                        } else {
                            $row['active'] = $card;
                            $row["action"] = "post";
                            $row["activeStep"] = 0;
                            $row["subCard"] = '';
                        }

                        $query = "UPDATE room SET thrown=?, lastThrown=?, turn=?, action=?, active=?, activeStep=?, subCard=?, drawn=-1 WHERE password = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ssssssss', $thrown, $row['lastThrown'], $row['turn'], $row['action'], $row['active'], $row['activeStep'], $row['subCard'], $_POST['roomPass']);
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


?>
