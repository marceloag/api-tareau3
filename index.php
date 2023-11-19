<?php

// Definir el encabezado para indicar que se retornará JSON
header('Content-Type: application/json');

// Conexión a la base de datos (ajusta las credenciales según tu configuración)
$dbname = 'api';
$usuario = 'marceloag';
$pass = 'oblow1604';
$host = '0.0.0.0';
$port = 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$dbname";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $usuario, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Falló la conexión a la base de datos']);
    exit;
}

// Manejar las solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todos los libros
    $stmt = $pdo->query('SELECT * FROM libros');
    $libros = $stmt->fetchAll();
    echo json_encode($libros);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear un nuevo libro
    $data = json_decode(file_get_contents('php://input'), true);
    $titulo = $data['titulo'];
    $autor = $data['autor'];
    $isbn = $data['isbn'];

    if (empty($titulo) || empty($autor) || empty($isbn)) {
        echo json_encode(['error' => 'Faltan campos requeridos']);
        exit;
    }

    $sql = "INSERT INTO libros (titulo, autor, isbn) VALUES (:titulo, :autor, :isbn)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':isbn', $isbn);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Libro creado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al crear el libro']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Actualizar un libro
    $data = json_decode(file_get_contents('php://input'), true);
    $libroId = $data['id'];
    $titulo = $data['titulo'];
    $autor = $data['autor'];
    $isbn = $data['isbn'];

    if ( empty($titulo) || empty($autor  || empty($isbn))) {
        echo json_encode(['error' => 'Faltan campos requeridos']);
        exit;
    }

    $sql = "UPDATE libros SET titulo = :titulo, autor = :autor, isbn=:isbn WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $libroId);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':isbn', $isbn);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Libro actualizado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al actualizar el libro']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar un libro
    $data = json_decode(file_get_contents('php://input'), true);
    // $libroId = $data['id'];
    $libroId = $_GET['id'];

    if (empty($libroId)) {
        echo json_encode(['error' => 'Falta el ID del libro a eliminar']);
        exit;
    }

    $sql = "DELETE FROM libros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $libroId);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Libro eliminado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al eliminar el libro']);
    }
}
