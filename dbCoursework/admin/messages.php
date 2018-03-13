<?php $siteroot = '/Databases-Group22/dbCoursework'; ?>


<?php include $_SERVER['DOCUMENT_ROOT']."$siteroot/dashboard/baseHead.php";?>


</head>

  <body>

    <?php include ('baseHeader.php');?>
    <?php include('sideMenu.php'); ?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

  <h1 class="page-header">Messages</h1>

  <div class="container-fluid panel panel-success" style="padding-top: 30px; border: 3px solid transparent; border-color: #d6e9c6;">
    
    <?php

        //create connection
        $conn = new PDO("mysql:host=ibe-database.mysql.database.azure.com;dbname=ibe_dbv3;charset=utf8","team22@ibe-database","ILoveCS17");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Array of most recent bids on each item for the current user:
        $query = "select * 
        FROM communication c JOIN users u
        ON c.senderID = u.userID
        WHERE  receiverID=17 AND communicationType='Private_Message' " ;
        $statement = $conn->prepare($query);
        $statement->execute();

    if(!empty($statement)) { ?>

      <!-- TABLE OF ITEMS CURRENTLY BIDDING ON -->
      <table class="table table-dark" >
          <thead>
              <tr scope="row">
                  <th scope="col">Username</th>
                  <th scope="col">Email</th>
                  <th scope="col">Company Name</th>
                  <th scope="col">Date</th>
                  <th scope="col">Message</th>
                  <th scope="col">Resolved?</th>
              </tr>
          </thead>
        <tbody id="all items">
            <?php

                foreach ($statement as $row)
                {
                    if(true){
                    include "message_row.php";
                    }
                }

            ?>


        </tbody>
      </table>
    <?php } else { echo "<p style='font-style: italic; font-size: 24px; color: grey;'>Hooray!! There are no messages yet.</p>";} ?>
  </div>
</div>




    <?php include $_SERVER['DOCUMENT_ROOT']."$siteroot/dashboard/baseFooter.php";?>

  </body>

</html>



