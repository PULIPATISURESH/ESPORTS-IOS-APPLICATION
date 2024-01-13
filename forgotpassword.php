<?php
// Include necessary files and set up your database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "sport";

// Define an empty response array
$response = [];

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are set in the $_POST array
    if (isset($_POST["username"], $_POST["email"], $_POST["newpassword"], $_POST["confirmpassword"])) {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $newpassword = $_POST["newpassword"];
        $confirmpassword = $_POST["confirmpassword"];

        // Check if any of the required fields are empty
        if (empty($username) || empty($email) || empty($newpassword) || empty($confirmpassword)) {
            $response['status'] = false;
            $response['message'] = 'Please provide all required fields: username, email, newpassword, confirmpassword';
        } elseif ($newpassword !== $confirmpassword) {
            // Check if new password and confirm password do not match
            $response['status'] = false;
            $response['message'] = 'New password and confirm password do not match';
        } else {
            // Create connection
            $conn = new mysqli($servername, $db_username, $db_password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                $response['status'] = false;
                $response['message'] = "Connection failed: " . $conn->connect_error;
            } else {
                // Prepare and bind a query to select the email based on the provided username
                $select_sql = "SELECT email FROM sign WHERE username = ?";
                $stmt_select = $conn->prepare($select_sql);
                $stmt_select->bind_param("s", $username);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();

                if ($result_select->num_rows === 0) {
                    // Provided username does not exist in the database
                    $response['status'] = false;
                    $response['message'] = 'Invalid username';
                } else {
                    // Fetch the email associated with the provided username
                    $row = $result_select->fetch_assoc();
                    $selectedEmail = $row['email'];

                    // Check if the selected email matches the provided email
                    if ($selectedEmail !== $email) {
                        $response['status'] = false;
                        $response['message'] = 'Username and email do not match';
                    } else {
                        // Prepare and bind the update query to update the password
                        $stmt = $conn->prepare("UPDATE sign SET password=? WHERE username=?");
                        $stmt->bind_param("ss", $newpassword, $username);

                        // Execute the query
                        if ($stmt->execute()) {
                            $response['status'] = true;
                            $response['message'] = 'Your password has been successfully changed';
                        } else {
                            $response['status'] = false;
                            $response['message'] = 'Error updating password: ' . $conn->error;
                        }

                        // Close the statement
                        $stmt->close();
                    }
                }

                // Close the statement for retrieving the email based on the username
                $stmt_select->close();
                // Close the database connection
                $conn->close();
            }
        }
    } else {
        $response['status'] = false;
        $response['message'] = 'Please provide all required fields: username, email, newpassword, confirmpassword';
    }
} else {
    $response['status'] = false;
    $response['message'] = 'Invalid request method';
}

// Set the appropriate HTTP headers for an API response
header('Content-Type: application/json');
http_response_code($response['status'] ? 200 : 400);

// Return the response as JSON
echo json_encode($response);
?>
