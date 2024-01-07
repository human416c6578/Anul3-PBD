<?php

namespace api;

use mysqli;

require __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$database = $_ENV['DB_NAME'];

class db
{
    function getBooks($title, $author)
    {
        global $servername, $username, $password, $database;
        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Use prepared statements to prevent SQL injection
        $sql = "SELECT b.id AS BookId, b.Title as BookTitle, a.Name as BookAuthor, b.Stock as BookStock 
            FROM Books b 
            JOIN Authors a ON b.AuthorId = a.id 
            WHERE b.Title LIKE ? AND a.Name LIKE ?
            LIMIT 25";



        $stmt = $conn->prepare($sql);

        // Bind parameters
        $title = "%" . $title . "%";
        $author = "%" . $author . "%";
        $stmt->bind_param("ss", $title, $author);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $records = array();

            while ($row = $result->fetch_assoc()) {
                array_push($records, array(
                    'bookId' => $row["BookId"],
                    'bookTitle' => $row["BookTitle"],
                    'bookAuthor' => $row["BookAuthor"],
                    'bookStock' => $row["BookStock"],
                ));
            }

            $stmt->close();
            $conn->close();
            return json_encode($records);
        } else {
            $stmt->close();
            $conn->close();
            return "0";
        }
    }

    // Function to get all authors
    function getAuthors($name)
    {
        global $servername, $username, $password, $database;
        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT id, Name FROM Authors WHERE Name LIKE ?";
        $stmt = $conn->prepare($sql);

        $name = "%" . $name . "%";

        $stmt->bind_param("s", $name);
        $stmt->execute();

        $result = $stmt->get_result();

        $authors = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($authors, array(
                    'authorId' => $row["id"],
                    'authorName' => $row["Name"],
                ));
            }

            $stmt->close();
            $conn->close();
            return json_encode($authors);
        } else {
            $stmt->close();
            $conn->close();
            return "0";
        }
    }


    // Function to delete a book by ID
    function deleteBook($bookId)
    {
        global $servername, $username, $password, $database;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "DELETE FROM Books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookId);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }

    // Function to add a book
    function addBook($title, $authorId, $stock)
    {
        global $servername, $username, $password, $database;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Books (Title, AuthorId, Stock) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $title, $authorId, $stock);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }

    // Function to delete an author by ID
    function deleteAuthor($authorId)
    {
        global $servername, $username, $password, $database;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // First, delete books associated with the author
        $sqlDeleteBooks = "DELETE FROM Books WHERE AuthorId = ?";
        $stmtDeleteBooks = $conn->prepare($sqlDeleteBooks);
        $stmtDeleteBooks->bind_param("i", $authorId);
        $stmtDeleteBooks->execute();
        $stmtDeleteBooks->close();

        // Then, delete the author
        $sqlDeleteAuthor = "DELETE FROM Authors WHERE id = ?";
        $stmtDeleteAuthor = $conn->prepare($sqlDeleteAuthor);
        $stmtDeleteAuthor->bind_param("i", $authorId);
        $stmtDeleteAuthor->execute();

        $stmtDeleteAuthor->close();
        $conn->close();
    }

    // Function to add an author
    function addAuthor($name)
    {
        global $servername, $username, $password, $database;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO Authors (Name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }
}
