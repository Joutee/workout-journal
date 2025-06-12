<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/inc/anonymUser.php'; // Potřebujeme databázové připojení

// Kontrola state parametru pro bezpečnost
if (!isset($_GET['state']) || !isset($_SESSION['google_state']) || $_GET['state'] !== $_SESSION['google_state']) {
    die('Neplatný state parametr.');
}

// Vymazání state z session
unset($_SESSION['google_state']);

$client = new Google_Client();
$client->setClientId('272107211808-87pu5hhcaah06otrmqv7upi8e2nqv839.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://eso.vse.cz/~kovp07/semestralka/googleCallback.php');

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        // Kontrola chyb při získání tokenu
        if (isset($token['error'])) {
            die('Chyba při získání tokenu: ' . $token['error_description']);
        }

        $client->setAccessToken($token);

        // Získání informací o uživateli
        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        // Uložení uživatele do databáze nebo přihlášení
        $email = $userInfo->email;
        $name = $userInfo->givenName;
        $surname = $userInfo->familyName;
        $googleId = $userInfo->id; // Google OAuth ID

        // Nejdříve zkontroluj, zda uživatel existuje podle Google ID
        $query = $db->prepare('SELECT * FROM user WHERE google_id = :google_id LIMIT 1;');
        $query->execute([':google_id' => $googleId]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Uživatel s tímto Google ID už existuje - přihlášení
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_full_name'] = [$user['name'], $user['surname']];
            $_SESSION['admin'] = $user['admin'] ?? 0;
        } else {
            // Zkontroluj, zda existuje uživatel se stejným emailem (bez Google ID)
            $emailQuery = $db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1;');
            $emailQuery->execute([':email' => $email]);
            $existingUser = $emailQuery->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                // Uživatel s emailem existuje, ale nemá Google ID - aktualizuj ho
                $updateQuery = $db->prepare('UPDATE user SET google_id = :google_id WHERE user_id = :user_id;');
                $updateQuery->execute([
                    ':google_id' => $googleId,
                    ':user_id' => $existingUser['user_id']
                ]);

                $_SESSION['user_id'] = $existingUser['user_id'];
                $_SESSION['user_full_name'] = [$existingUser['name'], $existingUser['surname']];
                $_SESSION['admin'] = $existingUser['admin'] ?? 0;
            } else {
                // Registrace nového uživatele s Google ID
                $insertQuery = $db->prepare('INSERT INTO user (name, surname, email, google_id, admin) VALUES (:name, :surname, :email, :google_id, 0);');
                $insertQuery->execute([
                    ':name' => $name,
                    ':surname' => $surname,
                    ':email' => $email,
                    ':google_id' => $googleId
                ]);
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['user_full_name'] = [$name, $surname];
                $_SESSION['admin'] = 0;
            }
        }

        // Přesměrování na hlavní stránku
        header('Location: index.php');
        exit();

    } catch (Exception $e) {
        die('Chyba při OAuth autentizaci: ' . $e->getMessage());
    }
} else {
    // Pokud není code parametr, přesměruj zpět na login
    header('Location: login.php');
    exit();
}
?>