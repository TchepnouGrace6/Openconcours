<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrôlement {{ $enrollement->numero_enrollement }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        .section { margin-bottom: 20px; }
        .section h2 { background-color: #f2f2f2; padding: 5px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>
    <h1>Confirmation d'Enrôlement</h1>

    <div class="section">
        <h2>Informations Générales</h2>
        <table>
            <tr><th>Numéro d'enrôlement</th><td>{{ $enrollement->numero_enrollement }}</td></tr>
            <tr><th>Numéro de reçu</th><td>{{ $enrollement->numero_recu }}</td></tr>
            <tr><th>Nom</th><td>{{ $enrollement->prenom }} {{ $enrollement->nom }}</td></tr>
            <tr><th>Date de naissance</th><td>{{ $enrollement->date_naissance }}</td></tr>
            <tr><th>Sexe</th><td>{{ $enrollement->sexe }}</td></tr>
            <tr><th>Téléphone</th><td>{{ $enrollement->telephone }}</td></tr>
            <tr><th>Adresse</th><td>{{ $enrollement->adresse }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Documents</h2>
        <ul>
            @foreach(json_decode($enrollement->documents) as $doc)
                <li>{{ $doc }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>Photo</h2>
        @if($enrollement->photo)
            <img src="{{ public_path('storage/' . $enrollement->photo) }}" style="width:150px;height:auto;">
        @endif
    </div>
</body>
</html>
