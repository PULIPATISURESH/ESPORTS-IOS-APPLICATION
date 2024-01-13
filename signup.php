<?php
// Include your database connection code here (e.g., db_conn.php)
require_once('db.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize variables
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $mobilenumber = isset($_POST['mobilenumber']) ? $_POST['mobilenumber'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Check if any of the required fields are empty
    if (empty($username) || empty($mobilenumber) || empty($email) || empty($password)) {
        $response = [
            'status' => 'error',
            'message' => 'Please provide all required fields: username, mobilenumber, email, password.'
        ];
        echo json_encode($response);
    } else {
        // Check if the user_id already exists in transporter_signup
        $check_sql = "SELECT email FROM sign WHERE email = '$email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            // User already exists
            $response = [
                'status' => 'error',
                'message' => 'User already exists.'
            ];
            echo json_encode($response);
        } else {
            $sql = "INSERT INTO sign (username, mobilenumber, email, password) VALUES ('$username', '$mobilenumber', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                // Successful insertion
                $response = [
                    'status' => 'success',
                    'message' => 'User registration successful.'
                ];
                echo json_encode($response);
            } else {
                // Error in database insertion
                $response = [
                    'status' => 'error',
                    'message' => 'Error: ' . $conn->error
                ];
                echo json_encode($response);
            }
        }
    }
} else {
    // Handle non-POST requests (e.g., return an error response)
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method.'
    ];
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
