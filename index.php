<!-- START DATE 8/28/2021 -->
<!-- UPDATE DATE 11/16/2021 -->


<!-- Search and Pagination -->
<?php
session_start();
if (isset($_SESSION['user_id'])) {
  $id = $_SESSION['user_id'];
}
require_once("perpage.php");
require_once("dbcontroller.php");
$db_handle = new DBController();

$title = "";
$author = "";
$topic = "";
$publication_day = "";
$publication_day = "";
$publication_year = "";

$queryCondition = "";
if (!empty($_POST["search"])) {
  foreach ($_POST["search"] as $k => $v) {
    if (!empty($v)) {

      $queryCases = array("title", "author", "topic", "publication_day", "publication_day", "publication_year");
      if (in_array($k, $queryCases)) {
        if (!empty($queryCondition)) {
          $queryCondition .= " OR ";
        } else {
          $queryCondition .= " WHERE ";
        }
      }
      switch ($k) {
        case "title":
          $title = $v;
          $queryCondition .= "title LIKE '%" . $v . "%'"  . "OR author LIKE'%" . $v . "%'"  . "OR topic LIKE'%" . $v . "%'";
          break;
      }
    }
  }
}
$orderby = " ORDER BY id desc";
$sql = "SELECT * from research " . $queryCondition;
$href = 'journals.php';

$perPage = 3;
$page = 1;
if (isset($_POST['page'])) {
  $page = $_POST['page'];
}
$start = ($page - 1) * $perPage;
if ($start < 0) $start = 0;

$query =  $sql . $orderby .  " limit " . $start . "," . $perPage;
$result = $db_handle->runQuery($query);

if (!empty($result)) {
  $result["perpage"] = showperpage($sql, $perPage, $href);
}
?>

<html>

<head>
  <title>Home</title>
  <script type="text/javascript" src="js/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/notification.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ChatBot -->
  <link rel="stylesheet" type="text/css" href="css/jquery.convform.css">
  <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="js/jquery.convform.js"></script>
  <script type="text/javascript" src="js/custom.js"></script>

  <style>
    .number {
      left: 55%;
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
  <?php

  $notif = "";
  $dbh = new PDO("mysql:host=localhost;dbname=journal", "root", "");
  $total_count = 0;
  $unseen_count = $dbh->prepare('select COUNT(*) as unseen_count from notification where seen_status="unseen" and user_id=?');
  $unseen_count->bindParam(1, $id);
  $unseen_count->execute();
  $unseened_count = $unseen_count->fetch();
  $total_count = $total_count + $unseened_count['unseen_count'];
  $unseen_count2 = $dbh->prepare('select * from inquiry where user_id=? and seen_status="unseen"');
  $unseen_count2->bindParam(1, $id);
  $unseen_count2->execute();
  while ($unseened_count2 = $unseen_count2->fetch()) {
    if (!empty($unseened_count2['reply'])) {
      $total_count = $total_count + 1;
    }
  }





  if (isset($_SESSION['user_id'])) {
    echo '<div class="navbar">
    <a href="index.php"><img style="height: 30px;" src="images/Logo.png"></a>
    <a style="margin-top: 6px;" href="research.php">RESEARCH</a>
    <a style="margin-top: 6px;" href="analytics.php">ANALYTICS</a>
    <a style="margin-top: 6px;" href="contact_us.php">CONTACT US</a>
    <div class="tooltip">
    <a style="float: right;" href="logout.php"><img style="height: 25px;" src="images/logoutIcon.png"></a>
    <span class="tooltiptext">Logout</span>
    </div>
    <div class="tooltip">
    <a style="float: right;" href="logOrProf.php"><img style="height: 25px;" src="images/profileIcon.png"></a>
    <span class="tooltiptext">Profile</span>
    </div>
    <div class="tooltip">
    <a style="float: right;" href="bookmark.php"><img style="height: 25px;" src="images/bookmark.png"></a>
    <span class="tooltiptext">Bookmark</span>
    </div>
    <div class="tooltip">
    <a style="float: right;" href="add_article.php"><img style="height: 25px;" src="images/plussign.png"></a>
    <span class="tooltiptext">Add Article</span>
    </div>
    <div class="tooltip">
    <span class="tooltiptext">Notification</span>
    <a style="float: right;">
    <div class="notBtn" href="#" onclick="seeNotif()">
            <div class="number" > ' . $total_count . ' </div>
            <i style="font-size:24px;height: 25px;" id="showdialog" class="fa fatest">&#xf0f3;</i>
        <div class="box" id="dialog" id="box" style="display:none">
                <div class="display">
                <div class="cont">
                    <!-- Fold this div and try deleting evrything inbetween -->
                    <div class="sec test">
                            <div class="txt"></div>
                    </div>
            </div> 
            </div>
        </div>
    </div>
    </a>
    </div>
</div>

    ';
  } else {
    echo '<div class="navbar">
    <a href="index.php"><img style="height: 30px;" src="images/Logo.png"></a>
    <a style="margin-top: 6px;" href="research.php">RESEARCH</a>
    <a style="margin-top: 6px;" href="analytics.php">ANALYTICS</a>
    <a class="ol-login-link" href="logOrProf.php"><span class="icons_base_sprite icon-open-layer-login"><strong style="margin-left:30px">Log in through your library</strong> <span>to access more features.</span></span></a>
    <a style="float: right;" href="logOrProf.php"><img style="height: 25px;" src="images/profileIcon.png"></a>
    </div>';
  }
  ?>

  <!-- BANNER IMAGE -->
  <br>
  <div id="index">
    <div class="slideshow-container">

      <div class="mySlides fade">
        <img src="images/Ban1.png" style="width:100%; height: 430px; ">
      </div>

      <div class="mySlides fade">
        <img src="images/Ban2.png" style="width:100%; height: 430px; ">
      </div>

      <div class="mySlides fade">
        <img src="images/Ban3.png" style="width:100%; height: 430px;">
      </div>

      <div class="mySlides fade">
        <img src="images/Ban1.png" style="width:100%; height: 430px; ">
      </div>


      <div class="mySlides fade">
        <img src="images/Ban2.png" style="width:100%; height: 430px; ">
      </div>

    </div>
    <br>


    <div style="text-align:center">
      <span style="display: none;" class="dot"></span>
      <span style="display: none;" class="dot"></span>
      <span style="display: none;" class="dot"></span>
      <span style="display: none;" class="dot"></span>
      <span style="display: none;" class="dot"></span>
    </div>
    <script>
      var slideIndex = 0;
      showSlides();

      function showSlides() {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");
        for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
          slideIndex = 1
        }
        for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
        setTimeout(showSlides, 3000); // Change image every 3 seconds
      }
    </script>

    <!-- SEARCH BAR CONTAINER -->
    <form name="frmSearch" method="post" action="research.php">
      <div class="container">
        <div class="row height d-flex justify-content-center align-items-center">
          <div>
            <div class="form">
              <div class="input-icons">

                <i style="cursor: pointer;" onclick="record()" class="fa fa-microphone"></i>
                <input type="text" id="speechToText" class="form-control form-input" name="search" placeholder="Search ThesisQuo" value="<?php if (isset($_POST["search"])) {
                                                                                                                                          }  ?>"> <button class="button" name="go">Search</button>
              </div>

            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- INTRODUCTION -->
    <h2 class="new">Read, Study and Develop With ThesisQuo!</h2>
    <p class="intro">Browse thesis studies that may help you in creating your own research, study and develop your research with ThesisQuo and share it to the community. ThesisQuo provides Local studies from different institutions in the Philippines to help every researchers developing their study.

    </p>

    <!-- 3 IMAGES -->
    <div class="images">
      <form action="research.php" method="POST">
        <div class="column">
          <img class="book" src="images/book.jpg">
          <button class="btn" type="submit" name="Education">Education</button>
        </div>
        <div class="column">
          <img class="chip" src="images/chip.jpg">
          <button class="btn2" type="submit" name="Technology">Technology</button>
        </div>
        <div class="column">
          <img class="business" src="images/business.jpg">
          <button class="btn3" type="submit" name="Business">Business</button>
        </div>
      </form>
    </div>

    <!-- ChatBot -->
    <div class="chat_icon">
      <img style="height: 80px;" src="images/chatboticon.PNG">
    </div>

    <div class="chat_box">
      <div class="my-conv-form-wrapper">
        <br><br>
        <div class="div-questions">
          <button class="questions" style="display:block" onclick="questionType(1)">How to Upload Study?</button>
          <button class="questions" style="display:block" onclick="questionType(2)">What study would you recommend for me to read?</button>
          <button class="questions" style="display:block" onclick="questionType(3)">What study topic can i develop?</button>
        </div>
        <div class="answer1" id="answer1" style="display:none">You must have an account before you upload your papers, if you are already a member, you may follow these steps:
          <br><br>
          1. Click the add (+) button on the navigation bar to upload your papers
          <br>
          2. Fill out the fields required by the admin to upload paper.
          <br>
          3. Read and Accept the Privacy Policy & Terms and Condition before submitting the paper.
          <br>
          4. Wait for the Plagiarism result if accepted or not.
          <br>
          5. If the paper passed the Plagiarism test, the paper will be upload. if not, the User must revise and re-upload the paper.
          <br><br><br>
        </div>
        <button class="answer1" id="reset" style="display:none" onclick="reset()">Reset</button>
        <select class="answer2 custom-select" style="display:none" name="topic" id="topic" required>
          <option value="" selected disabled hidden>Select topic type</option>
          <option value="Education">Education</option>
          <option value="Technology">Technology</option>
          <option value="Research">Research</option>
          <option value="Analysis">Analysis</option>
          <option value="Database">Database</option>
          <option value="Agriculture">Agriculture</option>
          <option value="Health">Health</option>
          <option value="Politics">Politics</option>
          <option value="Psychology">Psychology</option>
          <option value="Business">Business</option>
          <option value="Marketing and Advertising">Marketing and Advertising</option>
          <option value="Mechanical">Mechanical</option>
          <option value="Ethics">Ethics</option>
          <option value="Others">Others</option>
        </select>

        <button class="answer2 select" style="display:none" onclick="selectedTopic()">Select</button>
        <div class="analyticsResult" style="display:none">I Recommend these studies:
        </div>
        <div class="questionbutton">
          <button class="analyticsResult" id="analyticsResultbutton" style="display:none" onclick="analyticsQuestionType(1)">Do you want another question suggestion from other topics?</button>
          <button class="analyticsResult" id="analyticsResultbutton2" style="display:none" onclick="analyticsQuestionType(2)">Do you have any specific question for me?</button>
        </div>
        <button class="analyticsAnswer1" id="no" style="display:none" onclick="analyticsAnswerType('no')">No</button>
        <button class="analyticsAnswer1" id="yes" style="display:none" onclick="analyticsAnswerType('yes')">Yes</button>
        <div class="analyticsAnswer2" style="display:none">Send your Question to this email thesisquo.helpdesk@gmail.com</div>
        <button class="analyticsAnswer2" id="reset" style="display:none" onclick="reset()">Reset</button>
        <div class="answer3" id="answer3" style="display:none">What do you want to develop?</div>
        <button class="answer3" id="opt" style="display:none" onclick="developmentType(1)">Uniqie Study</button>
        <button class="answer3" id="opt" style="display:none" onclick="developmentType(2)">More Resources Available</button>
        <div class="development1" style="display:none">Show overall Lowest number of uploaded topic</div>
        <div class="development2" style="display:none">Show overall Highest number of uploaded topic</div>
        <div class="development" id="specific" style="display:none">Do you have any specific question for me?</div>
        <button class="development" style="display:none" onclick="developmentAnswerType('no')">No</button>
        <button class="development" style="display:none" onclick="developmentAnswerType('yes')">Yes</button>
        <div class="developmentQuestions" id="question2" style="display:none">Send your Question to this email thesisquo.helpdesk@gmail.com</div>
        <button class="developmentQuestions" id="developmentQuestions" style="display:none" onclick="reset()">Ok</button>
      </div>
    </div>
    <!-- ChatBot end -->

</body>
<!-- Below is the script for voice recognition and conversion to text-->
<script>
  function record() {
    var recognition = new webkitSpeechRecognition();
    recognition.lang = "en-GB";

    recognition.onresult = function(event) {
      // console.log(event);
      document.getElementById('speechToText').value = event.results[0][0].transcript;
    }
    recognition.start();
  }
</script>
<!-- Below is the script for mobile side navigation-->

<script>
  function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
  }

  function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
  }
</script>

</html>