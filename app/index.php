<?php
require_once 'integration.php';
$startTime = date("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $price = $_POST['price'];

    $stopTime = date("Y-m-d H:i:s");

    $integration = new Integration($name, $email, $phone, $price, $startTime, $stopTime);

    $intersectionPoints = $integration->amoCRM_add_deal();
}
?>

<!DOCTYPE html>
<header>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</header>
<html>
    <body>
        <div class="container">
            <form method="post" action="">
                <div class="row">
                    <div class="col-6">
                        <div class="row mt-4">
                            <div class="col-12">
                                <label>Имя:</label>
                                <input type="text" name="name" required>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <label>Email:</label>
                                <input type="text" name="email" required>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <label>Телефон:</label>
                                <input type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <label>Цена:</label>
                                <input type="text" name="price" required>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="submit" class="btn btn-success">Отправить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
