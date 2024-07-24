<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificato</title>
    <style>
        body {
            font-family: 'cursive-font', cursive;
            background-image: url('https://img.freepik.com/free-vector/blue-certificate-background-gold-modern-border-vector_53876-156358.jpg?w=1380&t=1705669869'); /* Corretto il link all'immagine di sfondo */
            background-size: cover;
        }
        .container {
            text-align: center;
            padding: 50px;
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .signature {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        /* Aggiunto stile per rendere il testo h2 in corsivo */
        h2 {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://videocorsi.tech/assets/images/logoIcon/logo.png" alt="Logo" class="logo" width="100">
        <h1>Congratulazioni!</h1>
        <p>Questo certifica che</p>
        <h2>{{ $user->firstname }} {{ $user->lastname }}</h2>
        <p>ha completato con successo il corso</p>
        <h2>{{ $certification->course_name }}</h2>
        <p>rilasciato il</p>
        <h3>{{ $certification->completion_date }}</h3>
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/Firma_Josep_Irla.png/800px-Firma_Josep_Irla.png" alt="Firma" class="signature" width="150">
    </div>
</body>
</html>

