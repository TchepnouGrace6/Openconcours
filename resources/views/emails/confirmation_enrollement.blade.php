<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation Enrôlement</title>
</head>
<body>
    <h2>Bonjour {{ $enrollement->prenom }} {{ $enrollement->nom }},</h2>

    <p>Votre candidature pour le concours <strong>{{ $enrollement->session->concours->nom ?? 'Concours' }}</strong> a été validée avec succès.</p>

    <h4>Détails de votre inscription :</h4>
    <ul>
        <li>Numéro de reçu : {{ $enrollement->numero_recu }}</li>
        <li>Numéro d'enrôlement : {{ $enrollement->numero_enrollement }}</li>
        <li>Session : {{ $enrollement->session->nom ?? 'Session' }}</li>
        <li>Filière : {{ $enrollement->session->concours->filiere->nom ?? 'Filière' }}</li>
       
        <li>Paiement : {{ $enrollement->paiement }}</li>
        <li>Date de naissance : {{ $enrollement->date_naissance }}</li>
        <li>salle : {{ $enrollement->salle_id }}</li>
        <li>Numero de table : {{ $enrollement->numero_table }}</li>
        <li>Niveau d'étude : {{ $enrollement->niveau_etude }}</li>
    </ul>

    <p>Merci de conserver cet email pour vos dossiers.</p>
    <p>Cordialement,<br>L’équipe du concours</p>
</body>
</html>
