<?php
include_once("connection.php");

try {
    // Drop existing tables
    $stmt = $conn->prepare("DROP TABLE IF EXISTS STUDENT_DOES_PAST_PAPER, LINKS, ASSIGNMENT, LEARNING_OBJECTIVE, TOPIC, STUDENT_DOES_SUBJECT, STUDENT_HAS_REWARD, REWARDS, PAST_PAPER, SUBJECT, STUDENT");
    $stmt->execute();
    $stmt->closeCursor(); //close statement

    // Create STUDENT table
    $stmt = $conn->prepare("
    CREATE TABLE STUDENT (
        studentID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL,
        password VARCHAR(30) NOT NULL,
        totalBalance INT DEFAULT 0
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create SUBJECT table
    $stmt = $conn->prepare("
    CREATE TABLE SUBJECT (
        subjectID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        subjectName VARCHAR(50) NOT NULL
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create STUDENT_DOES_SUBJECT table
    $stmt = £conn->prepare("
    CREATE TABLE STUDENT_DOES_SUBJECT (
        studentSubjectID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        studentID INT(6) UNSIGNED,
        subjectID INT(6) UNSIGNED,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID),
        FOREIGN KEY (subjectID) REFERENCES SUBJECT(subjectID)
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create TOPIC table
    $stmt = $conn->prepare("
    CREATE TABLE TOPIC (
        topicID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        subjectID INT(6) UNSIGNED,
        topicName VARCHAR(50) NOT NULL,
        topicOrder INT(3) NOT NULL,
        FOREIGN KEY (subjectID) REFERENCES SUBJECT(subjectID)
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create LEARNING_OBJECTIVE table
    $stmt = $conn->prepare("
    CREATE TABLE LEARNING_OBJECTIVE (
        objectiveID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        topicID INT(6) UNSIGNED,
        objectiveName VARCHAR(100) NOT NULL,
        notes TEXT,
        image BLOB,
        objectiveStatus VARCHAR(10) NOT NULL,
        FOREIGN KEY (topicID) REFERENCES TOPIC(topicID)
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create ASSIGNMENT table
    $stmt = $conn->prepare("
    CREATE TABLE ASSIGNMENT (
        assignmentID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        objectiveID INT(6) UNSIGNED,
        reviewStatus ENUM('Not started', 'Confused', 'Developing', 'Confident', 'Exam-ready', 'Retired') NOT NULL DEFAULT 'Not started',
        overdue BOOLEAN DEFAULT FALSE,
        dueDate DATE NOT NULL,
        coinsEarned INT DEFAULT 0,
        FOREIGN KEY (objectiveID) REFERENCES LEARNING_OBJECTIVE(objectiveID)
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create LINKS table
    $stmt = $conn->prepare("
    CREATE TABLE LINKS (
        linkID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        objectiveID INT(6) UNSIGNED,
        linkURL VARCHAR(255) NOT NULL,
        linkDescription VARCHAR(255),
        linkType VARCHAR(20),
        FOREIGN KEY (objectiveID) REFERENCES LEARNING_OBJECTIVE(objectiveID)
    )");
    $stmt->execute();
    $stmt->closeCursor();

    echo "Tables created successfully!";
} catch (PDOException $e) {
    //Handle any errors that occur when tables are being created
    echo "Error: " . $e->getMessage();
}
?>
