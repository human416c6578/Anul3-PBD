<?php
// Include your database connection and db class
require_once 'db.php';

// Create an instance of the db class
$db = new \api\db();

// Check if the request is a POST or DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adding a book
    $postData = json_decode(file_get_contents("php://input"));
    if (isset($postData->title) && isset($postData->authorId) && isset($postData->stock)) {
        $db->addBook($postData->title, $postData->authorId, $postData->stock);
        echo json_encode(array('success' => 'Book added successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Missing data for adding a book'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deleting a book
    $postData = json_decode(file_get_contents("php://input"));

    if (isset($postData->bookId)) {
        $db->deleteBook($postData->bookId);
        echo json_encode(array('success' => 'Book deleted successfully'));
    } else {
        http_response_code(400);
        echo json_encode(array('error' => 'Missing book ID for deleting a book'));
    }
} else {
    // If the request method is not POST or DELETE, return an error response
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}
