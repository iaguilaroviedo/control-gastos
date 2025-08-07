<?php
// views/layout.php
function renderLayout($title, $content, $activePage = '') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isAuthPage = in_array($activePage, ['login.php', 'register.php', 'logout.php']);

    $menuItems = [];
    $authItems = [];

    if (isset($_SESSION['user_id']) && !$isAuthPage) {
        $menuItems = [
            ['href' => 'index.php', 'icon' => 'home', 'label' => 'Dashboard'],
            ['href' => 'deudas.php', 'icon' => 'credit-card', 'label' => 'Deudas']
        ];
        $authItems = [
            ['href' => 'logout.php', 'icon' => 'sign-out-alt', 'label' => 'Cerrar Sesión']
        ];
    } elseif (!$isAuthPage) {
        $authItems = [
            ['href' => 'login.php', 'icon' => 'sign-in-alt', 'label' => 'Iniciar Sesión'],
            ['href' => 'register.php', 'icon' => 'user-plus', 'label' => 'Registrarse']
        ];
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> - Control de Gastos</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            body { font-family: 'Roboto', sans-serif; background-color: #ffffff; margin: 0; padding: 0; }
            .container { max-width: 1200px; margin: 60px auto; padding: 0 15px; }
            <?php if (!$isAuthPage): ?>
            .hamburger {
                position: fixed; top: 20px; left: 20px; background-color: #2E7D32; color: white;
                width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center;
                justify-content: center; font-size: 24px; cursor: pointer; z-index: 1000;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.3s ease;
            }
            .hamburger:hover { transform: scale(1.1); }
            .menu-popup { position: fixed; top: 0; left: -300px; width: 280px; height: 100%;
                background-color: white; box-shadow: 5px 0 15px rgba(0,0,0,0.1); z-index: 1001;
                transition: left 0.4s ease; padding: 60px 20px 20px; overflow-y: auto; }
            .menu-popup.active { left: 0; }
            .menu-content h3 { margin-bottom: 20px; color: #2E7D32; text-align: center; }
            .menu-content ul { list-style: none; padding: 0; }
            .menu-content ul li { margin: 15px 0; }
            .menu-content ul li a { color: #333; text-decoration: none; font-size: 18px; display: flex;
                align-items: center; gap: 10px; padding: 10px; border-radius: 8px;
                transition: background-color 0.3s; }
            .menu-content ul li a:hover, .menu-content ul li a.active { background-color: #e8f5e9; color: #2E7D32; font-weight: 500; }
            .menu-content ul li a i { width: 24px; text-align: center; }
            .close-btn { position: absolute; top: 20px; right: 20px; font-size: 30px; color: #555; cursor: pointer; }
            .menu-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.5); z-index: 999; opacity: 0; visibility: hidden;
                transition: all 0.4s ease; }
            .menu-overlay.active { opacity: 1; visibility: visible; }
            .plant-left, .plant-right { position: absolute; z-index: -1; }
            .plant-left { left: 0; bottom: 0; width: 200px; margin-bottom: 100px; }
            .plant-right { right: 0; bottom: 0; width: 200px; margin-bottom: 100px; }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <?php if (!$isAuthPage): ?>
        <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
        <div class="menu-popup" id="menuPopup">
            <div class="menu-content">
                <span class="close-btn" id="closeBtn">&times;</span>
                <h3>Menú</h3>
                <ul>
                    <?php foreach ($menuItems as $item): ?>
                        <li><a href="<?= $item['href'] ?>" class="<?= $activePage === $item['href'] ? 'active' : '' ?>">
                            <i class="fas fa-<?= $item['icon'] ?>"></i> <?= $item['label'] ?>
                        </a></li>
                    <?php endforeach; ?>
                    <?php if (!empty($menuItems) && !empty($authItems)): ?><hr><?php endif; ?>
                    <?php foreach ($authItems as $item): ?>
                        <li><a href="<?= $item['href'] ?>"><i class="fas fa-<?= $item['icon'] ?>"></i> <?= $item['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="menu-overlay" id="menuOverlay"></div>
        <img src="assets/images/plant-decorative-left.jpg" alt="Planta" class="plant-left">
        <img src="assets/images/plant-decorative-right.jpg" alt="Planta" class="plant-right">
        <?php endif; ?>

        <div class="container"><?= $content ?></div>

        <?php if (!$isAuthPage): ?>
        <script>
            const hamburger = document.getElementById('hamburger');
            const menuPopup = document.getElementById('menuPopup');
            const menuOverlay = document.getElementById('menuOverlay');
            const closeBtn = document.getElementById('closeBtn');
            hamburger.addEventListener('click', () => {
                menuPopup.classList.add('active');
                menuOverlay.classList.add('active');
            });
            closeBtn.addEventListener('click', () => {
                menuPopup.classList.remove('active');
                menuOverlay.classList.remove('active');
            });
            menuOverlay.addEventListener('click', () => {
                menuPopup.classList.remove('active');
                menuOverlay.classList.remove('active');
            });
        </script>
        <?php endif; ?>
    </body>
    </html>
    <?php
}
?>