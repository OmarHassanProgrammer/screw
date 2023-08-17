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

            echo json_encode(array(
                "lastThrown" => $nrow["lastThrown"], 
                "turn" => $nrow["turn"], 
                "msg" => "done",
                "isThereCard" => (count(explode(",", $nrow["deck"])) - 1 > 0),
                "screw" => $nrow["screw"],
                "mode" => $nrow["mode"],
                "active" => $nrow["active"],
                "activeStep" => $nrow["activeStep"],
                "drawn" => $nrow["drawn"] != -1? $nrow["turn"] == $row["id"]? $nrow["drawn"] : 0: -1,
                "drawThrow" => $nrow["drawThrow"],
                "subCard" => $nrow["subCard"],
                "action" => $nrow["turn"] == $row["id"]?$nrow["action"]:"wait",
                "card" => $nrow["card"]
            ));
        }
    }
?>