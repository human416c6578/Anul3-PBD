<?php
// Include your database connection and db class
require_once 'db.php';

// Create an instance of the db class
$db = new \api\db();

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search criteria from the POST data
    $postData = json_decode(file_get_contents("php://input"));

    // Check if all required search criteria are provided
    if (isset($postData->title) && isset($postData->author)) {
        // Call the get_books function with the search criteria
        $books = $db->getBooks($postData->title, $postData->author);

        // Send the records as JSON response
        header('Content-Type: application/json');

        echo $books;
    } else {
        // If any required criteria are missing, return an error response
        http_response_code(400);
        echo json_encode(array('error' => 'Missing search criteria'));
    }
} else {
    // If the request is not a POST request, return an error response

    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}
?>
