<?php
    include('tools.php');
    session_start();
    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     $email = $_SESSION['user']['email'];
    //     $playerId = $_GET['playerId'];

    //     favesHandler($email, $playerId, 'update', 'player');
    // }


    // for favourites page
    // function delFave() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $email = $_SESSION['user']['email'];
    //         $playerId = $_GET['playerId'];


    
    //         favesHandler($email, $playerId, 'del', 'player');

    //     }
    // }
   
    function printQueryHeader() {
        foreach ($_SESSION['nbaTeams'] as $team) {
            if ($_SESSION['query']['team'] == $team['id']) {
                $header = <<<"HEADER"
                    <span id='query-span'>
                        <h3>
                            <img src={$team['logo']}>
                            Players from {$team['name']}
                        </h3>
                    </span>
                HEADER;
                echo $header;
            }
        }
    }

    function printResultsTable() {
        $players = $_SESSION['players'];

        $tableHeaders = <<<"TABLE"
            <table>
            <thead>
                <tr>
                    <th hidden>Id</th>
                    <th>Jersey No.</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>DoB</th>
                    <th>Height (m)</th>
                    <th>Weight (kg)</th>
                    <th>Add to Faves</th>
                </tr>
            </thead>
            <tbody>
        TABLE;
        echo $tableHeaders;

        foreach ($players as $index => $player) {
            $tableRow = <<<"ROW"
                <form method='post'>
                    <tr>
                        <input name='playerId' value={$player['id']} hidden>
                        <input name='index' value={$index} hidden>
                        <td>{$player['leagues']['standard']['jersey']}</td>
                        <td>{$player['firstname']}</td>
                        <td>{$player['lastname']}</td>
                        <td>{$player['birth']['date']}</td>
                        <td>{$player['height']['meters']}</td>
                        <td>{$player['weight']['kilograms']}</td>
                        <td><button type='submit' name='add' value='true'>Add</button></td>
                    </tr>
                </form>
            ROW;
            echo $tableRow;
        }

        $tableBottom = <<<"TABLE"
                </tbody>
            </table>
        TABLE;
        echo $tableBottom;
    }

    function executeNewQuery() {
        $teamId = $_SESSION['query']['team'];
        $seasonId = $_SESSION['query']['season'];
        $qsAmp = "&";
        $qsQues = "?";
        
        $qsCategory = "/players";
        $qsTeam = "team={$teamId}";
        $qsSeason = "season={$seasonId}";

        $qs = $qsCategory . $qsQues . $qsTeam . $qsAmp . $qsSeason;     
        $players = nbaApiQueryExecute($qs);
        
        return $players;
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

    <form class="query-form" method="post" action="query.php">
        <label for="team">Team:</label>
        <select name="team" id="team">

            <?php
            // Generate options for team dropdown
            foreach ($_SESSION['nbaTeams'] as $team) {
                echo "<option value={$team['id']}>{$team['name']}</option>";
            }
            ?>

        </select>

        <label for="season">Season:</label>
        <select name="season" id="season">
            <option value="2022">2022</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <!-- Query area -->
    <?php
        // if form submitted post (dealing with query button)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("REQUEST METHOD = POST");

            // IF isset(post[teams]) => query button pressed
            if (isset($_POST['team'])) {
                error_log("POST[TEAM] SET");
                    
                // IF session[query] is not set (fresh query page) 
                // OR 
                // IF session[query][team] !== post[team] (previously stored query does not match new query):
                // => need new query, new set of players
                if (!isset($_SESSION['query']) || $_SESSION['query']['team'] !== $_POST['team']) {
                    error_log("INITIALIZING NEW QUERY");
                    
                    // first store post query output into session['query']
                    $_SESSION['query'] = $_POST;

                    // then execute query with session[query] values and store players in session[players]
                    $_SESSION['players'] = executeNewQuery();

                    // printQueryHeader();
                    // printResultsTable();
                }
            
            
            } else { // IF !isset(post[teams]) => add button pressed (session has user, teams, query, players)
                error_log(print_r($_POST, true));
                error_log("POST[ADD] SET");

                $_SESSION['add'] = $_POST;

                $email = $_SESSION['user']['email'];
                $password = $_SESSION['user']['password'];
                $playerId = $_SESSION['add']['playerId'];
                $index = $_SESSION['add']['index'];

                // Adds selection to favourites
                $result = favesHandler($email, $playerId, $index, 'update', 'player');

                if ($result['statusCode'] == 200) {
                    $message = "ID: {$playerId} added";

                    // Rotate new user details
                    $_SESSION['user'] = cleanResultGetUser(userHandler($email, $password, 'get'));
                    error_log("NEW USER DETAILS RETRIEVED AT DATABASE");
                    error_log(print_r($_SESSION['user'], true));          
                } else {
                    $message = "ID: {$playerId} could not be added";
                }
                echo "<span id='query-message'><p>{$message}</p></span>";
            }
            printQueryHeader();
            printResultsTable();
        }
    ?>   
    
</body>
</html>

<!-- debug area -->
<!-- <div style="width: 80%; margin: 0 auto; background-color: lightgray; padding: 20px;">
<?php
    // debugModule();
?>
</div> -->