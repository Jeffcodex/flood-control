<?php
// create_products.php - RUN THIS ONCE TO CREATE SAMPLE PRODUCTS
include 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Sample Products - ToyRex Corner</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .product { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🛍️ Creating Sample Products for ToyRex Corner</h1>";

try {
    // SAMPLE GUNDAM PRODUCTS
    $products = [
        [
            'name' => 'RX-93 Nu Gundam',
            'description' => 'Master Grade Ver.Ka with psycho-frame and fin funnel system. Includes detailed waterslide decals.',
            'price' => 4500.00,
            'quantity' => 3,
            'category' => 'Gundam',
            'image' => 'RX-93.png'
        ],
        [
            'name' => 'OZ-13MS Gundam Epyon',
            'description' => 'Perfect Grade transformation system. Includes heat rod and beam sword weapons.',
            'price' => 4203.00,
            'quantity' => 2,
            'category' => 'Gundam',
            'image' => 'QZ-13.png'
        ],
        [
            'name' => 'Metal Robot Spirits Hi-ν Gundam',
            'description' => 'Metal build version with die-cast parts. Fin funnel system and special display base.',
            'price' => 3111.00,
            'quantity' => 5,
            'category' => 'Gundam',
            'image' => 'Hi-v.png'
        ],
        [
            'name' => 'Nendoroid Raiden Shogun',
            'description' => 'Genshin Impact Nendoroid with multiple face plates and accessories.',
            'price' => 3030.00,
            'quantity' => 8,
            'category' => 'Nendoroid',
            'image' => 'Raiden.png'
        ],
        [
            'name' => 'Nendoroid Robocosan',
            'description' => 'Hololive Production Nendoroid with cute robot design and accessories.',
            'price' => 2860.00,
            'quantity' => 6,
            'category' => 'Nendoroid',
            'image' => 'Robocosan.png'
        ],
        [
            'name' => 'Nendoroid Hashirama Senju',
            'description' => 'Naruto Nendoroid with wood style jutsu accessories.',
            'price' => 2780.00,
            'quantity' => 4,
            'category' => 'Nendoroid',
            'image' => 'Hashirama.png'
        ],
        [
            'name' => 'Nendoroid Eren Yeager',
            'description' => 'Attack on Titan The Final Season Version with Titan form accessories.',
            'price' => 3280.00,
            'quantity' => 7,
            'category' => 'Nendoroid',
            'image' => 'Eren.png'
        ],
        [
            'name' => 'Nendoroid Loid Forger',
            'description' => 'Spy x Family Nendoroid with spy gadgets and Anya accessories.',
            'price' => 2860.00,
            'quantity' => 10,
            'category' => 'Nendoroid',
            'image' => 'Loid.png'
        ],
        [
            'name' => 'Sofvimates Chopper',
            'description' => 'One Piece Sofvimates Chopper Zou Version - super soft and huggable!',
            'price' => 750.00,
            'quantity' => 15,
            'category' => 'Plush',
            'image' => 'Chopper.png'
        ]
    ];

    $created_by = 1; // Admin user ID

    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, quantity, category, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $product['name'],
            $product['description'], 
            $product['price'],
            $product['quantity'],
            $product['category'],
            $product['image'],
            $created_by
        ]);
        echo "<div class='product success'>✅ {$product['name']} - ₱{$product['price']} - Stock: {$product['quantity']}</div>";
    }

    echo "<h3 class='success'>🎉 All products created successfully!</h3>";
    echo "<p><a href='products.php'>View Products Page</a> | <a href='index.php'>Go to Homepage</a></p>";

} catch(PDOException $e) {
    echo "<div class='error'>❌ Error creating products: " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";
?>