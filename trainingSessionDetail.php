<?php

/**
 * Created by PhpStorm.
 * User: driesc
 * Date: 19/11/2017
 * Time: 21:39
 */
require_once "checksession.php";
require_once "Service.php";
require_once "templates/head.php";

//if sign in button pushed => compile data and post to dataservice
//TODO: change to function like in trainingsessiondetail?
if (isset($_POST["trainingSessionId"]) && isset($_SESSION["userId"]) && isset($_POST["signin"])) {
    $curl_post_data = [
        "userid" => $_SESSION["userId"],
        "trainingsessionid" => $_POST["trainingSessionId"],
        "isApproved" => false,
        "isCancelled" => false
    ];

    if (Service::get("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$_POST["trainingSessionId"]}")) {
        Service::put("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$_POST["trainingSessionId"]}", $curl_post_data);
    } else {
        Service::post("followingtrainings", $curl_post_data);
    }

}

if (isset($_POST["trainingSessionId"]) && isset($_SESSION["userId"]) && isset($_POST["cancelsignin"])) {
    $curl_put_data = [
        "userid" => $_SESSION["userId"],
        "trainingsessionid" => $_POST["trainingSessionId"],
        "isApproved" => false,
        "isCancelled" => true
    ];
    //TODO: url gaat combo zijn: nog te testen
    Service::put("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$_POST["trainingSessionId"]}", $curl_put_data);
}

if (isset($_POST["trainingSessionId"]) && isset($_SESSION["userId"]) && isset($_POST["signout"])) {
    $curl_put_data = [
        "userid" => $_SESSION["userId"],
        "trainingsessionid" => $_POST["trainingSessionId"],
        "isApproved" => true,
        "isCancelled" => true
    ];
    //TODO: url gaat combo zijn: nog te testen
    Service::put("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$_POST["trainingSessionId"]}", $curl_put_data);
}

if (isset($_POST["trainingSessionId"]) && isset($_SESSION["userId"]) && isset($_POST["cancelsignout"])) {
    $curl_put_data = [
        "userid" => $_SESSION["userId"],
        "trainingsessionid" => $_POST["trainingSessionId"],
        "isApproved" => true,
        "isCancelled" => false
    ];
    //TODO: url gaat combo zijn: nog te testen
    Service::put("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$_POST["trainingSessionId"]}", $curl_put_data);
}

// checkforid(int id, array to look in): returns false if no match found
function checkForId($TSId, $array) {
    $t = 0;
    foreach ($array as $key => $value) {
        if ($TSId == $value["trainingSessionId"]) {
            $t++;
        }
    }

    if ($t == 0) {
        return false;
    } else {
        return true;
    }
}

if ((isset($_GET["trainingSessionId"]) || isset($_POST["trainingSessionId"])) && isset($_SESSION["userId"])) {
    if (isset($_GET["trainingSessionId"])) {
        $TSId = $_GET["trainingSessionId"];
    } else if (isset($_POST["trainingSessionId"])) {
        $TSId = $_POST["trainingSessionId"];
    }
    // get all data to show
    //TODO: get followingtraining object for user in $status if exists. API still under construction so probably doesnt work yet
    $status = Service::get("followingtrainings?userid={$_SESSION["userId"]}&trainingsessionid={$TSId}");
    $_SESSION["userTrainingSessions"] = Service::get("users/{$_SESSION["userId"]}/trainingsessions");
    $TS = Service::get("trainingsessions/{$TSId}?loadrelated=true");
} else {
    ?>
        <mark>Something went wrong!</mark>
    <?php
}

?>
<body>
<?php require_once 'templates/navigation.php';?>
<div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- TODO: back to where you came from (breadcrumb variable)-->
                <a class="btn btn-primary" role="button" href="<?= $_GET["breadcrumb"] ?>"> <i class="icon ion-android-arrow-back"></i> BACK</a>
                <?php
                if($TS["cancelled"] == true) {

                } else if(!checkForId($TS["trainingSessionId"], $_SESSION["userTrainingSessions"]) || ($status["isCancelled"] == "true" && $status["isApproved"] =="false")) { // not in followingtrainings -> sign in button -> in followingstrainings aproved & isCancelled on false
                    ?>
                    <form method="POST">
                        <input type="hidden" name="trainingSessionId" value="<?= $TS["trainingSessionId"] ?>"/>
                        <input type="hidden" name="signin" value="true"/>
                        <button class="btn btn-primary float-right" name="sign in">
                            Sign in
                        </button>
                    </form>
                    <?php
                } else if ($status["isCancelled"] == "false" && $status["isApproved"] == "false") { // approved & isCancelled false (manager must approve) -> cancel button -> isCancelled on true
                    ?>
                    <form method="POST">
                        <input type="hidden" name="trainingSessionId" value="<?= $TS["trainingSessionId"] ?>"/>
                        <input type="hidden" name="cancelsignin" value="true"/>
                        <button class="btn btn-primary float-right" name="sign in">
                            (awaiting confirmation) Cancel sign in
                        </button>
                    </form>
                    <?php
                } else if ($status["isCancelled"] == "false" && $status["isApproved"] == "true") { // approved = true, cancelled = false --> manager approved --> sign out button --> isCancelled = true
                    ?>
                    <form method="POST">
                        <input type="hidden" name="trainingSessionId" value="<?= $TS["trainingSessionId"] ?>"/>
                        <input type="hidden" name="signout" value="true"/>
                        <button class="btn btn-primary float-right" name="sign out">
                            sign out
                        </button>
                    </form>
                    <?php
                } else if ($status["isCancelled"] == "true" && $status["isApproved"] == "true") { // approved = true & isCancelled true -> signed out
                    ?>
                    <form method="POST">
                        <input type="hidden" name="trainingSessionId" value="<?= $TS["trainingSessionId"] ?>"/>
                        <input type="hidden" name="cancelsignout" value="true"/>
                        <button class="btn btn-primary float-right" name="signed out" disabled>
                            (awaiting confirmation) Cancel signed out
                        </button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Training Info
                </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="trainingsinfoborder">
                    <h5>
                        <?php
                        echo $TS["training"]["name"];
                        if ($TS["cancelled"] == 1) {
                            ?>
                            <mark> Cancelled!</mark>
                            <?php
                        }
                        ?>
                    </h5>
                    <p>
                        <?= $TS["training"]["infoGeneral"];?>
                    </p>
                    <h5>Date - Hour</h5>
                    <p>
                        <?php
                        $date = new DateTime($TS["date"]);
                        echo $date->format('d M Y');
                        ?> -
                        <?php
                        $start = new DateTime($TS["startHour"]);
                        $end = new DateTime($TS["endHour"]);
                        echo $start->format('H:i') . '-' . $end->format('H:i');
                        ?>
                    </p>
                    <h5>
                        Price:
                    </h5>
                    <p>
                        <?= $TS["training"]["price"];?>
                    </p>
                    <h5>
                        Payment:
                    </h5>
                    <p>
                        <?= $TS["training"]["infoPayment"];?>
                    </p>
                    <h5>
                        Exam:
                    </h5>
                    <p>
                        <?= $TS["training"]["infoExam"];?>
                    </p>
                    <h5>
                        Teacher:
                    </h5>
                    <p>
                        <?= $TS["teacher"]["firstName"] . $TS["teacher"]["lastName"] . $TS["teacher"]["phoneNumber"] . $TS["teacher"]["email"];?>
                    </p>
                    <h5>
                        Location:
                    </h5>
                    <p>
                        <?= $TS["address"]["locality"] . $TS["address"]["streetAddress"];?>
                    </p>
                    <div class="col">
                        <iframe
                                width="600"
                                height="450"
                                frameborder="0" style="border:0"
                                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyC_MyMlEb59Fb8IBPy_0hHm4FGP4r8nYxo&amp;q=<?= urlencode($TS["address"]["country"]) . "+" . urlencode($TS["address"]["locality"]) . "+" . urlencode($TS["address"]["streetaddress"]);
                                ?>">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-primary" role="button" href="#"> <i class="icon ion-android-arrow-back"></i> BACK</a>
            </div>
        </div>
    </div>
</div>
</body>
