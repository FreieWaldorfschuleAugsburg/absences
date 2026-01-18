<?php

return [
    'name' => [
        'short' => 'Abwesenheiten',
        'full' => 'Abwesenheiten Waldorf Augsburg',
        'headline' => 'Abwesenheiten<br/>Waldorf Augsburg'
    ],
    'copyright' => '&copy; %s Freie Waldorfschule und Waldorfkindergärten Augsburg e. V.<br><small>Entwickelt von <a href="https://linusgke.de" target="_blank">Linus Groschke</a> <br/> <a href="https://github.com/FreieWaldorfschuleAugsburg/absences" target="_blank">Quellcode auf GitHub</a> – <a href="https://waldorf-augsburg.de/impressum" target="_blank">Impressum</a></small></small>',
    'error' => [
        'exception' => [
            'title' => 'Systemfehler!',
            'text' => 'Es ist ein Fehler aufgetreten. Bitte wenden Sie sich an den technischen Support!'
        ],
        '404' => [
            'title' => 'Nicht gefunden!',
            'text' => 'Die von Ihnen angeforderte Seite konnte nicht gefunden werden.'
        ],
        '400' => [
            'title' => 'Ungültige Anfrage!',
            'text' => 'Die von Ihnen gestellte Anfrage ist ungültig.'
        ],
        'oauth' => [
            'title' => 'Authentifizierungsfehler',
            'text' => 'Bei der Authentifizierung ist ein Fehler aufgetreten.',
            'noPermissions' => 'Sie sind nicht berechtigt dieses System zu verwenden!',
            'login' => "OIDC-Anmeldefehler",
            'refresh' => "OIDC-Auffrischungsfehler",
            'logout' => "OIDC-Abmeldefehler"
        ],
        'portalHyperlink' => 'Portal aufrufen',
        'logoutHyperlink' => 'Abmelden'
    ],
    'confirm' => 'Wollen Sie das wirklich tun?'
];