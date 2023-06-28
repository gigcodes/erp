<?php
/**
 * Text shown in error messaging. / Текст відображається в повідомленнях про помилку.
 */
return [

    // Permissions
    'permission' => 'Ви не маєте дозволу на доступ до цієї сторінки.',
    'permissionJson' => 'Ви не маєте дозволу виконувати заявлену дію.',

    // Auth
    'error_user_exists_different_creds' => 'Користувач з електронною адресою: електронна адреса вже існує, але з іншими обліковими даними.',
    'email_already_confirmed' => 'Електронна пошта вже підтверджена, спробуйте увійти.',
    'email_confirmation_invalid' => 'Цей токен підтвердження недійсний або вже був використаний, будь ласка, спробуйте знову зареєструватися.',
    'email_confirmation_expired' => 'Термін дії токена підтвердження минув, новий електронний лист підтвердження був відправлений.',
    'ldap_fail_anonymous' => 'LDAP-доступ невдалий, з використання анонімного зв\'язку',
    'ldap_fail_authed' => 'LDAP-доступ невдалий, використовуючи задані параметри dn та password',
    'ldap_extension_not_installed' => 'Розширення PHP LDAP не встановлено',
    'ldap_cannot_connect' => 'Неможливо підключитися до ldap-сервера, Помилка з\'єднання',
    'social_no_action_defined' => 'Жодних дій не визначено',
    'social_login_bad_response' => "Помилка, отримана під час входу з :socialAccount помилка : \n:error",
    'social_account_in_use' => 'Цей :socialAccount обліковий запис вже використовується, спробуйте ввійти з параметрами :socialAccount.',
    'social_account_email_in_use' => 'Електронна пошта :email вже використовується. Якщо у вас вже є обліковий запис, ви можете підключити свій обліковий запис :socialAccount з налаштувань вашого профілю.',
    'social_account_existing' => 'Цей :socialAccount вже додано до вашого профілю.',
    'social_account_already_used_existing' => 'Цей обліковий запис :socialAccount вже використовується іншим користувачем.',
    'social_account_not_used' => 'Цей обліковий запис :socialAccount account не пов\'язаний з жодним користувачем. Будь ласка, додайте його в налаштуваннях вашого профілю. ',
    'social_account_register_instructions' => 'Якщо у вас ще немає облікового запису, ви можете зареєструвати обліковий запис за допомогою параметра :socialAccount.',
    'social_driver_not_found' => 'Драйвер для СоціальноїМережі не знайдено',
    'social_driver_not_configured' => 'Ваші соціальні настройки :socialAccount не правильно налаштовані.',

    // System
    'path_not_writable' => 'Не вдається завантажити шлях до файлу :filePath. Переконайтеся, що він доступний для запису на сервер.',
    'cannot_get_image_from_url' => 'Неможливо отримати зображення з :url',
    'cannot_create_thumbs' => 'Сервер не може створювати ескізи. Будь ласка, перевірте, чи встановлено розширення GD PHP.',
    'server_upload_limit' => 'Сервер не дозволяє завантажувати файли такого розміру. Спробуйте менший розмір файлу.',
    'uploaded' => 'Сервер не дозволяє завантажувати файли такого розміру. Спробуйте менший розмір файлу.',
    'image_upload_error' => 'Виникла помилка під час завантаження зображення',
    'image_upload_type_error' => 'Тип завантаженого зображення недійсний',
    'file_upload_timeout' => 'Тайм-аут при завантаженні файлу',

    // Attachments
    'attachment_page_mismatch' => 'Невідповідність сторінки при оновленні вкладень',
    'attachment_not_found' => 'Вкладення не знайдено',

    // Pages
    'page_draft_autosave_fail' => 'Не вдалося зберегти чернетку. Перед збереженням цієї сторінки переконайтеся, що у вас є зв\'язок з сервером.',
    'page_custom_home_deletion' => 'Неможливо видалити сторінку, коли вона встановлена як домашня сторінка',

    // Entities
    'entity_not_found' => 'Об\'єкт не знайдено',
    'bookshelf_not_found' => 'Книжкова полиця не знайдена',
    'book_not_found' => 'Книга не знайдена',
    'page_not_found' => 'Сторінку не знайдено',
    'chapter_not_found' => 'Розділ не знайдено',
    'selected_book_not_found' => 'Вибрана книга не знайдена',
    'selected_book_chapter_not_found' => 'Вибрана книга або глава не знайдена',
    'guests_cannot_save_drafts' => 'Гості не можуть зберігати чернетки',

    // Users
    'users_cannot_delete_only_admin' => 'Ви не можете видалити єдиного адміністратора',
    'users_cannot_delete_guest' => 'Ви не можете видалити гостьового користувача',

    // Roles
    'role_cannot_be_edited' => 'Цю роль не можна редагувати',
    'role_system_cannot_be_deleted' => 'Ця роль є системною, і її не можна видалити',
    'role_registration_default_cannot_delete' => 'Цю роль не можна видалити, бо вона встановлена як роль реєстрації за умовчанням',
    'role_cannot_remove_only_admin' => 'Цей користувач є єдиним користувачем, призначеним для ролі адміністратора. Призначте роль адміністратора іншому користувачеві, перш ніж спробувати його видалити.',

    // Comments
    'comment_list' => 'Під час отримання коментарів сталася помилка.',
    'cannot_add_comment_to_draft' => 'Ви не можете додати коментарі до проекту.',
    'comment_add' => 'Під час додавання/оновлення коментарів сталася помилка.',
    'comment_delete' => 'Під час видалення коментаря сталася помилка.',
    'empty_comment' => 'Неможливо додати порожній коментар.',

    // Error pages
    '404_page_not_found' => 'Сторінку не знайдено',
    'sorry_page_not_found' => 'Вибачте, сторінку, яку ви шукали, не знайдено.',
    'return_home' => 'Повернутися на головну',
    'error_occurred' => 'Виникла помилка',
    'app_down' => ':appName зараз недоступний',
    'back_soon' => 'Він повернеться найближчим часом.',

];