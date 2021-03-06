<?php





        include 'vendor\email.php';







        try {
            $conn = new PDO("mysql:host=ibe-database.mysql.database.azure.com;dbname=ibe_dbv3;charset=utf8",
                            "team22@ibe-database",
                            "ILoveCS17");
        }
        catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
            echo '<script type="text/javascript"> console.log("connection to MySQL failed"); </script>';
        }



        echo '<script type="text/javascript"> console.log("connection Ok"); </script>';

        $statement = $conn->prepare("SELECT itemID, sellerID, title, endDate, startPrice, reservePrice, itemViewCount
        FROM items
        WHERE endDate > NOW() AND itemRemoved = 0");


        $statement->execute();
        $res = $statement->fetchAll();

        $emails = array();
        $subjects = array();
        $messages = array();



        foreach ($res as $searchResult) {




            $itemID = $searchResult['itemID'];
            $sellerID = $searchResult['sellerID'];
            $title = $searchResult['title'];
            $endDate = $searchResult['endDate'];
            $startPrice = $searchResult['startPrice'];
            $reservePrice = $searchResult['reservePrice'];
            $reservePrice  = $searchResult['reservePrice'];
            $itemViewcount = $searchResult['itemViewCount'];

          



            $bid_query = $conn->prepare("SELECT  bidAmount, bidDate
            FROM bids b1
            INNER JOIN (
            SELECT MAX(bidAmount) bidAmountA, itemID
            FROM bids
            GROUP BY itemID
            ) b2 ON b1.itemID = b2.itemID AND b1.bidAmount = b2.bidAmountA
            WHERE b1.itemID = ".$itemID."
            ");

            $bid_query->execute();
            $bid = $bid_query->fetch();

            $bidAmount = $bid['bidAmount'];
            $bidDate = $bid['bidDate'];





            $seller_query = $conn->prepare("SELECT u.email, c.firstName, c.lastName
            FROM users u JOIN clients c ON c.userID = u.userID
            WHERE u.userID = " .$sellerID. "");
            $seller_query->execute();
            $seller = $seller_query->fetch();

            $sellerFirstName = $seller['firstName'];
            $sellerLastName = $seller['lastName'];
            $sellerEmail = $seller['email'];





            $subject_seller = 'Listing update';
            $message_seller = 'Dear '.$sellerFirstName.' '.$sellerLastName.', please see an update of your listing below:<br>
            <table style="width:100%">
            <tr>
            <th> Item </th>
            <th> Start Price </th>
            <th> Reserve Price  </th>
            <th>  End Date </th>
            <th> Highest Bid </th>
            <th> Bid Date </th>
            </tr>
            <tr>
            <td>'.$title.'</td>
            <td>£'.$startPrice.'</td>
            <td>£'.$reservePrice.'</td>
            <td>'.$endDate.'</td>
            <td>£'.$bidAmount.'</td>
            <td>'.$bidDate.'</td>
            </tr>
            </table>';



            array_push($emails,$sellerEmail);
            array_push($subjects, $subject_seller);
            array_push($messages, $message_seller);



            try {

                $conn->beginTransaction();
    
                $insertCommunication = $conn->prepare("INSERT INTO communication (senderID, receiverID, message) VALUES (6, ?, ?) ");
                $insertCommunication -> execute([$sellerID, $message_seller]);
    
                $lastid = $conn->lastInsertId();
    
                $insertNotifications = $conn->prepare("INSERT INTO private_message (communicationID, messageSubject, messageResolved) VALUES (?, ?, 0)");
                $insertNotifications -> execute([$lastid, $subject_seller]);
    
                $conn->commit();
    
    
            } catch (Exception $e) {
                     echo $e->getMessage();
                     $conn->rollBack();
            }
    






    }

    send_email($emails, $subjects, $messages);



        ?>
