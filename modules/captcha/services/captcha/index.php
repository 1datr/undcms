<?php
$letters = '0123456789ABCDEFGKIJKLMNOPQRSTUVWXYZ'; // алфавит

$caplen = 6; //длина текста
$width = 120; $height = 40; //ширина и высота картинки
$font = ".".xbrotherfileurl(__FILE__,'/comic.ttf');//шрифт текста
//$font = 'comic.ttf';//шрифт текста
//echo ">>$font";

$fontsize = 14;// размер текста

header('Content-type: image/png'); //тип возвращаемого содержимого (картинка в формате PNG)

$im = imagecreatetruecolor($width, $height); //создаёт новое изображение
imagesavealpha($im, true); //устанавливает прозрачность изображения
$bg = imagecolorallocatealpha($im, 0, 0, 0, 127); //идентификатор цвета для изображения
imagefill($im, 0, 0, $bg); //выполняет заливку цветом

putenv( 'GDFONTPATH=' . realpath('.') ); //проверяет путь до файла со шрифтами

$captcha = '';//обнуляем текст
for ($i = 0; $i < $caplen; $i++)
{
	$captcha .= $letters[ rand(0, strlen($letters)-1) ]; // дописываем случайный символ из алфавила
	$x = ($width - 20) / $caplen * $i + 10;//растояние между символами
	$x = rand($x, $x+4);//случайное смещение
	$y = $height - ( ($height - $fontsize) / 2 ); // координата Y
	$curcolor = imagecolorallocate( $im, rand(0, 100), rand(0, 100), rand(0, 100) );//цвет для текущей буквы
	$angle = rand(-25, 25);//случайный угол наклона
	imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]); //вывод текста
}

// открываем сессию для сохранения сгенерированного текста
session_start();
$capcha_id='capcha';
if($_GET['capid'])
	$capcha_id=$_GET['capid'];
$_SESSION[$capcha_id] = $captcha;

//var_dump($_SESSION);

imagepng($im); //выводим изображение
imagedestroy($im);//отчищаем память

?>