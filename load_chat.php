<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "vet_help";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

if (isset($_SESSION['user_id']) && isset($_POST['doctorId'])) {
    $userId = $_SESSION['user_id'];
    $doctorId = $_POST['doctorId'];

    // Загрузка сообщений между пользователем и врачом
    $sql = "SELECT m.*, d.full_name AS doctor_name FROM messages m
            JOIN doctors d ON m.doctor_id = d.doctor_id
            WHERE (m.user_id = $userId AND m.doctor_id = $doctorId) OR (m.user_id = $doctorId AND m.doctor_id = $userId)
            ORDER BY m.created_at";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="message">';
            echo '<p>' . $row['message_text'] . '</p>';
            echo '<p>' . (($row['is_doctor_response'] == 1) ? 'От доктора: ' : 'Отправлено: ') . $row['created_at'] . ' от ' . (($row['is_doctor_response'] == 1) ? 'Доктор ' . $row['doctor_name'] : 'Пользователь') . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>Нет сообщений.</p>';
    }
} else {
    echo '<p>Неверные параметры запроса.</p>';
}

$conn->close();
?>