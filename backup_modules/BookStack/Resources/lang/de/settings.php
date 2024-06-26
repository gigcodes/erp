<?php

return [
    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */
    'settings'              => 'Einstellungen',
    'settings_save'         => 'Einstellungen speichern',
    'settings_save_success' => 'Einstellungen gespeichert',
    /**
     * App settings
     */
    'app_customization'            => 'Personalisierung',
    'app_features_security'        => 'Funktionen & Sicherheit',
    'app_name'                     => 'Anwendungsname',
    'app_name_desc'                => 'Dieser Name wird im Header und in E-Mails angezeigt.',
    'app_name_header'              => 'Anwendungsname im Header anzeigen?',
    'app_public_access'            => 'Öffentlicher Zugriff',
    'app_public_access_desc'       => 'Wenn Sie diese Option aktivieren, können Besucher, die nicht angemeldet sind, auf Inhalte in Ihrer BookStack-Instanz zugreifen.',
    'app_public_access_desc_guest' => 'Der Zugang für öffentliche Besucher kann über den Benutzer "Guest" gesteuert werden.',
    'app_public_access_toggle'     => 'Öffentlichen Zugriff erlauben',

    'app_public_viewing'          => 'Öffentliche Ansicht erlauben?',
    'app_secure_images'           => 'Erhöhte Sicherheit für hochgeladene Bilder aktivieren?',
    'app_secure_images_desc'      => 'Aus Leistungsgründen sind alle Bilder öffentlich sichtbar. Diese Option fügt zufällige, schwer zu eratene, Zeichenketten zu Bild-URLs hinzu. Stellen sie sicher, dass Verzeichnisindizes deaktiviert sind, um einen einfachen Zugriff zu verhindern.',
    'app_editor'                  => 'Seiteneditor',
    'app_editor_desc'             => 'Wählen Sie den Editor aus, der von allen Benutzern genutzt werden soll, um Seiten zu editieren.',
    'app_custom_html'             => 'Benutzerdefinierter HTML <head> Inhalt',
    'app_custom_html_desc'        => 'Jeder Inhalt, der hier hinzugefügt wird, wird am Ende der <head> Sektion jeder Seite eingefügt. Diese kann praktisch sein, um CSS Styles anzupassen oder Analytics-Code hinzuzufügen.',
    'app_logo'                    => 'Anwendungslogo',
    'app_logo_desc'               => "Dieses Bild sollte 43px hoch sein.\nGrößere Bilder werden verkleinert.",
    'app_primary_color'           => 'Primäre Anwendungsfarbe',
    'app_primary_color_desc'      => "Dies sollte ein HEX Wert sein.\nWenn Sie nicht eingeben, wird die Anwendung auf die Standardfarbe zurückgesetzt.",
    'app_homepage'                => 'Startseite der Anwendung',
    'app_homepage_desc'           => 'Wählen Sie eine Seite als Startseite aus, die statt der Standardansicht angezeigt werden soll. Seitenberechtigungen werden für die ausgewählten Seiten ignoriert.',
    'app_homepage_select'         => 'Wählen Sie eine Seite aus',
    'app_disable_comments'        => 'Kommentare deaktivieren',
    'app_disable_comments_toggle' => 'Kommentare deaktivieren',
    'app_disable_comments_desc'   => 'Deaktiviert Kommentare über alle Seiten in der Anwendung. Vorhandene Kommentare werden nicht angezeigt.',
    /**
     * Registration settings
     */
    'reg_settings'                            => 'Registrierungseinstellungen',
    'reg_enable'                              => 'Registrierung erlauben?',
    'reg_enable_toggle'                       => 'Registrierung erlauben',
    'reg_enable_desc'                         => 'Wenn die Registrierung erlaubt ist, kann sich der Benutzer als Anwendungsbenutzer anmelden. Bei der Registrierung erhält er eine einzige, voreingestellte Benutzerrolle.',
    'reg_default_role'                        => 'Standard-Benutzerrolle nach Registrierung',
    'reg_email_confirmation'                  => 'Bestätigung per E-Mail',
    'reg_email_confirmation_toggle'           => 'Bestätigung per E-Mail erforderlich',
    'reg_confirm_email_desc'                  => 'Falls die Einschränkung für Domains genutzt wird, ist die Bestätigung per E-Mail zwingend erforderlich und der untenstehende Wert wird ignoriert.',
    'reg_confirm_restrict_domain'             => 'Registrierung auf bestimmte Domains einschränken',
    'reg_confirm_restrict_domain_desc'        => "Fügen sie eine durch Komma getrennte Liste von Domains hinzu, auf die die Registrierung eingeschränkt werden soll. Benutzern wird eine E-Mail gesendet, um ihre E-Mail Adresse zu bestätigen, bevor sie diese Anwendung nutzen können.\nHinweis: Benutzer können ihre E-Mail Adresse nach erfolgreicher Registrierung ändern.",
    'reg_confirm_restrict_domain_placeholder' => 'Keine Einschränkung gesetzt',

    /**
     * Maintenance settings
     */
    'maint'                                => 'Wartung',
    'maint_image_cleanup'                  => 'Bilder bereinigen',
    'maint_image_cleanup_desc'             => 'Überprüft Seiten- und Versionsinhalte auf ungenutzte und mehrfach vorhandene Bilder. Erstellen Sie vor dem Start ein Backup Ihrer Datenbank und Bilder.',
    'maint_image_cleanup_ignore_revisions' => 'Bilder in Versionen ignorieren',
    'maint_image_cleanup_run'              => 'Reinigung starten',
    'maint_image_cleanup_warning'          => ':count eventuell unbenutze Bilder wurden gefunden. Möchten Sie diese Bilder löschen?',
    'maint_image_cleanup_success'          => ':count eventuell unbenutze Bilder wurden gefunden und gelöscht.',
    'maint_image_cleanup_nothing_found'    => 'Keine unbenutzen Bilder gefunden. Nichts zu löschen!',

    /**
     * Role settings
     */
    'roles'                              => 'Rollen',
    'role_user_roles'                    => 'Benutzer-Rollen',
    'role_create'                        => 'Neue Rolle anlegen',
    'role_create_success'                => 'Rolle erfolgreich angelegt',
    'role_delete'                        => 'Rolle löschen',
    'role_delete_confirm'                => 'Sie möchten die Rolle ":roleName" löschen.',
    'role_delete_users_assigned'         => 'Diese Rolle ist :userCount Benutzern zugeordnet. Sie können unten eine neue Rolle auswählen, die Sie diesen Benutzern zuordnen möchten.',
    'role_delete_no_migration'           => 'Den Benutzern keine andere Rolle zuordnen',
    'role_delete_sure'                   => 'Sind Sie sicher, dass Sie diese Rolle löschen möchten?',
    'role_delete_success'                => 'Rolle erfolgreich gelöscht',
    'role_edit'                          => 'Rolle bearbeiten',
    'role_details'                       => 'Rollendetails',
    'role_name'                          => 'Rollenname',
    'role_desc'                          => 'Kurzbeschreibung der Rolle',
    'role_external_auth_id'              => 'Externe Authentifizierungs-IDs',
    'role_system'                        => 'System-Berechtigungen',
    'role_manage_users'                  => 'Benutzer verwalten',
    'role_manage_roles'                  => 'Rollen und Rollen-Berechtigungen verwalten',
    'role_manage_entity_permissions'     => 'Alle Buch-, Kapitel- und Seiten-Berechtigungen verwalten',
    'role_manage_own_entity_permissions' => 'Nur Berechtigungen eigener Bücher, Kapitel und Seiten verwalten',
    'role_manage_settings'               => 'Globaleinstellungen verwalten',
    'role_asset'                         => 'Berechtigungen',
    'role_asset_desc'                    => 'Diese Berechtigungen gelten für den Standard-Zugriff innerhalb des Systems. Berechtigungen für Bücher, Kapitel und Seiten überschreiben diese Berechtigungenen.',
    'role_asset_admins'                  => 'Administratoren erhalten automatisch Zugriff auf alle Inhalte, aber diese Optionen können Oberflächenoptionen ein- oder ausblenden.',
    'role_all'                           => 'Alle',
    'role_own'                           => 'Eigene',
    'role_controlled_by_asset'           => 'Berechtigungen werden vom Uploadziel bestimmt',
    'role_save'                          => 'Rolle speichern',
    'role_update_success'                => 'Rolle erfolgreich gespeichert',
    'role_users'                         => 'Dieser Rolle zugeordnete Benutzer',
    'role_users_none'                    => 'Bisher sind dieser Rolle keine Benutzer zugeordnet',
    /**
     * Users
     */
    'users'                         => 'Benutzer',
    'user_profile'                  => 'Benutzerprofil',
    'users_add_new'                 => 'Benutzer hinzufügen',
    'users_search'                  => 'Benutzer suchen',
    'users_details'                 => 'Benutzerdetails',
    'users_details_desc'            => 'Legen Sie für diesen Benutzer einen Anzeigenamen und eine E-Mail-Adresse fest. Die E-Mail-Adresse wird bei der Anmeldung verwendet.',
    'users_details_desc_no_email'   => 'Legen Sie für diesen Benutzer einen Anzeigenamen fest, damit andere ihn erkennen können.',
    'users_role'                    => 'Benutzerrollen',
    'users_role_desc'               => 'Wählen Sie aus, welchen Rollen dieser Benutzer zugeordnet werden soll. Wenn ein Benutzer mehreren Rollen zugeordnet ist, werden die Berechtigungen dieser Rollen gestapelt und er erhält alle Fähigkeiten der zugewiesenen Rollen.',
    'users_password'                => 'Benutzerpasswort',
    'users_password_desc'           => 'Legen Sie ein Passwort fest, mit dem Sie sich anmelden möchten. Diese muss mindestens 5 Zeichen lang sein.',
    'users_external_auth_id'        => 'Externe Authentifizierungs-ID',
    'users_external_auth_id_desc'   => 'Dies ist die ID, die verwendet wird, um diesen Benutzer bei der Kommunikation mit Ihrem LDAP-System abzugleichen.',
    'users_password_warning'        => 'Füllen Sie die folgenden Felder nur aus, wenn Sie Ihr Passwort ändern möchten:',
    'users_system_public'           => 'Dieser Benutzer repräsentiert alle unangemeldeten Benutzer, die diese Seite betrachten. Er kann nicht zum Anmelden benutzt werden, sondern wird automatisch zugeordnet.',
    'users_delete'                  => 'Benutzer löschen',
    'users_delete_named'            => 'Benutzer ":userName" löschen',
    'users_delete_warning'          => 'Der Benutzer ":userName" wird aus dem System gelöscht.',
    'users_delete_confirm'          => 'Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?',
    'users_delete_success'          => 'Benutzer erfolgreich gelöscht.',
    'users_edit'                    => 'Benutzer bearbeiten',
    'users_edit_profile'            => 'Profil bearbeiten',
    'users_edit_success'            => 'Benutzer erfolgreich aktualisisert',
    'users_avatar'                  => 'Benutzer-Bild',
    'users_avatar_desc'             => 'Das Bild sollte eine Auflösung von 256x256px haben.',
    'users_preferred_language'      => 'Bevorzugte Sprache',
    'users_preferred_language_desc' => 'Diese Option ändert die Sprache, die für die Benutzeroberfläche der Anwendung verwendet wird. Dies hat keinen Einfluss auf von Benutzern erstellte Inhalte.',
    'users_social_accounts'         => 'Social-Media Konten',
    'users_social_accounts_info'    => 'Hier können Sie andere Social-Media-Konten für eine schnellere und einfachere Anmeldung verknüpfen. Wenn Sie ein Social-Media Konto lösen, bleibt der Zugriff erhalten. Entfernen Sie in diesem Falle die Berechtigung in Ihren Profil-Einstellungen des verknüpften Social-Media-Kontos.',
    'users_social_connect'          => 'Social-Media-Konto verknüpfen',
    'users_social_disconnect'       => 'Social-Media-Konto lösen',
    'users_social_connected'        => ':socialAccount-Konto wurde erfolgreich mit dem Profil verknüpft.',
    'users_social_disconnected'     => ':socialAccount-Konto wurde erfolgreich vom Profil gelöst.',
];
