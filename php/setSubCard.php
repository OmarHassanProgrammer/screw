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
                if($row['action'] != "post") {
                    echo json_encode(array("msg" => 'not'));
                    exit;
                } else {
                    $pastActive = $row["active"];
                    
                    if($row["active"] == 11 && $row["subCard"] == "") {
                        $query = "UPDATE room SET subCard=? WHERE password = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $_POST['card'], $_POST['roomPass']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if($result) {
                            echo json_encode(array("msg" => 'not'));
                        } else {
                            echo json_encode(array("msg" => "done", "pastActive" => $pastActive));
                        }
                    } else {
                        echo json_encode(array("msg" => 'not'));
                    }
                }
            }
        }
    }




?>
