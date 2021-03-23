<?php
  $photoName = (string) $_POST['photoName'];
  $photoDate = (string) $_POST['photoDate'];
  $photographer = (string) $_POST['photographer'];
  $location = preg_replace('/\t|\R/',' ',$_POST['location']);
  $fileToUpload = (string) $_FILES['fileToUpload']['name'];
  $date = date('H:i, jS F Y');
  ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>CPSC 431, Assignment 1</title>
  </head>
  <body>
    <div class="jumbotron text-center" style="padding: 50px; background-color: #778899; color: white;">
      <h1>Simple Photo Gallery</h1>
      <p>Created by Adam Laviguer and Hammad Qureshi</p>
    </div>

    <div class="container" style="margin-top: 25px;">
      <h3>View All Photos</h3>
      <div class="d-flex flex-row">
        <div style="padding-right: 10px;">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
              Sort By
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
              <li><a class="dropdown-item">Name</a></li>
              <li><a class="dropdown-item">Date action</a></li>
              <li><a class="dropdown-item">Photographer</a></li>
              <li><a class="dropdown-item">Location</a></li>
            </ul>
          </div>
        </div>
        <div style="padding-left: 10px;">
          <form action="./index.html" method="post" enctype="multipart/form-data">
            <button type="submit" class="btn btn-primary">Upload New Photo</button>
          </form>
        </div>
      </div>
    </div>

    <div class="container">
      <?php
        if(isset($_POST['submitBtn'])) {
          //UploadPhoto();
          //Do a check for UploadPhoto - if it uploads a duplicate then do not UploadData
          UploadData($photoName, $photoDate, $photographer, $location, $fileToUpload, $date);
        }

        function UploadData($photoName, $photoDate, $photographer, $location, $fileToUpload, $date) {
          @$db = new mysqli('mariadb', 'cs431s23', 'Va7Wobi9', 'cs431s23');

          // Check database connection
          if (mysqli_connect_errno()) {
            echo "<p>Error: Could not connect to database.<br/>
                  Please try again later.</p>";
            exit;
          }

          $query = "INSERT INTO Images VALUES (?, ?, ?, ?)";
          $stmt = $db->prepare($query);
          $stmt->bind_param('sdss', $photoName, $photoDate, $photographer, $location);
          $stmt->execute();

          if ($stmt->affected_rows > 0) {
              echo  "<p>Image metadata inserted into the database.</p>";
          } else {
              echo "<p>An error has occurred.<br/>
              The image metadata was not added.</p>";
          }
          $db->close();     
        }

        function UploadPhoto() {
          $target_dir = "uploads/";
          $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
          $uploadOk = 1;
          $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

          // Check if image file is a actual image or fake image
          if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
              echo "File is an image - " . $check["mime"] . ".";
              $uploadOk = 1;
            } else {
              echo "File is not an image.";
              $uploadOk = 0;
            }
          }

          // Check if file already exists
          if (file_exists($target_file)) {
            echo "Sorry, file already exists.<br>";
            $uploadOk = 0;
          }

          // // Check file size
          // if ($_FILES["fileToUpload"]["size"] > 500000) {
          //   echo "Sorry, your file is too large.";
          //   $uploadOk = 0;
          // }

          // Allow certain file formats
          if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
          && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
            $uploadOk = 0;
          }

          // Check if $uploadOk is set to 0 by an error
          if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
          // if everything is ok, try to upload file
          } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
              echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
            } else {
              echo "Sorry, there was an error uploading your file.<br>";
            }
          }
        }
      ?>
    </div>

    <div class="container">
      <div class="row" style="padding-top: 25px;">
        <?php
        // scan "uploads" folder and display files
        $directory = "./uploads";
        $results = scandir('./uploads');

        foreach ($results as $result) {
          if ($result === '.' or $result === '..') {
            continue;
          }
          if (is_file($directory . '/' . $result)) {
            echo '
            <div class="col-md-3" style="padding-bottom: 25px; padding-top: 25px;">
              <div class="thumbnail">
                <img src="'.$directory . '/' . $result.'" alt="..." style="width:100%">
                  <!-- <div class="caption">
                    <p>'.$photoName.'<br>'.$photoDate.'<br>'.$photographer.'<br>'.$location.'</p>
                  </div> -->
              </div>
            </div>';
          }
        }
        ?>
      </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

  </body>
</html>
