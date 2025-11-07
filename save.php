<?php
// --- Configuration ---
$dbFile = realpath("collection.accdb");

// Check if file exists
if (!$dbFile) {
    die("Database file not found!");
}

// --- Connect to Access Database ---
$connStr = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbFile;";
try {
    $conn = new PDO($connStr);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// --- Get form data ---
$fname      = $_POST['fname'] ?? '';
$mname      = $_POST['mname'] ?? '';
$lname      = $_POST['lname'] ?? '';
$phone      = $_POST['phone'] ?? '';
$email      = $_POST['email'] ?? '';
$gender     = $_POST['gender'] ?? '';
$how        = $_POST['how'] ?? '';
$bornagain  = $_POST['bornagain'] ?? '';
$county     = $_POST['county'] ?? '';
$hometown   = $_POST['hometown'] ?? '';

// --- Handle file upload ---
$photoPath = "";
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $photoPath = $targetDir . basename($_FILES["photo"]["name"]);
    move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath);
}

// --- Insert into Access Table ---
try {
    $sql = "INSERT INTO Members 
        (FirstName, MiddleName, LastName, Phone, Email, Gender, HowKnown, BornAgain, County, HomeTown, Photo)
        VALUES 
        (:fname, :mname, :lname, :phone, :email, :gender, :how, :bornagain, :county, :hometown, :photo)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fname' => $fname,
        ':mname' => $mname,
        ':lname' => $lname,
        ':phone' => $phone,
        ':email' => $email,
        ':gender' => $gender,
        ':how' => $how,
        ':bornagain' => $bornagain,
        ':county' => $county,
        ':hometown' => $hometown,
        ':photo' => $photoPath
    ]);

    echo "<h3>✅ Member Registered Successfully!</h3>";
} catch (PDOException $e) {
    echo "❌ Error saving data: " . $e->getMessage();
}
?>

