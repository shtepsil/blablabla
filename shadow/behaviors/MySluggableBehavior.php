<?php
/**
 * Behavior for transliterate russian text to latin.
 *
 * Поведение для транслитерации кирилицы в латиницу. Я так считаю.
 * Использовать так же как и оригинальное поведение. Класс унаследован от \yii\behaviors\SluggableBehavior
 * просто переопределен метод generateSlug
 *
 * ```php
 * use zabachok\behaviors\SluggableBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => SluggableBehavior::className(),
 *             'attribute' => 'title',
 *             // 'slugAttribute' => 'slug',
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Daniil Romanov <zabachok@zabachok.net>
 */
namespace shadow\behaviors;


class MySluggableBehavior extends \yii\behaviors\SluggableBehavior
{

    public function translit($text)
    {
        $converter = array(
            'а'	=>'a', 'б'	=>'b', 'в'	=>'v',
            'г'	=>'g', 'д'	=>'d', 'е'	=>'e',
            'ё'	=>'e', 'ж'	=>'zh', 'з'	=>'z',
            'и'	=>'i', 'й'	=>'y', 'к'	=>'k',
            'л'	=>'l', 'м'	=>'m', 'н'	=>'n',
            'о'	=>'o', 'п'	=>'p', 'р'	=>'r',
            'с'	=>'s', 'т'	=>'t', 'у'	=>'u',
            'ф'	=>'f', 'х'	=>'h', 'ц'	=>'c',
            'ч'	=>'ch', 'ш'	=>'sh', 'щ'	=>'shch',
            'ь'	=>'', 'ы'	=>'y', 'ъ'	=>'',
            'э'	=>'e', 'ю'	=>'yu', 'я'	=>'ya',
            'А'	=>'A', 'Б'	=>'B', 'В'	=>'V',
            'Г'	=>'G', 'Д'	=>'D', 'Е'	=>'E',
            'Ё'	=>'E', 'Ж'	=>'Zh', 'З'	=>'Z',
            'И'	=>'I', 'Й'	=>'Y', 'К'	=>'K',
            'Л'	=>'L', 'М'	=>'M', 'Н'	=>'N',
            'О'	=>'O', 'П'	=>'P', 'Р'	=>'R',
            'С'	=>'S', 'Т'	=>'T', 'У'	=>'U',
            'Ф'	=>'F', 'Х'	=>'H', 'Ц'	=>'C',
            'Ч'	=>'Ch', 'Ш'	=>'Sh', 'Щ'	=>'Shch',
            'Ь'	=>'', 'Ы'	=>'Y', 'Ъ'	=>'',
            'Э'	=>'E', 'Ю'	=>'Yu', 'Я'	=>'Ya',
        );

        $text = str_replace("ый", "iy", $text);
        $text = str_replace("Ый", "iy", $text);
        $text = str_replace("ыЙ", "iy", $text);
        $text = str_replace("ЫЙ", "iy", $text);

        return strtr($text, $converter);
    }

    public function generateSlug($slugParts)
    {
        //Удаляем пробелы
        $len = 0;
        while(true)
        {
            $text = str_replace('  ', ' ', implode('-', $slugParts));
            $newlen = strlen($text);
            if($len == $newlen) break;
            $len = $newlen;
        }
        $text = trim($text);
        // переводим в транслит
        $text		 = self::translit($text);
        // в нижний регистр
        $text		 = strtolower($text);
        // пробел на тире
        $converter	 = array(' '=>'-');
        $text		 = strtr($text, $converter);
        // удаляем все лишнее
        $text		 = preg_replace('/[^a-z0-9_\-]/u', '', $text);

        $text = preg_replace("/-{2,}/", "-", $text);
        return $text;
    }
}