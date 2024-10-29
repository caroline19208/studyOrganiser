<?php
include_once("connection.php");

try {
    // Disable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Drop existing tables if they exist
    $stmt = $conn->prepare("DROP TABLE IF EXISTS STUDENT_DOES_PAST_PAPER, LINKS, ASSIGNMENT, LEARNING_OBJECTIVE, TOPIC, STUDENT_DOES_SUBJECT, STUDENT_HAS_REWARD, REWARDS, PAST_PAPER, SUBJECT, STUDENT");
    $stmt->execute();
    $stmt->closeCursor();

    // Re-enable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Drop existing tables if they exist, starting from child tables to parent tables
    $stmt = $conn->prepare("DROP TABLE IF EXISTS LINKS, ASSIGNMENT, LEARNING_OBJECTIVE, STUDENT_DOES_PAST_PAPER, TOPIC, STUDENT_DOES_SUBJECT, STUDENT_HAS_REWARD, REWARDS, PAST_PAPER, SUBJECT, STUDENT");
    $stmt->execute();
    $stmt->closeCursor();

    // Create STUDENT table
    $stmt = $conn->prepare("
    CREATE TABLE STUDENT (
        studentID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL UNIQUE,
        password VARCHAR(70) NOT NULL,
        totalBalance INT DEFAULT 0,
        diamondBalance INT DEFAULT 0
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create SUBJECT table
    $stmt = $conn->prepare("
    CREATE TABLE SUBJECT (
        subjectID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        studentID INT(6) UNSIGNED,
        subjectName VARCHAR(50) NOT NULL,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create STUDENT_DOES_SUBJECT table
    $stmt = $conn->prepare("
    CREATE TABLE STUDENT_DOES_SUBJECT (
        studentSubjectID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        studentID INT(6) UNSIGNED,
        subjectID INT(6) UNSIGNED,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE,
        FOREIGN KEY (subjectID) REFERENCES SUBJECT(subjectID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create REWARDS table
    $stmt = $conn->prepare("
    CREATE TABLE REWARDS (
        rewardID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        rewardDescription VARCHAR(100) NOT NULL,
        rewardCost INT NOT NULL
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create STUDENT_HAS_REWARD table
    $stmt = $conn->prepare("
    CREATE TABLE STUDENT_HAS_REWARD (
        studentRewardID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        studentID INT(6) UNSIGNED,
        rewardID INT(6) UNSIGNED,
        purchaseDate DATE NOT NULL,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE,
        FOREIGN KEY (rewardID) REFERENCES REWARDS(rewardID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create PAST_PAPER table
    $stmt = $conn->prepare("
    CREATE TABLE PAST_PAPER (
        pastPaperID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create STUDENT_DOES_PAST_PAPER table
    $stmt = $conn->prepare("
    CREATE TABLE STUDENT_DOES_PAST_PAPER (
        studentPastPaperID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        studentID INT(6) UNSIGNED,
        pastPaperID INT(6) UNSIGNED,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE,
        FOREIGN KEY (pastPaperID) REFERENCES PAST_PAPER(pastPaperID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create TOPIC table
    $stmt = $conn->prepare("
    CREATE TABLE TOPIC (
        topicID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        subjectID INT(6) UNSIGNED,
        studentID INT(6) UNSIGNED, 
        topicName VARCHAR(50) NOT NULL,
        topicOrder INT(3) NOT NULL,
        FOREIGN KEY (subjectID) REFERENCES SUBJECT(subjectID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create LEARNING_OBJECTIVE table
    $stmt = $conn->prepare("
    CREATE TABLE LEARNING_OBJECTIVE (
        objectiveID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        topicID INT(6) UNSIGNED NOT NULL,
        studentID INT(6) UNSIGNED NOT NULL,
        objectiveName VARCHAR(100) NOT NULL,
        notes TEXT,
        image BLOB,
        objectiveStatus VARCHAR(20) NOT NULL,
        FOREIGN KEY (topicID) REFERENCES TOPIC(topicID) ON DELETE CASCADE,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    // Create ASSIGNMENT table
    $stmt = $conn->prepare("
    CREATE TABLE ASSIGNMENT (
        assignmentID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        objectiveID INT(6) UNSIGNED,
        studentID INT(6) UNSIGNED,
        reviewStatus ENUM('Not started', 'Confused', 'Developing', 'Confident', 'Exam-ready', 'Retired') NOT NULL DEFAULT 'Not started',
        overdue BOOLEAN DEFAULT FALSE,
        dueDate DATE NOT NULL,
        coinsEarned INT DEFAULT 0,
        FOREIGN KEY (studentID) REFERENCES STUDENT(studentID) ON DELETE CASCADE,
        FOREIGN KEY (objectiveID) REFERENCES LEARNING_OBJECTIVE(objectiveID) ON DELETE CASCADE
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
        FOREIGN KEY (objectiveID) REFERENCES LEARNING_OBJECTIVE(objectiveID) ON DELETE CASCADE
    )");
    $stmt->execute();
    $stmt->closeCursor();

    echo "Tables created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
