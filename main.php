<?php
    include('tools.php');
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        changeImage();
    }
    getImage();

    // Object details:
    // $nbaTeams as $index => $team:
    // $team => {
    //  id
    //  name
    //  nickname
    //  code
    //  city
    //  logo
    //  allstar
    //  nbaFranchise
    //  leagues
    // }
    if (isset($_SESSION['nbaTeams'])) {
        $nbaTeams = $_SESSION['nbaTeams'];
    } else {
        $nbaTeams = getNbaTeams();
        $_SESSION['nbaTeams'] = $nbaTeams;
    }
       
    function getImage() {
        $email = $_SESSION['user']['email'];
        $resultBody = profileImageHandler($email, 'get');
        $presignedImageUrl = $resultBody['body'];

        $_SESSION['user']['profileImage'] = $presignedImageUrl;
    }

    /**
     * for when user changes profile image:
     * - stored image in s3 gets deleted
     * - then new image stored in $_SESSION['imagePath'] is uploaded to s3
     * - then method finishes -> invokes getImage() to get newly stored image
    */ 
    function changeImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['image'])) {
                $file = $_FILES['image'];
                $tempFilePath = $file['tmp_name'];
                $_SESSION['imagePath'] = $tempFilePath;
                
                // deleting stored image
                $email = $_SESSION['user']['email'];
                $resultBody = profileImageHandler($email, 'del');

                // if successful -> post new image
                if ($resultBody['statusCode'] == 200) {
                    $resultBody = profileImageHandler($email, 'post');
                }
            }
        }
    }

    function newsBoxModule() {
        $news = getNbaNews();

        echo "<ul class='article-list'>";
        for ($i = (count($news) - 5); $i < count($news); $i++) {
            $article = $news[$i];

            $listItem = <<<"ARTICLE"
                <li class='article-item'>
                    <h3 class='article-title'>
                        <a class='article-url' href={$article['url']} target='_blank'>
                            {$article['title']}
                        </a>
                    </h3>
                    <span class='article-source'>Source: {$article['source']}</span>
                </li>
            ARTICLE;
            echo $listItem;
        }
        echo "</ul>";       
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>NBA Database</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/styles.css">
</head>
<body>

    <?php navBarModule(); ?>

    <div id=main-page-content>

        <div class="profile">
            <div id="profile-content">
                <img src=<?php echo $_SESSION['user']['profileImage'] ?> alt="Profile Image">
                <div class="email">
                    <?php 
                        echo "Welcome " . $_SESSION['user']['email']; 
                    ?>
                </div>
                <form method="POST" action="main.php" enctype="multipart/form-data" class="edit-link" >
                    <input type="file" name="image" accept="image/jpeg" required>
                    <input type="submit" value="Change profile image">
                </form>
            </div>
        </div>

        <div id="news-box">
            <div id="news-box-content">
                <h3 id="news-box-title">NEWS</h3>
                
                <?php 
                    newsBoxModule();
                ?>

            </div>
        </div>
    </div>
</body>
</html>

<!-- debug area -->
<!-- <div style="width: 80%; margin: 0 auto; background-color: lightgray; padding: 20px;">
<?php
    // debugModule();
?>
</div> -->