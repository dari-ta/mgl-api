<?php
    /**
     * unofficial MGL App API - Get File
     * ===================================
     * @version 1.3revDR
     * Original by dari-ta
     * Modified and extended by mawalu
     * Reengineered by dari-ta
     */


    define('DB_CONFIG_FILE_NAME', 'db.inc.php');
    define('VPL_TABLE', 'vpl');

    global $db;


    include(DB_CONFIG_FILE_NAME); 





    $_classes = isset($_POST['classes']) ? $_POST['classes'] : '';
    // $_classes = isset($_GET['classes']) ? $_GET['classes'] : ''; // Uncomment if you want to use GET


    if($_classes == ''){
        echo "NO VALID CLASS";
        exit;
    }

    $__classes = explode(',', $_classes);

    echo "{\n";
    foreach($__classes as $_class){
        $sql = "SELECT `class`,`std`,`subj`,`room`,`froom`,`fsubj`,`comm`,`hash`,`date`,`act` FROM " . VPL_TABLE . " WHERE `class` = :class";
        $query = $db -> prepare($sql);
        $query -> execute(array(':class' => $_class));
        echo "  \"$_class\"{\n";
        while($row = $query -> fetch(PDO::FETCH_ASSOC)){
            echo <<<END
                "{$row['hash']}": {
                    "class": "{$row['class']}",
                    "std": "{$row['std']}",
                    "subject" : "{$row['subj']}",
                    "room": "{$row['room']}",
                    "from" : "{$row['froom']}",
                    "old_subject": "{$row['fsubj']}",
                    "comment": "{$row['comm']}",
                    "date": "{$row['date']}",
                    "action": "{$row['act']}"
                },\n
END;
        }
        echo "                \"\":{}\n";
        echo "  },";
    }
    echo "  \"\":{}\n";
    echo "}\n";

?>