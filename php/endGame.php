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
        
        $query = "UPDATE room SET lastThrown=-1, deck='', turn=0, started=0, card='', mode='ended', screw=-1, active=-1, activeStep=-1, thrown='', action='before', subCard='', drawThrow=0, drawn=-1, reveal='-1,-1' WHERE password = ?";
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


?>
