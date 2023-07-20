## Разработать Bitrix компонент для активации сертификатов. Обернете компонент в самостоятельный модуль, с независимой от инфоблоков таблицей сущности.

### Техническое условие
```
- Компонент должен состоять из 3 шагов активации: 
1) Первый экран(шаг) содержит форму ввода номера сертификата, E-mail владельца и Bitrix капча.
2) Второй экран содержит поле ввода кода подтверждения активации и появляется после проверки входных данных с первого экрана. 
3) Страница активированного\заблокированного сертификата.

Желательно, чтобы каждый шаг имел собственный HTML шаблон. Шаблоны других шагов не должны попадать в DOM текущего шага.
```

### Описательная часть
```
1) Инфоблок (сущность в случае модуля) хранения сертификатов. Элемент = сертификат. 
Обязательные данные:
- Срок действия (если срок действия на дату активации истек, активация невозможна, сертификат блокируется).
- номер сертификата (произвольный набор цифр)
- E-mail владельца сертификата

2) Первый экран: После ввода формы и проверки данных генерируется 6-значный код подтверждения. Ниже нужно вывести технический шаблон письма, содержащий код подтверждения (#SECURITY_CODE#), плюсом будет ссылка на страницу активации сертификата (#CERTIFICATE_URL#). Фактическую отправку письма по указанному шаблону реализовывать не нужно.

3) Второй экран: Поле ввода кода подтверждения и валидации данных, активация завершается успехом с переходом на 3-тий экран.

4) Третий экран: Техническая информация об активном сертификате или заблокированном по дате действия.
```

### Ручная установка
* Создать папку `certificat.activation` в папке `/local/modules/` или `/bitrix/modules/`
* Скопировать файлы модуля в папку `certificat.activation`
* Установить модуль в CMS Bitrix `/bitrix/admin/partner_modules.php?lang=ru`

## Компонент для активации сертификата
```
<? $APPLICATION->IncludeComponent('local.certificat:certificat.activation',".default",
    [
        "CACHE_TYPE" => "N",
        "CACHE_GROUPS" => "N",
    ], false
); ?>
```