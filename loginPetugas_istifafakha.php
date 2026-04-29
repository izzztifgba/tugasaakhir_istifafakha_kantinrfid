<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir Kantin | Emerald Edition</title>
    <style>
        /* Menggunakan Font Montserrat agar terlihat modern dan bersih */
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Background Gradient Emerald yang mewah */
            background: linear-gradient(135deg, #022e1a 0%, #065f37 50%, #50c878 100%);
            overflow: hidden;
            position: relative;
        }

        /* Elemen Dinamis: Lingkaran Latar Belakang yang Bergerak */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
            animation: move 20s infinite alternate ease-in-out;
        }

        body::before { top: -50px; left: -50px; }
        body::after { bottom: -50px; right: -50px; animation-delay: -10s; }

        @keyframes move {
            from { transform: translate(0, 0) rotate(0deg); }
            to { transform: translate(100px, 100px) rotate(360deg); }
        }

        /* Box Login dengan Efek Glassmorphism */
        .box {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: #065f37;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        p {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Styling Input Field */
        .input-group {
            position: relative;
        }

        input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: #f9f9f9;
            outline: none;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #50c878;
            background: #fff;
            box-shadow: 0 0 10px rgba(80, 200, 120, 0.2);
        }

        /* Tombol Login Dinamis */
        button {
            padding: 15px;
            border: none;
            border-radius: 12px;
            background: #065f37;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(6, 95, 55, 0.2);
        }

        button:hover {
            background: #50c878;
            transform: scale(1.02);
            box-shadow: 0 15px 25px rgba(80, 200, 120, 0.3);
        }

        button:active {
            transform: scale(0.98);
        }

        /* Link Tambahan */
        .footer-link {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }

        .footer-link a {
            color: #065f37;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="box">
        <h2>Kantin RFID</h2>
        <p>Silakan login untuk mengelola transaksi</p>

        <form action="proses_login_istifafakha.php" method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">MASUK</button>
        </form>

        <div class="footer-link">
            Lupa password? <a></a>Hubungi Admin</a>
        </div>
    </div>

</body>
</html>