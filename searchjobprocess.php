<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development" />
    <meta name="keywords" content="PHP" />
    <meta name="author" content="Nguyen The Vinh" />
    <link rel="stylesheet" href="style.css" />
    <title>Assignment 1</title>
</head>

<body>
    <nav>
        <ul>
            <li
                class="<?php echo basename(htmlspecialchars($_SERVER["PHP_SELF"])) == "index.php" ? "liactive" : ""; ?>">
                <a href="index.php">Home</a>
            </li>
            <li
                class="<?php echo basename(htmlspecialchars($_SERVER["PHP_SELF"])) == "postjobform.php" ? "liactive" : ""; ?>">
                <a href="postjobform.php">Post a job</a>
            </li>
            <li
                class="<?php echo basename(htmlspecialchars($_SERVER["PHP_SELF"])) == "searchjobform.php" ? "liactive" : ""; ?>">
                <a href="searchjobform.php">Look for a job</a>
            </li>
            <li
                class="<?php echo basename(htmlspecialchars($_SERVER["PHP_SELF"])) == "about.php" ? "liactive" : ""; ?>">
                <a href="about.php">About this</a>
            </li>
        </ul>
    </nav>
    <div class="center container">
        <h1>Job Vacancy Information</h1>
        <?php
        function check_empty(&$string)
        {
            $string = trim($string);
            if (!empty($string))
                return false;
            return true;
        }
        function validate_title($i)
        {
            if (check_empty($i))
                return "Title cannot be empty";
            if (strlen($i) > 20)
                return "Title must be 20 characters or less in length";
            if (preg_match("/[^\d\w,.! ]/", $i) == 1)
                return "Title can only contain alphanumeric characters including
                            spaces, comma, period";
            return "";
        }

        $title = $_GET["title"];
        $filters = $_GET["filter"];
        $valid = validate_title($title);
        if ($valid != "") {
            echo "<p>$valid<p>";
            echo "
            <div class=\"redirect-link center\">
                <p><a href=\"searchjobform.php\">Search for another job vacancy</a></p>
                <p><a href=\"index.php\">Return to Home Page</a></p>
            </div>";
            return;
        }
        $filename = "../../data/jobposts/jobs.txt";
        if (!file_exists($filename)) {
            echo "<p>Jobs text file does not exist</p>";
            echo "
            <div class=\"redirect-link center\">
                <p><a href=\"searchjobform.php\">Search for another job vacancy</a></p>
                <p><a href=\"index.php\">Return to Home Page</a></p>
            </div>";
            return;
        }
        $title = strtolower($title);
        $matches = array();
        $joblist = array();
        $handle = fopen($filename, "r");
        while (!feof($handle)) {
            $data = fgets($handle);
            $data = trim($data);
            if (empty($data))
                continue;
            $data = explode("\t", $data);
            $joblist[] = $data;
        }
        foreach ($joblist as $jobdata) {
            $match = true;
            $jobtitle = strtolower($jobdata[1]);
            if (preg_match("/$title/", $jobtitle)) {
                foreach ($filters as $index => $filter) {
                    if (!preg_match("/$filter/", $jobdata[$index])) {
                        $match = false;
                        break;
                    }
                }
                if (!$match) {
                    continue;
                }
                if (date_create_from_format("d/m/y", $jobdata[3])->getTimestamp() <= time()) continue;
                $matches[] = $jobdata;
            }
        }
        fclose($handle);
        if (empty($matches)) {
            echo "<p>No matching job vacancy</p>";
            echo "
            <div class=\"redirect-link center\">
                <p><a href=\"searchjobform.php\">Search for another job vacancy</a></p>
                <p><a href=\"index.php\">Return to Home Page</a></p>
            </div>";
            return;
        }

        usort($matches, function ($a, $b) {
            $da = date_create_from_format("d/m/y", $a[3]);
            $db = date_create_from_format("d/m/y", $b[3]);
            return $da < $db;
        });

        foreach ($matches as $i=>$job) {
            $i = $i+1;
            echo "<h3>$i</h3>";
            echo "<p>Title: $job[1]</p>";
            echo "<p>Description: $job[2]</p>";
            echo "<p>Closing Date: $job[3]</p>";
            echo "<p>Position: $job[5] - $job[4]</p>";
            $post = $job[6];
            $mail = $job[7];
            echo "<p>Application by: ";
            if ($post && $mail != "") {
                echo "Post and Email</p>";
            } else {
                echo $post != "" ? "Post" : "Email" . "</p>";
            }
            echo "<p>Location: $job[8]</p>";
        }
        ?>
        <div class="redirect-link center">
            <p><a href="searchjobform.php">Search for another job vacancy</a></p>
            <p><a href="index.php">Return to Home Page</a></p>
        </div>
    </div>
</body>

</html>