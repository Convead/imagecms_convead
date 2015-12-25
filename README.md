Требования к CMS
----------------

* Поддерживается ImageCMS Shop версии 4.8.x.

Установка модуля в магазин
--------------------------

1. [Скачайте архив модуля](https://github.com/Convead/imagecms_convead/archive/master.zip) из репозитория.
2. Распакуйте архив и загрузите его содержимое через FTP на ваш сайт в папку `application/modules/convead`. 
3. Перейдите в панель администратора вашего магазина, раздел «Модули» → «Все модули».
4. Перейдите на вкладку «Установить модули», найдите в списке модуль «Convead» и нажмите на ссылку «Установка».
5. После успешной установки модуля в разделе «Все модули» активируйте переключатель «Автозагрузка» для модуля «Convead».
6. Нажмите на название модуля «Convead» и перейдите в его настройки. Установите галочку «Enable module» и впишите в поле «Convead App Key» ключ вашего аккаунта Convead. Сохраните изменения.
7. Очистите кеш CMS (меню «Настройки» → «Очистить кеш»).

ImageCMS hack
-------------

Для корректной работы текущей версии плагина (0.1) нужно внести несколько изменений в файлы ImageCMS:

1) В файле `application/modules/CMSFactory/BaseEvents.php` добавить новые триггеры:

**Перед функцией `onAddToCart`** добавить:

```php
public function onSetQuantity() {
  $this->key = 'Cart:setQuantity';
  return $this;
}

public function onEmptyCart() {
  $this->key = 'SCart:emptyCart';
  return $this;
}
```

2) В файле `application/modules/shop/classes/Cart/BaseCart.php` добавить вызовы триггеров:

**В функции `setQuantity`**:

После строки `$data = array('success' => true, 'setquan' => true);` добавить:

```php
\CMSFactory\Events::create()->registerEvent($data, 'Cart:setQuantity');
\CMSFactory\Events::runFactory();
```

**В функции `removeItem`**:

После строки `$data = array('success' => true, 'delete' => true);` добавить:

```php
\CMSFactory\Events::create()->registerEvent($data, 'SCart:removeOne');
\CMSFactory\Events::runFactory();
```

**В функции `removeAll`**:

После строки `$data = array('success' => true, 'delete' => true);` добавить:

```php
\CMSFactory\Events::create()->registerEvent($data, 'SCart:emptyCart');
\CMSFactory\Events::runFactory();
```

TODO
----

1. Выяснить, как можно обойтись без хака файлов CMS. Возможно стоит отправить pull request в репозиторий ImageCMS?
2. Все эвенты работы с корзиной отсылаются по два раза с абсолютно одинаковыми параметрами. С т.з. Convead это ни на что не влияет, просто не очень красиво выглядит в таймлайне визитора.
3. При оформлении заказа событие заказа отсылается аж три раза с абсолютно одинаковыми параметрами. С т.з. Convead это ни на что не влияет, т.к. есть ограничения на уникальный номер заказа и после финализации визита будет оставлен только один эвент.
4. Вопрос по форуме сообщества так и остался не отвеченным: http://forum.imagecms.net/viewtopic.php?pid=23289
5. Добавить файлы переводов в `language`.
