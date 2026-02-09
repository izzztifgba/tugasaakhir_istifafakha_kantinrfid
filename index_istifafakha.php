<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Selamat Datang di Kantin RFID</h1>
    <h2>Silahkan scan kode RFID Anda</h2>

    <input type="text" id="rfid_input_istifafakha" placeholder="Scan RFID Anda">
    <button onclick="submitRFID()">Submit</button>
    <script>
        function submitRFID() {
            const rfid = document.getElementById('rfid_input_istifafakha').value;
            if (rfid) {
                alert('RFID ' + rfid + ' telah diterima!');
                // Di sini Anda bisa menambahkan logika untuk mengirim RFID ke server atau memprosesnya lebih lanjut
            } else {
                alert('Silahkan masukkan kode RFID terlebih dahulu.');
            }

        }
    </script>
</body>

</html>