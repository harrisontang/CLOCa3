<?php
    session_start();
    include('tools.php');

    // if (isset($_SESSION['favePlayerStats'])) {
    //     unset($_SESSION['favePlayerStats']);
    // }

    function printFavouritesHeader() {
        $header = <<<"HEADER"
            <span id='query-span'>
                <h3>{$_SESSION['user']['email']}'s Favourites' Most Recent Statlines</h3>
            </span>
        HEADER;
        echo $header;
    }

    // function printFavouritesTable() {
    //     $favePlayers = $_SESSION['user']['favourites'];

    //     $tableHeaders = <<<"TABLE"
    //         <table>
    //         <thead>
    //             <tr>
    //                 <th hidden>Id</th>
    //                 <th>Jersey No.</th>
    //                 <th>First name</th>
    //                 <th>Last name</th>
    //                 <th>DoB</th>
    //                 <th>Height (m)</th>
    //                 <th>Weight (kg)</th>
    //                 <th>Remove from Faves</th>
    //             </tr>
    //         </thead>
    //         <tbody>
    //     TABLE;
    //     echo $tableHeaders;

    //     foreach ($players as $index => $player) {
    //         $tableRow = <<<"ROW"
    //             <form method='post'>
    //                 <tr>
    //                     <input name='playerId' value={$player['id']} hidden>
    //                     <input name='index' value={$index} hidden>
    //                     <td>{$player['leagues']['standard']['jersey']}</td>
    //                     <td>{$player['firstname']}</td>
    //                     <td>{$player['lastname']}</td>
    //                     <td>{$player['birth']['date']}</td>
    //                     <td>{$player['height']['meters']}</td>
    //                     <td>{$player['weight']['kilograms']}</td>
    //                     <td><button type='submit' name='add' value='true'>Add</button></td>
    //                 </tr>
    //             </form>
    //         ROW;
    //         echo $tableRow;
    //     }

    //     $tableBottom = <<<"TABLE"
    //             </tbody>
    //         </table>
    //     TABLE;
    //     echo $tableBottom;
    // }

    function printFavouritesTable() {
        $tableHeaders = <<<"TABLE"
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Team</th>
                    <th>Pos</th>
                    <th>Min</th>
                    <th>Points</th>
                    <th>Assists</th>
                    <th>Reb</th>
                    <th>FGM</th>
                    <th>FGA</th>
                    <th>FG%</th>
                    <th>FT</th>
                    <th>FT%</th>
                    <th>Fouls</th>
                    <th>Steals</th>
                    <th>Blocks</th>
                    <th>Turnovers</th>
                    <th>+/-</th>
                    <th>Remove from faves</th>
                </tr>
            </thead>
        <tbody>
    TABLE;
        echo $tableHeaders;

        if (isset($_SESSION['tableData'])) {
            $tableData = $_SESSION['tableData'];
            // error_log(print_r($tableData, true));

            foreach ($tableData as $index => $player) {
                $game = $player[(count($player) - 1)];
                // error_log("game data: " . print_r($game, true));

                $tableRow = <<<"ROW"
                    <form method='post'>
                        <tr>
                            <input name='playerId' value={$game['player']['id']} hidden>
                            <input name='index' value={$index} hidden>
                            <td>{$game['player']['firstname']}</td>
                            <td>{$game['player']['lastname']}</td>
                            <td class='table-logo'style='background-image: url({$game['team']['logo']})'></td>
                            <td>{$game['pos']}</td>
                            <td>{$game['min']}</td>
                            <td>{$game['points']}</td>
                            <td>{$game['assists']}</td>
                            <td>{$game['totReb']}</td>
                            <td>{$game['fgm']}</td>
                            <td>{$game['fga']}</td>
                            <td>{$game['fgp']}</td>
                            <td>{$game['ftm']}/{$game['fta']}</td>
                            <td>{$game['ftp']}</td>
                            <td>{$game['pFouls']}</td>
                            <td>{$game['steals']}</td>
                            <td>{$game['blocks']}</td>
                            <td>{$game['turnovers']}</td>
                            <td>{$game['plusMinus']}</td>
                            <td><button type='submit' name='remove' value='true'>Remove</button></td>
                        </tr>
                    </form>
                ROW;
                echo $tableRow;
            }
        } else {
            echo "<span id='query-message'><p>There are no favourites currently.</p></span>";
        }

        // tabledata[player][game]
        
       

        $tableBottom = <<<"TABLE"
                </tbody>
            </table>
        TABLE;
        echo $tableBottom;
    }

    function getFavePlayerStats($player) {
        $qsCategory = "/players";
        $qsStats = "/statistics";
        $qsQues = "?";

        list($type, $Id) = explode("-", $player);
        $qsId = "id={$Id}";        
        $qsAmp = "&";
        $qsSeasom = "season=2022";

        $qs = $qsCategory . $qsStats . $qsQues . $qsId . $qsAmp . $qsSeasom;
        error_log($qs);

        $playerWithStats = nbaApiQueryExecute($qs);

        return $playerWithStats;
    }

    

?>

<!DOCTYPE html>
<html>
<head>
    <title>Favourites</title>
    <link rel="stylesheet" type="text/css" href="assets/styles/styles.css">
</head>
<body>

    <?php 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['tableData'])) {
                unset($_SESSION['tableData']);
            }
            $_SESSION['remove'] = $_POST;

            $email = $_SESSION['user']['email'];
            $password = $_SESSION['user']['password'];
            $index = $_SESSION['remove']['index'];
            $playerId = $_SESSION['remove']['playerId'];

            $result = favesHandler($email, $playerId, $index, 'del', 'player');
            // $result = favesHandler("admin@admin.com", $playerId, 2, 'del', 'player');

            $_SESSION['user'] = cleanResultGetUser(userHandler($email, $password, 'get'));
            error_log("NEW USER DETAILS RETRIEVED AT FAVOURITES");
            error_log(print_r($_SESSION['user'], true));
        } 
        
        if (isset($_SESSION['user']['favourites'])) {
    
            // for when the user arrives at the page for the first time 
            // (session does not have playerstats loaded)
            $tableData = [];
            for ($i = 0; $i < count($_SESSION['user']['favourites']); $i++) {
                array_push($tableData, getFavePlayerStats($_SESSION['user']['favourites'][$i]));
            }
            
            // table data loaded into session
            $_SESSION['tableData'] = $tableData;
        }

        // $_SESSION['user']['favourites']['0'] = "players-2808";

        navBarModule(); 
        printFavouritesHeader();
        printFavouritesTable();

        // $player = getFavePlayerStats("players-2808");
        // error_log(print_r($player, true));
    ?>



</body>
</html>

<!-- debug area -->
<!-- <div style="width: 80%; margin: 0 auto; background-color: lightgray; padding: 20px;">
<?php
    // debugModule();
?>
</div> -->