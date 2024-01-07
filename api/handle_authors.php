<?php
// Include your database connection and db class
require_once 'db.php';

// Create an instance of the db class
$db = new \api\db();

// Check if the request is a POST or DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adding an author
    $postData = json_decode(file_get_contents("php://input"));
    if (isset($postData->authorName)) {
        $db->addAuthor($postData->authorName);
        echo json_encode(array('success' => 'Author added successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Missing author name for adding an author'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deleting an author
    $postData = json_decode(file_get_contents("php://input"));
    if (isset($postData->authorId)) {
        $db->deleteAuthor($postData->authorId);
        echo json_encode(array('success' => 'Author deleted successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Missing author ID for deleting an author'));
    }
} else {
    // If the request method is not POST or DELETE, return an error response
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}
?>
