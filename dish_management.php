<?php
    //Session_Start for later user
    session_start();
    $Username_From_Session = $_SESSION['username'];
    $User_Role = $_SESSION['is_staff'];
    // If user not logged in, redirect to index
    if ($Username_From_Session == "") {
        header('Location: index.html');
        exit();
    }
    if($User_Role == "0"){
        header('Location: index.html');
        exit();
    }
    //Request database connection data from config file
    require_once 'database_config.php';
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    // Function to sanitize user input
    function sanitize($input) {
        return htmlspecialchars(strip_tags($input));
    }
    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Handle add dish
        if (isset($_POST['add'])) {
            $dishName = sanitize($_POST['dish_name']);
            $price = sanitize($_POST['price']);
            $quantity = sanitize($_POST['quantity']);
            $sql = "INSERT INTO menu (dishname,dishprice, dishquantity) VALUES ('$dishName', '$price', '$quantity')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Add Successfully');</script>";
            }
        }
        //Handle delete dish
        if (isset($_POST['delete'])) {
            $dishNameDel = sanitize($_POST['dishname']);
            $sql = "DELETE FROM menu WHERE dishname='$dishNameDel'";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Deleted Successfully');</script>";
            }
        }
    }
    // Get dish information from the database
    $sql = "SELECT * FROM menu";
    $result = $conn->query($sql);
?>

<html>
    <head>
        <title>Dish Management</title>
    </head>
    <style>
        body{
            margin: 0;
            text-align: center;
        }
        .center{
            width: 800px;
            margin: 0 auto;
        }
    </style>
    <body>
        <div class="center">
            <h3>Add Dish</h3>
            <form method="post" action="">
                Dish Name: <input type="text" name="dish_name" required><br>
                Price: <input type="text" name="price" required><br>
                Quantity: <input type="text" name="quantity" required><br>
                <input type="submit" name="add" value="Add Dish">
            </form>
            <h3>Delete Dish</h3>
            <table>
                <tr>
                    <th width="200">Dish Name</th>
                    <th width="200">Price</th>
                    <th width="200">Quantity</th>
                    <th width="200">Actions</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['dishname'] . "</td>";
                        echo "<td>" . $row['dishprice'] . "</td>";
                        echo "<td>" . $row['dishquantity'] . "</td>";
                        echo "<td>
                    <form method='post' action=''>
                        <input type='hidden' name='dishname' value='" . $row['dishname'] . "'>
                        <button><a href='editdish.php?edit=". $row['dishname']."'>Edit</button>
                        <input type='submit' name='delete' value='Delete'>
                    </form>
                </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No dishes found</td></tr>";
                }
                ?>
            </table>
        </div>
        <a href="landing.php"><button>Back</button></a>
    </body>
</html>
