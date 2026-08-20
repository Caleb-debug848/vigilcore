<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de langue de validation
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne peut pas contenir plus de :max caractères.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Noms d'attributs personnalisés
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'adresse email',
        'password' => 'mot de passe',
        'name' => 'nom',
        'title' => 'titre',
        'severity' => 'sévérité',
        'status' => 'statut',
    ],

];
